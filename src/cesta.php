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
    $cantidad = isset($producto['cantidad']) ? $producto['cantidad'] : 1; // Si no hay cantidad, usa 1 como valor por defecto
    $total_producto = $producto['precio'] * $cantidad; // Calcula el total por producto
    $total += $total_producto; // Acumula el total
}

// Procesar pago
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_pago = isset($_POST['pago_contado']) ? 'contado' : 'credito';
    $cliente_nombre = "ZapaTecNM";
    $cliente_email = "klausdepaepe@gmail.com";
    $id_usuario = 1;

    // Registrar cada producto en la tabla ventas
    $fecha_venta = date('Y-m-d');
    $venta_id = 123; // Aquí podrías obtener el ID de la venta (usualmente después de insertarla en la base de datos)

    foreach ($_SESSION['cesta'] as $item) {
        $codigo_zapato = $item['codigo'];
        $precio = $item['precio'];
        $cantidad = isset($item['cantidad']) ? $item['cantidad'] : 1; // Si no hay cantidad, usa 1 como valor por defecto

        $stmt = $mysqli->prepare("
            INSERT INTO ventas (fecha, cliente_nombre, cliente_email, tipo_pago, total, id_usuario, codigo_zapato, nombre_cliente, fecha_venta, precio, cantidad) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            'ssssdsdssdd',
            $fecha_venta,
            $cliente_nombre,
            $cliente_email,
            $tipo_pago,
            $total,
            $id_usuario,
            $codigo_zapato,
            $cliente_nombre,
            $fecha_venta,
            $precio,
            $cantidad
        );


        if (!$stmt->execute()) {
            echo "<script>alert('Error al registrar la venta: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }

    // Función para generar el ticket
    function generar_ticket($venta_id, $cliente_nombre, $cliente_email, $productos, $total, $fecha_venta, $tipo_pago) {
        // Crear contenido HTML del ticket
        $ticket_html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Ticket de Venta</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    width: 300px;
                    background-color: #f4f4f4;
                }
                h1 {
                    text-align: center;
                    color: #333;
                }
                .ticket-info {
                    margin-bottom: 20px;
                    padding: 10px;
                    background-color: #fff;
                    border-radius: 8px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }
                .ticket-info p {
                    margin: 5px 0;
                    font-size: 14px;
                }
                table {
                    width: 100%;
                    margin-top: 10px;
                    border-collapse: collapse;
                }
                table, th, td {
                    border: 1px solid #ddd;
                }
                th, td {
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #007bff;
                    color: white;
                }
                .total {
                    font-size: 18px;
                    font-weight: bold;
                    text-align: right;
                    margin-top: 15px;
                }
                .footer {
                    text-align: center;
                    font-size: 12px;
                    margin-top: 20px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <h1>Ticket de Venta</h1>
            <div class="ticket-info">
                <p><strong>Venta ID:</strong> ' . $venta_id . '</p>
                <p><strong>Fecha:</strong> ' . $fecha_venta . '</p>
                <p><strong>Vendedor:</strong> ' . htmlspecialchars($cliente_nombre) . '</p>
                <p><strong>Email:</strong> ' . htmlspecialchars($cliente_email) . '</p>
                <p><strong>Tipo de Pago:</strong> ' . ucfirst($tipo_pago) . '</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>';

        // Agregar productos al ticket
        foreach ($productos as $item) {
            $cantidad = isset($item['cantidad']) ? $item['cantidad'] : 1; // Verifica la cantidad
            $total_producto = $item['precio'] * $cantidad;

            $ticket_html .= '
                <tr>
                    <td>' . htmlspecialchars($item['codigo']) . '</td>
                    <td>' . htmlspecialchars($item['marca']) . '</td>
                    <td>' . htmlspecialchars($item['modelo']) . '</td>
                    <td>$' . number_format($item['precio'], 2) . '</td>
                    <td>' . $cantidad . '</td>
                    <td>$' . number_format($total_producto, 2) . '</td>
                </tr>';
        }

        $ticket_html .= '
                </tbody>
            </table>

            <div class="total">
                Total: $' . number_format($total, 2) . '
            </div>

            <div class="footer">
                Gracias por tu compra. ¡Vuelve pronto!
            </div>
        </body>
        </html>';

        // Guardar el ticket en un archivo
        $filename = 'ticket_' . $venta_id . '.html';
        file_put_contents($filename, $ticket_html);

        // Puedes devolver la ruta del archivo generado si deseas usarlo más tarde
        return $filename;
    }

    // Llamada a la función para generar el ticket
    $ticket = generar_ticket($venta_id, $cliente_nombre, $cliente_email, $_SESSION['cesta'], $total, $fecha_venta, $tipo_pago);

    // Mostrar mensaje de éxito y redirigir al cliente
    echo "<script>alert('Compra a $tipo_pago realizada.'); window.location.href = '$ticket';</script>";

    session_destroy(); // Limpiar la cesta después del pago
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
</body>
</html>