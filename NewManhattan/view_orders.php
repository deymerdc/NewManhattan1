<?php
include 'connection.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos en Tiempo Real</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Pedidos</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plato</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody id="orders">
                <?php
                $sql = "SELECT * FROM pedidos";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['plato']}</td>";
                    echo "<td>{$row['cantidad']}</td>";
                    echo "<td>{$row['estado']}</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="index.php">Volver</a>
    </div>

    <script>
        const ordersContainer = document.getElementById('orders');

        function addOrder(order) {
            const orderRow = document.createElement('tr');
            orderRow.innerHTML = `
                <td>${order.id}</td>
                <td>${order.plato}</td>
                <td>${order.cantidad}</td>
                <td>${order.estado}</td>
            `;
            ordersContainer.appendChild(orderRow);
        }

        window.addEventListener('storage', (event) => {
            if (event.key === 'order_update') {
                const order = JSON.parse(event.newValue);
                addOrder(order);
            }
        });

        window.addEventListener('load', () => {
            const previousOrders = JSON.parse(localStorage.getItem('order_history')) || [];
            previousOrders.forEach((order) => addOrder(order));
        });

        new MutationObserver(() => {
            const orders = Array.from(ordersContainer.children).map(row => ({
                id: row.children[0].textContent,
                plato: row.children[1].textContent,
                cantidad: row.children[2].textContent,
                estado: row.children[3].textContent
            }));
            localStorage.setItem('order_history', JSON.stringify(orders));
        }).observe(ordersContainer, { childList: true });
    </script>
</body>
</html>

<?php
$conn->close();
?>
