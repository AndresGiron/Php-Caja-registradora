<?php
require '../config/config.php';

// Consulta para obtener el inventario
$sql = 'SELECT i.codigo_producto, p.nombre, i.cantidad 
        FROM inventario i
        JOIN producto p ON i.codigo_producto = p.codigo_producto';
$stmt = $pdo->query($sql);
$inventario = $stmt->fetchAll();

function estadoInventario($cantidad)
{
    if ($cantidad <= 0) {
        return 'Agotado';
    } else {
        return 'En stock';
    }
}

// Filtrado de productos por nombre
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT i.codigo_producto, p.nombre, i.cantidad 
            FROM inventario i
            JOIN producto p ON i.codigo_producto = p.codigo_producto
            WHERE p.nombre LIKE '%$search%'";
    $stmt = $pdo->query($sql);
    $inventario = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<?php include 'components/navbar.php'; ?>
<body>

    <div class="container mt-4">
        <h1>Inventario</h1>
        <form class="form-inline mb-4">
            <input class="form-control mr-sm-2" type="search" placeholder="Buscar por nombre" aria-label="Buscar" name="search">
            <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Buscar</button>
        </form>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Código de Producto</th>
                    <th>Nombre</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Agregar Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventario as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['codigo_producto']) ?></td>
                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                        <td><?= htmlspecialchars($item['cantidad']) ?></td>
                        <td><?= estadoInventario($item['cantidad']) ?></td>
                        <td>
                            <form action="Inventory/actualizar_inventario.php" method="POST">
                                <input type="hidden" name="codigo_producto" value="<?= $item['codigo_producto'] ?>">
                                <div class="input-group">
                                    <input type="number" name="cantidad_nueva" class="form-control" min="0" value="0">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">Actualizar</button>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Scripts de Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
