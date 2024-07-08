<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (ob_get_contents()) {
    ob_end_clean();
}

require '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $products = $_POST['products'];
    $customerName = $_POST['customerName'];
    $customerCedula = $_POST['customerCedula'];

    try {
        $pdo->beginTransaction();

        $sqlFactura = 'INSERT INTO factura (total, nombre_cliente, cedula_cliente) VALUES (:total, :nombre_cliente, :cedula_cliente)';
        $stmtFactura = $pdo->prepare($sqlFactura);
        
        $total = array_reduce($products, function($sum, $item) {
            return $sum + ($item['priceIva'] * $item['quantity']);
        }, 0);

        // Ejecutar la inserción de la factura
        $stmtFactura->execute([
            'total' => $total,
            'nombre_cliente' => $customerName,
            'cedula_cliente' => $customerCedula
        ]);

        // Obtener el ID de la factura insertada
        $facturaId = $pdo->lastInsertId();

        // Insertar las ventas y actualizar el inventario
        $sqlVenta = 'INSERT INTO venta (codigo_producto, factura_id, cantidad, total_a_pagar, total_a_pagar_con_iva) VALUES (:codigo_producto, :factura_id, :cantidad, :total_a_pagar, :total_a_pagar_con_iva)';
        $stmtVenta = $pdo->prepare($sqlVenta);

        // Actualizar el inventario
        $sqlInventario = 'UPDATE inventario SET cantidad = cantidad - :cantidad WHERE codigo_producto = :codigo_producto';
        $stmtInventario = $pdo->prepare($sqlInventario);

        foreach ($products as $product) {
            // Ejecutar la venta
            $stmtVenta->execute([
                'codigo_producto' => $product['productId'],
                'factura_id' => $facturaId,
                'cantidad' => $product['quantity'],
                'total_a_pagar' => $product['price'] * $product['quantity'],
                'total_a_pagar_con_iva' => $product['priceIva'] * $product['quantity']
            ]);

            // Actualizar el inventario
            $stmtInventario->execute([
                'codigo_producto' => $product['productId'],
                'cantidad' => $product['quantity']
            ]);
        }

        if ($pdo->inTransaction()) {
            $pdo->commit();
        }

        // Generar el PDF de la factura 
        require_once('../../vendor/setasign/fpdf/fpdf.php'); 

        $pdf = new FPDF();
        $pdf->AddPage();

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(40, 10, 'Factura');
        $pdf->Ln(10);

        // Información del Cliente
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, "Nombre del Cliente: $customerName");
        $pdf->Ln();
        $pdf->Cell(0, 10, "Cédula del Cliente: $customerCedula");
        $pdf->Ln(15);

        // Detalle de Productos
        $pdf->SetFont('Arial', 'B', 12);
        foreach ($products as $product) {
            $pdf->Cell(0, 5, 'Código: ' . $product['productId']);
            $pdf->Ln();
            $pdf->Cell(0, 5, 'Nombre: ' . $product['productName']);
            $pdf->Ln();
            $pdf->Cell(0, 5, 'Cantidad: ' . $product['quantity']);
            $pdf->Ln();
            $pdf->Cell(0, 5, 'Precio (sin IVA): $' . number_format($product['price'], 2));
            $pdf->Ln();
            $pdf->Cell(0, 5, 'Precio (con IVA): $' . number_format($product['priceIva'], 2));
            $pdf->Ln();
            $pdf->Cell(0, 5, 'Total: $' . number_format($product['priceIva'] * $product['quantity'], 2));
            $pdf->Ln(15);
        }

        // Total a pagar
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, "Total a pagar: $" . number_format($total, 2));

        // Generar el nombre del archivo PDF
        $currentDateTime = date('Ymd_His');
        $pdfFileName = $customerCedula . '_' . $currentDateTime . '.pdf';

        echo '<script>
        console.log("antes")
    </script>';
        $pdf->Output("../pdf/$pdfFileName", 'F');
        echo '<script>
            console.log("despues")
        </script>';

        echo '<script>
            alert("Factura generada exitosamente.");
            window.location.href = "../sales_product.php";
        </script>';


    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo 'Error: ' . $e->getMessage();
    }

} else {
    echo 'Método de solicitud no permitido.';
}
?>
