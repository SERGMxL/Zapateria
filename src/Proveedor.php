<?php
require_once('../config/db.php');

// Variable para almacenar mensajes
$mensaje = '';

// Procesar formulario de agregar proveedor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nombre']) && !isset($_POST['editar_id']) && !isset($_POST['eliminar_id'])) {
        // Agregar nuevo proveedor
        $nombre = $_POST['nombre'];
        $contacto = $_POST['contacto'];
        $direccion = $_POST['direccion'];
        $correo = $_POST['correo'];
        $rfc = $_POST['rfc'];

        try {
            $sql = "INSERT INTO proveedores (nombre, contacto, direccion, correo, rfc) 
                    VALUES (:nombre, :contacto, :direccion, :correo, :rfc)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':contacto' => $contacto,
                ':direccion' => $direccion,
                ':correo' => $correo,
                ':rfc' => $rfc
            ]);
            $mensaje = "Proveedor agregado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error: " . $e->getMessage();
        }
    }
    
    // Eliminar proveedor
    if (isset($_POST['eliminar_id'])) {
        $id = $_POST['eliminar_id'];

        try {
            $sql = "DELETE FROM proveedores WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $mensaje = "Proveedor eliminado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error: " . $e->getMessage();
        }
    }

    // Editar proveedor
    if (isset($_POST['editar_id'])) {
        $id = $_POST['editar_id'];
        $nombre = $_POST['nombre'];
        $contacto = $_POST['contacto'];
        $direccion = $_POST['direccion'];
        $correo = $_POST['correo'];
        $rfc = $_POST['rfc'];

        try {
            $sql = "UPDATE proveedores SET nombre = :nombre, contacto = :contacto, direccion = :direccion, 
                    correo = :correo, rfc = :rfc WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':nombre' => $nombre,
                ':contacto' => $contacto,
                ':direccion' => $direccion,
                ':correo' => $correo,
                ':rfc' => $rfc
            ]);
            $mensaje = "Proveedor actualizado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error: " . $e->getMessage();
        }
    }
}

// Obtener todos los proveedores
$sql = "SELECT * FROM proveedores";
$stmt = $pdo->query($sql);
$proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proveedores - ZapaTecNM</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../img/blur_blue.jpg');
            background-size: cover;
            background-position: center;
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
        label, input, button {
            margin-top: 10px;
        }
        input, button {
            padding: 10px;
            border-radius: 10px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        #popup-proveedores, #popup-editar {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.95);
            color: #333;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-height: 80%;
            overflow-y: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .tabla-proveedores {
            width: 100%;
            border-collapse: collapse;
            background-color: #2c3e50; /* Dark background */
            color: #ecf0f1; /* Light text color */
        }
        .tabla-proveedores th {
            background-color: #34495e; /* Slightly lighter dark header */
            color: #ffffff;
            padding: 10px;
            border: 1px solid #465870;
        }
        .tabla-proveedores td {
            padding: 10px;
            border: 1px solid #465870;
            background-color: #2c3e50;
        }
        .tabla-proveedores tr:hover {
            background-color: #34495e; /* Hover effect */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='ventas.php'">Volver</button>
        <div class="title">Gestión de Proveedores</div>
        <button onclick="verProveedores()">Ver Proveedores</button>
    </div>

    <?php if($mensaje): ?>
    <div style="background-color: #f0f0f0; padding: 10px; text-align: center;">
        <?php echo htmlspecialchars($mensaje); ?>
    </div>
    <?php endif; ?>

    <div class="container">
        <h2>Agregar Proveedor</h2>
        <form method="POST" action="proveedor.php">
            <label for="nombre">Nombre de la empresa:</label>
            <input type="text" name="nombre" required>

            <label for="contacto">Número de contacto:</label>
            <input type="text" name="contacto" required>

            <label for="direccion">Dirección:</label>
            <input type="text" name="direccion" required>

            <label for="correo">Correo:</label>
            <input type="email" name="correo" required>

            <label for="rfc">RFC:</label>
            <input type="text" name="rfc" required>

            <button type="submit">Guardar Proveedor</button>
        </form>
    </div>

    <div id="popup-proveedores">
        <h2>Proveedores Registrados</h2>
        <table class="tabla-proveedores">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Contacto</th>
                <th>Dirección</th>
                <th>Correo</th>
                <th>RFC</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($proveedores as $proveedor): ?>
                <tr>
                    <td><?php echo htmlspecialchars($proveedor['id']); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['contacto']); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['direccion']); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['correo']); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['rfc']); ?></td>
                    <td>
                        <button onclick="editarProveedor(
                            '<?php echo $proveedor['id']; ?>',
                            '<?php echo htmlspecialchars($proveedor['nombre']); ?>',
                            '<?php echo htmlspecialchars($proveedor['contacto']); ?>',
                            '<?php echo htmlspecialchars($proveedor['direccion']); ?>',
                            '<?php echo htmlspecialchars($proveedor['correo']); ?>',
                            '<?php echo htmlspecialchars($proveedor['rfc']); ?>'
                        )">Editar</button>
                        <form method="POST" action="proveedor.php" style="display:inline;">
                            <input type="hidden" name="eliminar_id" value="<?php echo $proveedor['id']; ?>">
                            <button type="submit" style="background-color: #ff4d4d;">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <button onclick="cerrarProveedores()">Cerrar</button>
    </div>

    <div id="popup-editar">
        <h2>Editar Proveedor</h2>
        <form method="POST" action="proveedor.php">
            <input type="hidden" name="editar_id" id="editar-id">
            <label for="editar-nombre">Nombre:</label>
            <input type="text" name="nombre" id="editar-nombre" required>
            <label for="editar-contacto">Contacto:</label>
            <input type="text" name="contacto" id="editar-contacto" required>
            <label for="editar-direccion">Dirección:</label>
            <input type="text" name="direccion" id="editar-direccion" required>
            <label for="editar-correo">Correo:</label>
            <input type="email" name="correo" id="editar-correo" required>
            <label for="editar-rfc">RFC:</label>
            <input type="text" name="rfc" id="editar-rfc" required>
            <button type="submit">Guardar Cambios</button>
        </form>
        <button onclick="cerrarEditar()">Cancelar</button>
    </div>

    <script>
        function verProveedores() {
            document.getElementById('popup-proveedores').style.display = 'block';
        }

        function cerrarProveedores() {
            document.getElementById('popup-proveedores').style.display = 'none';
        }

        function editarProveedor(id, nombre, contacto, direccion, correo, rfc) {
            document.getElementById('editar-id').value = id;
            document.getElementById('editar-nombre').value = nombre;
            document.getElementById('editar-contacto').value = contacto;
            document.getElementById('editar-direccion').value = direccion;
            document.getElementById('editar-correo').value = correo;
            document.getElementById('editar-rfc').value = rfc;
            document.getElementById('popup-editar').style.display = 'block';
        }

        function cerrarEditar() {
            document.getElementById('popup-editar').style.display = 'none';
        }
    </script>
</body>
</html>