<?php
include '../config/db.php';

// Variable para los proveedores
$proveedores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nombre'], $_POST['contacto'])) {
        $nombre = $_POST['nombre'];
        $contacto = $_POST['contacto'];

        try {
            $stmt = $pdo->prepare("INSERT INTO proveedores (nombre, contacto) VALUES (?, ?)");
            $stmt->execute([$nombre, $contacto]);
            $mensaje = "Proveedor agregado correctamente."; // Variable para el mensaje
        } catch (PDOException $e) {
            $mensaje = "Error al guardar el proveedor: " . $e->getMessage(); // Variable para el error
        }
    }

    // Eliminar proveedor
    if (isset($_POST['eliminar_id'])) {
        $id = $_POST['eliminar_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM proveedores WHERE id = ?");
            $stmt->execute([$id]);
            $mensaje = "Proveedor eliminado correctamente."; // Mensaje de eliminación
        } catch (PDOException $e) {
            $mensaje = "Error al eliminar el proveedor: " . $e->getMessage();
        }
    }
}

// Obtener los proveedores registrados
$stmt = $pdo->query("SELECT * FROM proveedores");
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
            background-image: url('../src/venta.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #007bff;
            padding: 15px;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            justify-content: space-between; /* Para separar los elementos */
            align-items: center;
        }
        .navbar .title {
            flex-grow: 1;
            text-align: center; /* Centra el texto en la barra */
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
        /* Estilo para la ventana emergente */
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

        /* Efecto de salto para el popup */
        @keyframes popupEffect {
            0% { transform: translate(-50%, -60%); }
            50% { transform: translate(-50%, -50%); }
            100% { transform: translate(-50%, -50%); }
        }

        /* Estilo para la ventana emergente de proveedores */
        #popup-proveedores {
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
            overflow-y: auto;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            max-height: 80%;
            animation: popupEffect 1s ease;
        }
        .tabla-proveedores {
            width: 100%;
            border-collapse: collapse;
        }
        .tabla-proveedores th, .tabla-proveedores td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .tabla-proveedores th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='ventas.php'">Volver</button>
        <div class="title">Gestión de Proveedores</div>
        <button onclick="verProveedores()">Ver Proveedores</button>
    </div>

    <div class="container">
        <h2>Agregar Proveedor</h2>
        <form method="POST" action="proveedor.php">
            <label for="nombre">Nombre de la empresa:</label>
            <input type="text" name="nombre" required>

            <label for="contacto">Número de contacto:</label>
            <input type="text" name="contacto" required>

            <button type="submit">Guardar Proveedor</button>
        </form>
    </div>

    <!-- Ventana emergente que aparece cuando el proveedor es agregado correctamente -->
    <div id="popup"><?php if(isset($mensaje)) echo $mensaje; ?></div>

   <!-- Ventana emergente para ver proveedores -->
<div id="popup-proveedores">
    <h2>Proveedores Registrados</h2>
    <table class="tabla-proveedores">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Contacto</th>
            <th>Acción</th>
        </tr>
        <?php foreach ($proveedores as $proveedor): ?>
            <tr>
                <td><?php echo htmlspecialchars($proveedor['id']); ?></td>
                <td><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                <td><?php echo htmlspecialchars($proveedor['contacto']); ?></td>
                <td>
                    <!-- Botón para eliminar el proveedor -->
                    <form method="POST" action="proveedor.php" style="display:inline;">
                        <input type="hidden" name="eliminar_id" value="<?php echo $proveedor['id']; ?>">
                        <button type="submit" style="background-color: #ff4d4d; color: white; padding: 5px 10px; border: none; border-radius: 5px;">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <button onclick="cerrarProveedores()">Cerrar</button>
</div>


    <script>
        // Mostrar la ventana emergente si el mensaje está presente
        <?php if (isset($mensaje)): ?>
            document.getElementById('popup').style.display = 'block';
            setTimeout(function() {
                document.getElementById('popup').style.display = 'none';
            }, 3000); // El mensaje se oculta después de 3 segundos
        <?php endif; ?>

        // Función para ver los proveedores
        function verProveedores() {
            document.getElementById('popup-proveedores').style.display = 'block';
        }

        // Función para cerrar la ventana emergente de proveedores
        function cerrarProveedores() {
            document.getElementById('popup-proveedores').style.display = 'none';
        }
    </script>
</body>
</html>
