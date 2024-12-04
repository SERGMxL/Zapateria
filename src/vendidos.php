<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "tienda_zapatos");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Consulta las ventas en la base de datos
$query = "SELECT * FROM ventas ORDER BY fecha DESC";
$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas Registradas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../img/fondo.jpg');
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
            margin-right: 20px;
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
            width: 80%;
            margin: 150px auto;
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
        .no-data {
            text-align: center;
            font-size: 16px;
            color: #777;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Ventas Registradas</h1>
    <div class="nav-buttons">
        <button onclick="location.href='ventas.php'">Volver</button>
    </div>
</div>

<div class="container">
    <h2>Listado de Ventas</h2>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Vendedor</th>
                    <th>Email</th>
                    <th>Tipo de Pago</th>
                    <th>Total</th>
                    <th>Código de Zapato</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['fecha']) ?></td>
                        <td><?= htmlspecialchars($row['cliente_nombre']) ?></td>
                        <td><?= htmlspecialchars($row['cliente_email']) ?></td>
                        <td><?= htmlspecialchars($row['tipo_pago']) ?></td>
                        <td>$<?= number_format($row['total'], 2) ?></td>
                        <td><?= htmlspecialchars($row['codigo_zapato']) ?></td>
                        <td>$<?= number_format($row['precio'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-data">No hay ventas registradas en este momento.</div>
    <?php endif; ?>
</div>
</body>
</html>
