<?php
// Obtener usuario  
$current_user = wp_get_current_user();
$username = $current_user->user_login;

// Uso para obtener 
$stock_controller = new Warehouses_Controller($username);
$stock = $stock_controller->get_stock_by_warehouse();
$picking_data = $stock_controller->get_requested_stock();
?>

<h2 class="stock-panel-title">Solicitar Productos</h2>
<table class="stock-table stock-table-1">
    <thead>
        <tr>
            <th>Nombre del Producto</th>
            <th>Cantidad (en stock)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stock as $producto): ?>
            <tr>
                <td><?php echo htmlspecialchars($producto['product_name']); ?></td>
                <td><?php echo htmlspecialchars($producto['quantity']); ?></td>
                <td>
                    <form class="ajax-form" data-action="request_stock" data-method="POST">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($producto['product_id']); ?>">
                    <input type="number" name="quantity" placeholder="Cantidad" required>
                    <button type="submit">Pedir stock</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2 class="stock-panel-title">Productos en Camino</h2>
<table class="stock-table stock-table-2">
    <thead>
        <tr>
            <th>Nombre del Producto</th>
            <th>Cantidad</th>
            <th>Destino</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($picking_data as $picking): ?>
            <tr>
                <td><?php echo htmlspecialchars($picking['product_name']); ?></td>
                <td><?php echo htmlspecialchars($picking['quantity']); ?></td>
                <td><?php echo htmlspecialchars($picking['location_dest_id']); ?></td>
                <td id="<?php echo htmlspecialchars($picking['state']); ?>"><p><?php echo htmlspecialchars($picking['state']); ?></p></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>