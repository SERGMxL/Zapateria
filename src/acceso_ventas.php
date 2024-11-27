<?php
include '../config/db.php'; // Asegúrate de que `db.php` contiene la conexión PDO

// Inicializa el mensaje de error
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_password = $_POST['password'];

    // Consulta la contraseña desde la base de datos
    $stmt = $pdo->prepare("SELECT clave FROM contraseña LIMIT 1");
    $stmt->execute();
    $result = $stmt->fetch();

    if ($result && password_verify($input_password, password_hash($result['clave'], PASSWORD_DEFAULT))) {
        // Si la contraseña es correcta, redirige a vendidos.php
        header('Location: vendidos.php');
        exit();
    } else {
        $error = 'Contraseña incorrecta. Intenta de nuevo.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validación de Acceso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../img/blur_blue.jpg');
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; /* Centra el contenido horizontalmente */
            align-items: center; /* Centra el contenido verticalmente */
            height: 100vh; /* Hace que la página ocupe el 100% de la altura */
            flex-direction: column;
        }
        .navbar {
            background-color: #007bff;
            padding: 15px;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .navbar .title {
            flex-grow: 1;
            text-align: center;
        }
        .navbar button {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .navbar button:hover {
            background-color: #0056b3;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 100%;
            margin-top: 80px; /* Espacio debajo de la navbar */
        }
        h2 {
            color: #333;
        }
        input[type="password"] {
            padding: 10px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin: 15px 0;
        }
        button {
            padding: 10px 20px;
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
        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='ventas.php'">Volver</button>
        <div class="title">AdminKey Necesaria</div>
    </div>
    <div class="container">
        <h2>Acceso Restringido para Aministradores</h2>
        <form method="POST" action="acceso_ventas.php">
            <input type="password" name="password" placeholder="Introduce la contraseña" required>
            <button type="submit">Acceder</button>
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
