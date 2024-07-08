<?php
require '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_producto = $_POST['codigo_producto'];
    $cantidad_nueva = $_POST['cantidad_nueva'];

    // Validar datos recibidos
    if (!empty($codigo_producto) && isset($cantidad_nueva)) {
        // Actualizar el inventario
        $sql = 'UPDATE inventario SET cantidad = cantidad + :cantidad_nueva WHERE codigo_producto = :codigo_producto';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['codigo_producto' => $codigo_producto, 'cantidad_nueva' => $cantidad_nueva]);

        // Redirigir a la página de inventario
        header('Location: ../view_inventory.php');
        exit();
    } else {
        // Manejar el caso de datos incompletos
        echo "Por favor, complete todos los campos.";
    }
} else {
    // Manejar el acceso incorrecto a la página
    echo "Acceso no permitido.";
}
?>
