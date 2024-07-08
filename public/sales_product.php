<?php
require '../config/config.php';

// Obtener inventario
$sql = 'SELECT i.codigo_producto, p.nombre, p.precio, i.cantidad 
        FROM inventario i
        JOIN producto p ON i.codigo_producto = p.codigo_producto
        WHERE i.cantidad > 0';
$stmt = $pdo->query($sql);
$productos = $stmt->fetchAll();

// Obtener IVA
$sqlIva = 'SELECT iva_porcentaje FROM iva WHERE iva_id = 1'; // Ajusta el WHERE según corresponda
$stmtIva = $pdo->query($sqlIva);
$ivaData = $stmtIva->fetch();
$ivaPorcentaje = $ivaData['iva_porcentaje'] / 100;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vender Productos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        #productList {
            max-height: 400px;
            overflow-y: auto;
        }
        .card {
            padding: 10px;
            margin: 5px 0;
        }
        .card-body {
            padding: 10px;
        }
        .card-title, .card-text {
            margin-bottom: 5px;
        }
        .cantidad-input {
            width: 60px;
        }
        .add-to-list {
            padding: 5px 10px;
        }
        .remove-from-list {
            cursor: pointer;
            color: red;
        }
    </style>
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    <div class="container mt-4">
        <h1>Vender Productos</h1>
        <div class="form-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Buscar producto...">
        </div>
        <div id="productList" class="mb-3">
            <?php foreach ($productos as $item): ?>
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($item['nombre']) ?></h5>
                        <p class="card-text">Precio: $<?= htmlspecialchars($item['precio']) ?> (sin IVA)</p>
                        <p class="card-text">Precio: $<?= htmlspecialchars($item['precio'] * (1 + $ivaPorcentaje)) ?> (con IVA)</p>
                        <p class="card-text">Cantidad disponible: <?= htmlspecialchars($item['cantidad']) ?></p>
                        <div class="input-group mb-1">
                            <input type="number" class="form-control cantidad-input" min="1" max="<?= htmlspecialchars($item['cantidad']) ?>" placeholder="Cantidad">
                            <div class="input-group-append">
                                <button class="btn btn-primary add-to-list" data-id="<?= $item['codigo_producto'] ?>" data-name="<?= htmlspecialchars($item['nombre']) ?>" data-price="<?= htmlspecialchars($item['precio']) ?>" data-price-iva="<?= htmlspecialchars($item['precio'] * (1 + $ivaPorcentaje)) ?>" data-max="<?= htmlspecialchars($item['cantidad']) ?>">Agregar</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <hr>
        <h2>Lista de Productos</h2>
        <ul id="selectedProducts" class="list-group">
            <!-- Aquí se agregarán los productos seleccionados -->
        </ul>
        <form id="invoiceForm">
            <div class="form-group">
                <label for="customerName">Nombre del Cliente</label>
                <input type="text" id="customerName" name="customerName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="customerCedula">Cédula del Cliente</label>
                <input type="text" id="customerCedula" name="customerCedula" class="form-control" required>
            </div>
            <button id="generateReceipt" class="btn btn-success mt-3">Generar Factura</button>
        </form>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Filtrar productos al escribir en el campo de búsqueda
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#productList .card').filter(function() {
                    $(this).toggle($(this).find('.card-title').text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Agregar producto a la lista
            $('.add-to-list').on('click', function() {
                var productId = $(this).data('id');
                if ($('#product_' + productId).length > 0) {
                    alert('El producto ya está en la lista.');
                    return;
                }

                var productName = $(this).data('name');
                var productPrice = $(this).data('price');
                var productPriceIva = $(this).data('price-iva');
                var quantity = $(this).closest('.input-group').find('.cantidad-input').val();
                var maxQuantity = $(this).data('max');

                if (quantity > 0 && quantity <= maxQuantity) {
                    var listItem = '<li class="list-group-item d-flex justify-content-between align-items-center" id="product_' + productId + '">';
                    listItem += '<div class="product-info" data-id="' + productId + '" data-name="' + productName + '" data-price="' + productPrice + '" data-price-iva="' + productPriceIva + '" data-quantity="' + quantity + '">';
                    listItem += productName + ' - Cantidad: ' + quantity + ' - Precio: $' + productPrice + ' (sin IVA) / $' + productPriceIva + ' (con IVA)';
                    listItem += '<span class="badge badge-primary badge-pill">Código: ' + productId + '</span>';
                    listItem += '</div>';
                    listItem += '<span class="remove-from-list ml-2" data-id="' + productId + '">Quitar</span>';
                    listItem += '</li>';

                    $('#selectedProducts').append(listItem);
                } else {
                    alert('Por favor, ingrese una cantidad válida.');
                }
            });

            // Quitar producto de la lista
            $(document).on('click', '.remove-from-list', function() {
                var productId = $(this).data('id');
                $('#product_' + productId).remove();
            });

            // Generar factura
            $('#generateReceipt').on('click', function(e) {
                e.preventDefault();

                var selectedProducts = [];
                $('#selectedProducts .product-info').each(function() {
                    var productId = $(this).data('id');
                    var productName = $(this).data('name');
                    var quantity = $(this).data('quantity');
                    var price = $(this).data('price');
                    var priceIva = $(this).data('price-iva');

                    selectedProducts.push({
                        productId: productId,
                        productName: productName,
                        quantity: quantity,
                        price: price,
                        priceIva: priceIva
                    });
                });

                var customerName = $('#customerName').val();
                var customerCedula = $('#customerCedula').val();

                if (selectedProducts.length > 0 && customerName && customerCedula) {
                    $.ajax({
                        url: 'Sales/generate_receipt.php',
                        type: 'POST',
                        data: {
                            products: selectedProducts,
                            customerName: customerName,
                            customerCedula: customerCedula
                        },
                        success: function(response) {
                            console.log("llegue aqui")
                            window.location.href = "../sales_product.php";
                        },
                        error: function() {
                            alert('Error al conectar con el servidor.');
                        }
                    });
                } else {
                    alert('Por favor, complete todos los campos y seleccione al menos un producto.');
                }
            });
        });
    </script>
</body>
</html>
