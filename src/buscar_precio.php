<?php
include '../config/db.php';

if ($argc !== 2) {
    echo json_encode(['error' => 'Faltan parámetros']);
    exit(1);
}

$codigo_zapato = $argv[1];

// Consultar la base de datos
$stmt = $pdo->prepare("SELECT * FROM zapatos WHERE codigo = ?");
$stmt->execute([$codigo_zapato]);
$zapato = $stmt->fetch();

if ($zapato) {
    echo json_encode([
        'precio' => $zapato['precio'],
        'marca' => $zapato['marca'],
        'modelo' => $zapato['modelo']
    ]);
    exit(0);
} else {
    echo json_encode(['error' => 'Zapato no encontrado']);
    exit(1);
}
?>
