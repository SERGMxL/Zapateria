<?php
session_start();
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zapato_id = $_POST['zapato_id'];
    $cliente_nombre = $_POST['cliente_nombre'];
    $fecha_venta = $_POST['fecha_venta'];
    $tipo_pago = $_POST['tipo_pago'];
    $user_id = $_SESSION['user_id'];

    // Insertar la venta en la tabla `ventas`
    $stmt = $pdo->prepare("INSERT INTO ventas (fecha, cliente_nombre, tipo_pago, total, id_usuario) VALUES (?, ?, ?, (SELECT precio FROM zapatos WHERE id = ?), ?)");
    $stmt->execute([$fecha_venta, $cliente_nombre, $tipo_pago, $zapato_id, $user_id]);
    $venta_id = $pdo->lastInsertId();

    if ($tipo_pago === 'credito') {
        $numero_tarjeta = $_POST['numero_tarjeta'];
        $fecha_caducidad = $_POST['fecha_caducidad'];
        $cvv = $_POST['cvv'];

        $stmt = $pdo->prepare("INSERT INTO detalles_credito (id_venta, numero_tarjeta, fecha_caducidad, cvv) VALUES (?, ?, ?, ?)");
        $stmt->execute([$venta_id, $numero_tarjeta, $fecha_caducidad, $cvv]);
    }

    // Generar PDF con la biblioteca FPDF
    require '../libs/fpdf.php';

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'ZapaTecNM');
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Nombre del Cliente: ' . $cliente_nombre);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Fecha de Venta: ' . $fecha_venta);
    $pdf->Ln();
    $pdf->Cell(40, 10, 'Total: ' . $zapato['precio']);
    $pdf->Ln();

    if ($tipo_pago === 'credito') {
        $pdf->Cell(40, 10, 'Pago a Crédito');
    } else {
        $pdf->Cell(40, 10, 'Pago de Contado');
    }

    $pdf->Output('D', 'ticket_venta.pdf');
    exit();
}
?>
