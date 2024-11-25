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
$total = array_sum(array_column($_SESSION['cesta'], 'precio'));

// Procesar pago
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_pago = isset($_POST['pago_contado']) ? 'contado' : 'credito';
    $cliente_nombre = "Sergio Castañeda"; // Puedes usar una variable de sesión con los datos del cliente.
    $cliente_email = "klausdepaepe@gmail.com"; // También puedes obtenerlo dinámicamente.
    $id_usuario = 1; // Cambia según el usuario activo.

    // Registrar cada producto en la tabla ventas
    $fecha_venta = date('Y-m-d');
    foreach ($_SESSION['cesta'] as $item) {
        $codigo_zapato = $item['codigo'];
        $precio = $item['precio'];

        $stmt = $mysqli->prepare("
            INSERT INTO ventas (fecha, cliente_nombre, cliente_email, tipo_pago, total, id_usuario, codigo_zapato, nombre_cliente, fecha_venta, precio) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            'ssssdsdssd',
            $fecha_venta,
            $cliente_nombre,
            $cliente_email,
            $tipo_pago,
            $total,
            $id_usuario,
            $codigo_zapato,
            $cliente_nombre,
            $fecha_venta,
            $precio
        );

        if (!$stmt->execute()) {
            echo "<script>alert('Error al registrar la venta: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }

    echo "<script>alert('Compra a $tipo_pago realizada.');</script>";
    session_destroy(); // Limpiar la cesta después del pago
    header("Location: ventas.php");
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
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        button {
            padding: 10px 15px;
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
    </style>
</head>
<body>
<div class="navbar">
    <h3>Cesta</h3>
    <div class="nav-buttons">
        <button onclick="location.href='ventas.php'">Volver</button>

    </div>
</div>

<div class="container">
    <h2>Tu Cesta de Compras</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['cesta'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['codigo']) ?></td>
                    <td><?= htmlspecialchars($item['marca']) ?></td>
                    <td><?= htmlspecialchars($item['modelo']) ?></td>
                    <td>$<?= number_format($item['precio'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h3 style="text-align: right; color: #333;">Total: $<?= number_format($total, 2) ?></h3>
    <div class="buttons">
        <form method="POST" style="margin: 0;">
            <button type="submit" name="pago_contado">Pagar a Contado</button>
        </form>
        <form method="POST" style="margin: 0;">
            <button type="submit" name="pago_credito">Pagar a Crédito</button>
        </form>
    </div>
</div>
</body>
</html>
