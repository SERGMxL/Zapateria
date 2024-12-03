<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "tienda_zapatos");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

if (!isset($_SESSION['cesta']) || empty($_SESSION['cesta'])) {
    echo "<script>alert('No hay productos en la cesta'); location.href='ventas.php';</script>";
    exit;
}

// Calcular total
$total = 0;
foreach ($_SESSION['cesta'] as $producto) {
    $cantidad = isset($producto['cantidad']) ? $producto['cantidad'] : 1;
    $total_producto = $producto['precio'] * $cantidad;
    $total += $total_producto;
}

// Procesar pago
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_pago = isset($_POST['pago_contado']) ? 'contado' : 'credito';
    $cliente_nombre = "ZapaTecNM";
    $cliente_email = "klausdepaepe@gmail.com";
    $id_usuario = 1;

    $fecha_venta = date('Y-m-d');
    $venta_id = rand(1000, 9999);

    foreach ($_SESSION['cesta'] as $item) {
        $codigo_zapato = $item['codigo'];
        $precio = $item['precio'];
        $cantidad = isset($item['cantidad']) ? $item['cantidad'] : 1;

        $stmt = $mysqli->prepare("
            INSERT INTO ventas (fecha, cliente_nombre, cliente_email, tipo_pago, total, id_usuario, codigo_zapato, precio, cantidad) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            'sssdsdsdd',
            $fecha_venta,
            $cliente_nombre,
            $cliente_email,
            $tipo_pago,
            $total,
            $id_usuario,
            $codigo_zapato,
            $precio,
            $cantidad
        );

        if (!$stmt->execute()) {
            echo "<script>alert('Error al registrar la venta: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }

    echo "<script>
        alert('Compra a $tipo_pago realizada. Redirigiendo a la impresión del ticket.');
        window.location.href = 'imprimir_ticket.php?venta_id=$venta_id';
    </script>";

    session_destroy();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cesta de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../img/venta.jpg');
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
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }
        .navbar h1 {
            font-size: 24px;
            margin: 0;
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
        }
        .nav-buttons button {
            background-color: #0056b3;
            border: none;
            padding: 10px 15px;
            border-radius: 10px;
            color: white;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .nav-buttons button:hover {
            background-color: #003f88;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 600px;
            margin: 280px auto 0 auto;
        }
        h2 {
            text-align: center;
            font-size: 28px;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .total {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
        }
        .payment-buttons {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .payment-buttons button {
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .payment-buttons button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Cesta de Compras</h1>
        <div class="nav-buttons">
            <button onclick="window.location.href='ventas.php'">Volver a la Tienda</button>
        </div>
    </div>
    
    <div class="container">
        <h2>Resumen de tu Compra</h2>
        <table>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
            </tr>
            <?php
                foreach ($_SESSION['cesta'] as $producto) {
                    $cantidad = isset($producto['cantidad']) ? $producto['cantidad'] : 1;
                    $total_producto = $producto['precio'] * $cantidad;
                    echo "<tr>
                            <td>{$producto['modelo']}</td>
                            <td>$" . number_format($producto['precio'], 2) . "</td>
                            <td>$cantidad</td>
                            <td>$" . number_format($total_producto, 2) . "</td>
                        </tr>";
                }
            ?>
        </table>
        <div class="total">Total: $<?php echo number_format($total, 2); ?></div>
        
        <div class="payment-buttons">
            <form action="cesta.php" method="POST">
                <button type="submit" name="pago_contado">Pagar en Contado</button>
                <button type="submit" name="pago_credito">Pagar a Crédito</button>
            </form>
        </div>
    </div>

    <script src="print32print.js"></script>
    <script>
        function buscarImpresoras() {
            PrintJS.getPrinters().then(printers => {
                console.log("Impresoras disponibles:", printers);
                alert("Impresoras detectadas: " + printers.join(", "));
            }).catch(error => {
                console.error("Error al buscar impresoras:", error);
            });
        }

        document.addEventListener('DOMContentLoaded', buscarImpresoras);
    </script>
</body>
</html>
