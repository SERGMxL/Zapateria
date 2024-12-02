<?php
include '../config/db.php';

// Variable para los zapatos
$zapatos = [];
$mensaje = "";

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Agregar zapato
    if (isset($_POST['codigo'], $_POST['marca'], $_POST['modelo'], $_POST['color'], $_POST['precio']) && !isset($_POST['editar_id'])) {
        $codigo = $_POST['codigo'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $color = $_POST['color'];
        $precio = $_POST['precio'];

        try {
            $stmt = $pdo->prepare("INSERT INTO zapatos (codigo, marca, modelo, color, precio) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$codigo, $marca, $modelo, $color, $precio]);
            $mensaje = "Zapato agregado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error al guardar el zapato: " . $e->getMessage();
        }
    }

    // Eliminar zapato
    if (isset($_POST['eliminar_id'])) {
        $id = $_POST['eliminar_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM zapatos WHERE id = ?");
            $stmt->execute([$id]);
            $mensaje = "Zapato eliminado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error al eliminar el zapato: " . $e->getMessage();
        }
    }

    // Editar zapato
    if (isset($_POST['editar_id'], $_POST['editar_codigo'], $_POST['editar_marca'], 
               $_POST['editar_modelo'], $_POST['editar_color'], $_POST['editar_precio'])) {
        $id = $_POST['editar_id'];
        $codigo = $_POST['editar_codigo'];
        $marca = $_POST['editar_marca'];
        $modelo = $_POST['editar_modelo'];
        $color = $_POST['editar_color'];
        $precio = $_POST['editar_precio'];

        try {
            $stmt = $pdo->prepare("UPDATE zapatos SET codigo = ?, marca = ?, modelo = ?, color = ?, precio = ? WHERE id = ?");
            $stmt->execute([$codigo, $marca, $modelo, $color, $precio, $id]);
            $mensaje = "Zapato actualizado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error al actualizar el zapato: " . $e->getMessage();
        }
    }
}

// Obtener los zapatos registrados
$stmt = $pdo->query("SELECT id, codigo, marca, modelo, color, precio FROM zapatos");
$zapatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Zapatos - ZapaTecNM</title>
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
            width: 90%;
            max-width: 1000px;
            margin: 50px auto;
            overflow-x: auto;
        }
        .boton-agregar {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }
        .boton-agregar:hover {
            background-color: #218838;
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
        #popup-agregar, #popup-editar {
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
            max-width: 400px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            animation: popupEffect 1s ease;
        }
        .tabla-zapatos {
            width: 100%;
            border-collapse: collapse;
        }
        .tabla-zapatos th, .tabla-zapatos td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .tabla-zapatos th {
            background-color: #007bff;
            color: white;
        }
        .tabla-zapatos tr:nth-child(even) {
            background-color: rgba(0, 123, 255, 0.1);
        }
        .tabla-zapatos tr:hover {
            background-color: rgba(0, 123, 255, 0.2);
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
        .boton-eliminar {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px;
        }
        .boton-editar {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <button onclick="location.href='ventas.php'">Volver</button>
        <div class="title">Gestión de Zapatos</div>
    </div>

    <div id="popup"><?php if (isset($mensaje)) echo $mensaje; ?></div>

    <div class="container">
        <button class="boton-agregar" onclick="mostrarAgregarZapato()">Agregar Zapato</button>
        
        <table class="tabla-zapatos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Color</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($zapatos as $zapato): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($zapato['id']); ?></td>
                        <td><?php echo htmlspecialchars($zapato['codigo']); ?></td>
                        <td><?php echo htmlspecialchars($zapato['marca']); ?></td>
                        <td><?php echo htmlspecialchars($zapato['modelo']); ?></td>
                        <td><?php echo htmlspecialchars($zapato['color']); ?></td>
                        <td>$<?php echo number_format($zapato['precio'], 2); ?></td>
                        <td>
                            <!-- Eliminar -->
                            <form method="POST" action="zapatos.php" style="display:inline;">
                                <input type="hidden" name="eliminar_id" value="<?php echo $zapato['id']; ?>">
                                <button type="submit" class="boton-eliminar">Eliminar</button>
                            </form>
                            <!-- Editar -->
                            <button onclick="editarZapato(
                                '<?php echo $zapato['id']; ?>', 
                                '<?php echo htmlspecialchars($zapato['codigo']); ?>', 
                                '<?php echo htmlspecialchars($zapato['marca']); ?>', 
                                '<?php echo htmlspecialchars($zapato['modelo']); ?>', 
                                '<?php echo htmlspecialchars($zapato['color']); ?>', 
                                '<?php echo $zapato['precio']; ?>')" 
                                class="boton-editar">Editar</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Popup Agregar Zapato -->
    <div id="popup-agregar">
        <h2>Agregar Nuevo Zapato</h2>
        <form method="POST" action="productos.php">
            <label for="codigo">Código:</label>
            <input type="text" id="codigo" name="codigo" required>
            
            <label for="marca">Marca:</label>
            <input type="text" id="marca" name="marca" required>
            
            <label for="modelo">Modelo:</label>
            <input type="text" id="modelo" name="modelo" required>
            
            <label for="color">Color:</label>
            <input type="text" id="color" name="color" required>
            
            <label for="precio">Precio:</label>
            <input type="number" step="0.01" id="precio" name="precio" required>
            
            <button type="submit">Guardar Zapato</button>
        </form>
        <button onclick="cerrarAgregarZapato()">Cerrar</button>
    </div>

    <!-- Popup Editar Zapato -->
    <div id="popup-editar">
        <h2>Editar Zapato</h2>
        <form method="POST" action="productos.php">
            <input type="hidden" id="editar_id" name="editar_id">
            <label for="editar_codigo">Código:</label>
            <input type="text" id="editar_codigo" name="editar_codigo" required>
            <label for="editar_marca">Marca:</label>
            <input type="text" id="editar_marca" name="editar_marca" required>
            <label for="editar_modelo">Modelo:</label>
            <input type="text" id="editar_modelo" name="editar_modelo" required>
            <label for="editar_color">Color:</label>
            <input type="text" id="editar_color" name="editar_color" required>
            <label for="editar_precio">Precio:</label>
            <input type="number" step="0.01" id="editar_precio" name="editar_precio" required>
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

        function mostrarAgregarZapato() {
            document.getElementById('popup-agregar').style.display = 'block';
        }

        function cerrarAgregarZapato() {
            document.getElementById('popup-agregar').style.display = 'none';
        }

        function editarZapato(id, codigo, marca, modelo, color, precio) {
            document.getElementById('popup-editar').style.display = 'block';
            document.getElementById('editar_id').value = id;
            document.getElementById('editar_codigo').value = codigo;
            document.getElementById('editar_marca').value = marca;
            document.getElementById('editar_modelo').value = modelo;
            document.getElementById('editar_color').value = color;
            document.getElementById('editar_precio').value = precio;
        }

        function cerrarEditar() {
            document.getElementById('popup-editar').style.display = 'none';
        }
    </script>
</body>
</html>