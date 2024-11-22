<?php
include '../config/db.php';
require_once('C:/xampp/htdocs/venta_zapatos/libs/fpdf.php');

$error = '';

// Recibir los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_zapato = $_POST['codigo_zapato'];
    $fecha_venta = $_POST['fecha_venta'];
    $nombre_cliente = $_POST['nombre_cliente'];
    $cliente_email = $_POST['cliente_email'];

    // Busca el zapato en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM zapatos WHERE codigo = ?");
    $stmt->execute([$codigo_zapato]);
    $zapato = $stmt->fetch();

    if ($zapato) {
        $precio = $zapato['precio'];
        $tipo_pago = $_POST['tipo_pago'];

        // Inserta la venta principal
        $stmt_venta = $pdo->prepare("INSERT INTO ventas (fecha, cliente_nombre, cliente_email, tipo_pago, total, codigo_zapato, id_usuario) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt_venta->execute([
            $fecha_venta, $nombre_cliente, $cliente_email, $tipo_pago, $precio, $codigo_zapato, 1
        ]);

        $venta_id = $pdo->lastInsertId();

        // Lógica específica para el tipo de pago
        if ($tipo_pago == 'contado') {
            $pdf_ruta = generar_pdf($venta_id, $nombre_cliente, $precio, $zapato['marca'], $zapato['modelo']);
            echo "Venta a contado realizada con éxito. <a href='$pdf_ruta'>Descargar ticket</a>";
        } elseif ($tipo_pago == 'credito') {
            $numero_tarjeta = $_POST['numero_tarjeta'];
            $fecha_caducidad = $_POST['fecha_caducidad'];
            $cvv = $_POST['cvv'];

            $stmt_credito = $pdo->prepare("INSERT INTO detalles_credito (id_venta, numero_tarjeta, fecha_caducidad, cvv) 
                VALUES (?, ?, ?, ?)");
            $stmt_credito->execute([$venta_id, $numero_tarjeta, $fecha_caducidad, $cvv]);

            echo "Venta a crédito registrada correctamente.";
        }
    } else {
        $error = "Zapato no encontrado.";
    }
}

// Función para generar el PDF
function generar_pdf($venta_id, $nombre_cliente, $total, $marca, $modelo) {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(200, 10, 'TICKET DE VENTA', 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(100, 10, "Venta ID: $venta_id", 0, 1);
    $pdf->Cell(100, 10, "Cliente: $nombre_cliente", 0, 1);
    $pdf->Cell(100, 10, "Zapato: $marca - $modelo", 0, 1);
    $pdf->Cell(100, 10, "Total: $$total", 0, 1);
    $pdf->Ln(10);
    $pdf->Cell(100, 10, "Gracias por su compra", 0, 1);

    $ruta_carpeta = dirname(__DIR__) . 'C:/xampp/htdocs/venta_zapatos/tickets';
    if (!is_dir($ruta_carpeta)) {
        mkdir($ruta_carpeta, 0755, true);
    }

    $ruta_pdf = "$ruta_carpeta/ticket_venta_$venta_id.pdf";
    $pdf->Output('F', $ruta_pdf);

    return "../tickets/ticket_venta_$venta_id.pdf";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ZapaTecNM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../src/venta.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #007bff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .navbar h1 {
            font-size: 24px;
            margin: 0;
        }
        .navbar button {
            background-color: #0056b3;
            border: none;
            padding: 10px 15px;
            border-radius: 10px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .navbar button:hover {
            background-color: #003f88;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 400px;
            margin: 200px auto;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            color: #555;
        }
        input {
            margin-top: 5px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
        }
        button {
            margin-top: 20px;
            padding: 10px;
            border: none;
            border-radius: 10px;
            background-color: #007bff;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        #formulario_credito {
            display: none;
            margin-top: 20px;
        }
        .error {
            color: red;
            text-align: center;
        }
        #modalEscaner {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        #videoPreview {
            width: 100%;
            max-width: 500px;
            height: 300px;
            border: 2px solid white;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .modal-content button {
            margin-top: 10px;
            background-color: #f44336;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>ZapaTecNM</h1>
    <button onclick="location.href='proveedor.php'">Proveedores</button>
</div>

<div class="container">
    <h2>Gestión de Ventas</h2>
    <?php if ($error): ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="ventas.php">
        <label for="codigo_zapato">Código del zapato:</label>
        <input type="text" name="codigo_zapato" required>

        <label for="fecha_venta">Fecha de venta:</label>
        <input type="date" name="fecha_venta" required>

        <label for="nombre_cliente">Nombre del cliente:</label>
        <input type="text" name="nombre_cliente" required>

        <label for="cliente_email">Email del cliente:</label>
        <input type="email" name="cliente_email" required>

        <button type="submit" name="tipo_pago" value="contado">Venta a Contado</button>
        <button type="button" onclick="mostrarFormularioCredito()">Venta a Crédito</button>

        <div id="formulario_credito">
            <label for="numero_tarjeta">Número de tarjeta:</label>
            <input type="text" name="numero_tarjeta" required>

            <label for="fecha_caducidad">Fecha de caducidad:</label>
            <input type="month" name="fecha_caducidad" required>

            <label for="cvv">CVV:</label>
            <input type="text" name="cvv" required>

           
