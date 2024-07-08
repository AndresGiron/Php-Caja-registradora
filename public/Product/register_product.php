<?php
require '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    if (!empty($nombre) && !empty($precio)) {
        try {
            // Iniciar una transacción
            $pdo->beginTransaction();

            // Insertar el nuevo producto
            $sqlProducto = 'INSERT INTO producto (nombre, precio) VALUES (:nombre, :precio)';
            $stmtProducto = $pdo->prepare($sqlProducto);
            $stmtProducto->execute(['nombre' => $nombre, 'precio' => $precio]);

            // Obtener el código del producto recién insertado
            $codigo_producto = $pdo->lastInsertId('producto_codigo_producto_seq');

            // Insertar la fila en el inventario con cantidad 0
            $sqlInventario = 'INSERT INTO inventario (codigo_producto, cantidad) VALUES (:codigo_producto, :cantidad)';
            $stmtInventario = $pdo->prepare($sqlInventario);
            $stmtInventario->execute(['codigo_producto' => $codigo_producto, 'cantidad' => 0]);

            // Confirmar la transacción
            $pdo->commit();

            // Mostrar mensaje de éxito con JavaScript y redirigir
            echo "<script>alert('Producto registrado exitosamente.'); window.location.href = '../register_product_form.php';</script>";
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $pdo->rollBack();
            echo "Error al registrar el producto: " . $e->getMessage();
        }
    } else {
        echo "Por favor, complete todos los campos.";
    }
}
?>
