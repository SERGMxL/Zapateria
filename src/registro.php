<?php
session_start();
include '../config/db.php';

$error = '';
$secret_key = 'tu_clave_secreta'; // Cambia esto por una clave secreta
$iv = '1234567890123456'; // Debe ser de 16 bytes

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Validación de los campos
    if (empty($username) || empty($password) || empty($email)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Verificar si el usuario ya existe
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->fetch()) {
            $error = "El correo electrónico ya está en uso.";
        } else {
            // Encriptar el nombre de usuario y la contraseña
            $encrypted_username = openssl_encrypt($username, 'aes-256-cbc', $secret_key, 0, $iv);
            $encrypted_password = openssl_encrypt($password, 'aes-256-cbc', $secret_key, 0, $iv);

            // Insertar el nuevo usuario
            $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, email) VALUES (:username, :password, :email)");
            $stmt->bindParam(':username', $encrypted_username);
            $stmt->bindParam(':password', $encrypted_password);
            $stmt->bindParam(':email', $email);

            try {
                $stmt->execute();
                $_SESSION['success'] = "Registro exitoso. Por favor, inicia sesión.";
                header("Location: login.php");
                exit();
            } catch (PDOException $e) {
                $error = "Error al registrar al usuario: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Bienvenido</title>
    <style>
        /* General */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('../src/fondo.jpg'); /* Ruta de la imagen de fondo */
            background-size: cover;
            background-position: center;
            display: flex;
            overflow: hidden;
            animation: fadeIn 1s ease;
        }

        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        /* Caja del formulario */
        .register-box {
            width: 50%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-box h2 {
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

        /* Caja de texto */
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
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
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

        @keyframes fadeInOut {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Enlace de iniciar sesión */
        .login-link {
            margin-top: 10px;
            font-size: 14px;
            color: #007bff;
            text-decoration: none;
            cursor: pointer;
        }

        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Formulario de registro -->
    <div class="register-box">
        <h2>Registro</h2>
        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">Nombre de usuario:</label>
            <input type="text" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required>

            <label for="email">Correo electrónico:</label>
            <input type="email" name="email" required>

            <button type="submit">Registrarse</button>
        </form>
        <p>¿Ya tienes una cuenta? <a href="login.php" class="login-link" id="loginLink">Inicia sesión</a></p>
    </div>

    <!-- Cuadro de texto informativo -->
    <div class="text-box">
        <h1>¡Bienvenido a ZapaTecNM!</h1>
        <p>Regístrate para comenzar a administrar tus ventas de forma profesional.</p>
        <p>Descubre herramientas diseñadas para facilitar tu negocio.</p>
    </div>

    <script>
        // Añade una animación de salida al hacer clic en el enlace
        const loginLink = document.getElementById('loginLink');
        loginLink.addEventListener('click', (e) => {
            e.preventDefault();
            document.body.style.animation = 'fadeOut 1s ease';
            setTimeout(() => {
                window.location.href = loginLink.href;
            }, 1000); // Espera a que termine la animación
        });
    </script>
</body>
</html>
