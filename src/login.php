<?php
session_start();
include '../config/db.php';

$error = '';
$secret_key = 'tu_clave_secreta';
$iv = '1234567890123456'; // IV debe ser exactamente de 16 bytes

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Por favor, completa todos los campos.';
    } else {
        // Encriptar el nombre de usuario para buscarlo
        $encrypted_username = openssl_encrypt($username, 'aes-256-cbc', $secret_key, 0, $iv);

        // Recuperar usuario de la base de datos
        $stmt = $pdo->prepare("SELECT id, password FROM usuarios WHERE username = :username");
        $stmt->bindParam(':username', $encrypted_username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Desencriptar la contraseña almacenada
            $decrypted_password = openssl_decrypt($user['password'], 'aes-256-cbc', $secret_key, 0, $iv);

            if ($password === $decrypted_password) {
                // Inicio de sesión exitoso
                $_SESSION['user_id'] = $user['id'];
                header("Location: ventas.php");
                exit();
            } else {
                $error = 'Contraseña incorrecta.';
            }
        } else {
            $error = 'Usuario no encontrado.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión - Bienvenido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('../img/venta.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
        }
        .text-box {
            width: 50%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 20px;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
        }
        .text-box h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .text-box p {
            font-size: 18px;
            line-height: 1.8;
            animation: fadeInOut 5s infinite;
        }
        .login-box {
            width: 50%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .login-box h2 {
            color: #007bff;
            font-size: 28px;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80%;
        }
        form label {
            color: #333;
            margin: 10px 0 5px;
            font-size: 14px;
            align-self: flex-start;
        }
        form input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            font-size: 14px;
        }
        form button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 15px;
            width: 100%;
        }
        form button:hover {
            background: #0056b3;
        }
        p {
            margin-top: 15px;
        }
        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
        @keyframes fadeInOut {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <!-- Tile de texto a la izquierda -->
    <div class="text-box">
        <h1>Bienvenido a ZapaTecNM</h1>
        <p>Administra tus ventas de manera rápida y sencilla.</p>
        <p>Inicia sesión en el sistema y aprovecha nuestras funcionalidades diseñadas especialmente para ti.</p>
    </div>

    <!-- Tile de inicio de sesión a la derecha -->
    <div class="login-box">
        <h2>Inicio de Sesión</h2>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Usuario:</label>
            <input type="text" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
