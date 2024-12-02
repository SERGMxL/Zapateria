<?php
include '../config/db.php';

// Variable para los usuarios
$usuarios = [];
$mensaje = "";

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Agregar usuario
    if (isset($_POST['nombre'], $_POST['tipo']) && !isset($_POST['editar_id'])) {
        $nombre = $_POST['nombre'];
        $tipo = $_POST['tipo'];

        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, tipo) VALUES (?, ?)");
            $stmt->execute([$nombre, $tipo]);
            $mensaje = "Usuario agregado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error al guardar el usuario: " . $e->getMessage();
        }
    }

    // Eliminar usuario
    if (isset($_POST['eliminar_id'])) {
        $id = $_POST['eliminar_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $mensaje = "Usuario eliminado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error al eliminar el usuario: " . $e->getMessage();
        }
    }

    // Editar usuario
    if (isset($_POST['editar_id'], $_POST['editar_nombre'], $_POST['editar_tipo'])) {
        $id = $_POST['editar_id'];
        $nombre = $_POST['editar_nombre'];
        $tipo = $_POST['editar_tipo'];

        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, tipo = ? WHERE id = ?");
            $stmt->execute([$nombre, $tipo, $id]);
            $mensaje = "Usuario actualizado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error al actualizar el usuario: " . $e->getMessage();
        }
    }
}

// Obtener los usuarios registrados
$stmt = $pdo->query("SELECT id, nombre, tipo FROM usuarios");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios - ZapaTecNM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../img/blur_blue.jpg');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #007bff;
            padding: 15px;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 400px;
            margin: 250px auto;
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
        #popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 18px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            animation: popupEffect 1s ease;
        }
        #popup-usuarios, #popup-editar {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            animation: popupEffect 1s ease;
        }
        .tabla-usuarios {
            width: 100%;
            border-collapse: collapse;
        }
        .tabla-usuarios th, .tabla-usuarios td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .tabla-usuarios th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='ventas.php'">Volver</button>
        <div class="title">Gestión de Usuarios</div>
        <button onclick="verUsuarios()">Ver Usuarios</button>
    </div>

    <div id="popup"><?php if (isset($mensaje)) echo $mensaje; ?></div>

    <div id="popup-usuarios">
        <h2>Usuarios Registrados</h2>
        <table class="tabla-usuarios">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Acción</th>
            </tr>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['tipo']); ?></td>
                    <td>
                        <!-- Eliminar -->
                        <form method="POST" action="usuarios.php" style="display:inline;">
                            <input type="hidden" name="eliminar_id" value="<?php echo $usuario['id']; ?>">
                            <button type="submit" style="background-color: #ff4d4d; color: white;">Eliminar</button>
                        </form>
                        <!-- Editar -->
                        <button onclick="editarUsuario('<?php echo $usuario['id']; ?>', '<?php echo htmlspecialchars($usuario['nombre']); ?>', '<?php echo htmlspecialchars($usuario['tipo']); ?>')" style="background-color: #4caf50; color: white;">Editar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <button onclick="cerrarUsuarios()">Cerrar</button>
    </div>

    <div id="popup-editar">
        <h2>Editar Usuario</h2>
        <form method="POST" action="usuarios.php">
            <input type="hidden" id="editar_id" name="editar_id">
            <label for="editar_nombre">Nombre:</label>
            <input type="text" id="editar_nombre" name="editar_nombre">
            <label for="editar_tipo">Tipo:</label>
            <input type="text" id="editar_tipo" name="editar_tipo">
            <button type="submit">Guardar Cambios</button>
        </form>
        <button onclick="cerrarEditar()">Cerrar</button>
    </div>

    <script>
        <?php if (!empty($mensaje)): ?>
            document.getElementById('popup').style.display = 'block';
            setTimeout(function() {
                document.getElementById('popup').style.display = 'none';
            }, 3000);
        <?php endif; ?>

        function verUsuarios() {
            document.getElementById('popup-usuarios').style.display = 'block';
        }

        function cerrarUsuarios() {
            document.getElementById('popup-usuarios').style.display = 'none';
        }

        function editarUsuario(id, nombre, tipo) {
            document.getElementById('popup-editar').style.display = 'block';
            document.getElementById('editar_id').value = id;
            document.getElementById('editar_nombre').value = nombre;
            document.getElementById('editar_tipo').value = tipo;
        }

        function cerrarEditar() {
            document.getElementById('popup-editar').style.display = 'none';
        }
    </script>
</body>
</html>
