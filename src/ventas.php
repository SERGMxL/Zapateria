<?php
session_start(); // Para manejar la sesión
include '../config/db.php';

$error = '';

// Manejo de la búsqueda del precio por AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_precio'])) {
    $codigo_zapato = $_POST['codigo_zapato'];

    $stmt = $pdo->prepare("SELECT * FROM zapatos WHERE codigo = ?");
    $stmt->execute([$codigo_zapato]);
    $zapato = $stmt->fetch();

    if ($zapato) {
        echo json_encode([
            'precio' => $zapato['precio'],
            'marca' => $zapato['marca'],
            'modelo' => $zapato['modelo']
        ]);
    } else {
        echo json_encode(['error' => 'Zapato no encontrado']);
    }
    exit;
}

// Manejo de la adición a la cesta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_cesta'])) {
    $codigo_zapato = $_POST['codigo_zapato'];
    $precio = $_POST['precio'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];

    if (!isset($_SESSION['cesta'])) {
        $_SESSION['cesta'] = [];
    }

    // Agregar zapato a la cesta
    $_SESSION['cesta'][] = [
        'codigo' => $codigo_zapato,
        'marca' => $marca,
        'modelo' => $modelo,
        'precio' => $precio
    ];

    echo "<script>alert('Zapato agregado a la cesta');</script>";
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
            width: 400px;
            margin: 200px auto 0 auto; /* Se ajusta el margen para no superponer la navbar */
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
        }
        button:hover {
            background-color: #0056b3;
        }
        #formulario_credito {
            display: none;
        }
        </style>
</head>
<body>
<div class="navbar">
    <h1>ZapaTecNM</h1>
    <div class="nav-buttons">
        <button onclick="location.href='proveedor.php'">Proveedores</button>
        <button onclick="location.href='acceso.php'">Usuarios</button>
        <button onclick="location.href='login.php'">Cerrar Sesión</button>
    </div>
</div>

<div class="container">
    <h2>Gestión de Ventas</h2>
    <form method="POST" action="ventas.php">
        <label for="codigo_zapato">Código del zapato:</label>
        <input type="text" id="codigo_zapato" name="codigo_zapato" required>
        <button type="button" onclick="buscarPrecio()">Buscar Modelo</button>

        <label for="precio_zapato">Precio:</label>
        <input type="text" id="precio_zapato" name="precio" readonly>

        <input type="hidden" id="marca_zapato" name="marca">
        <input type="hidden" id="modelo_zapato" name="modelo">

        <button type="submit" name="agregar_cesta" value="1">Agregar a la Cesta</button>
        <button type="button" onclick="location.href='cesta.php'">Ver Cesta</button>
    </form>
</div>

<script>
    function buscarPrecio() {
        const codigo = document.getElementById('codigo_zapato').value;

        if (!codigo) {
            alert('Por favor ingresa el código del zapato.');
            return;
        }

        const formData = new FormData();
        formData.append('codigo_zapato', codigo);
        formData.append('buscar_precio', true);

        fetch('ventas.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                document.getElementById('precio_zapato').value = data.precio;
                document.getElementById('marca_zapato').value = data.marca;
                document.getElementById('modelo_zapato').value = data.modelo;
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
</body>
</html>