<?php
include 'connection.php';

$sql = "SELECT * FROM platos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Manhattan - Gestión de Platos</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        /* Estilos adicionales para el chat y pedidos */
        .container { margin: 0 auto; max-width: 800px; padding: 20px; }
        #chat-container { margin-top: 20px; }
        #orders { margin-top: 20px; border: 1px solid #ccc; padding: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Platos</h1>
        <form action="insert_dish.php" method="post">
            <input type="text" name="nombre" placeholder="Nombre del Plato" required>
            <input type="number" name="precio" placeholder="Precio" required step="0.01">
            <textarea name="contenido" placeholder="Contenido del Plato" required></textarea>
            <input type="submit" value="Agregar">
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Contenido</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['precio']; ?></td>
                    <td><?php echo $row['contenido']; ?></td>
                    <td class="actions">
                        <a href="edit_dish.php?id=<?php echo $row['id']; ?>">Editar</a>
                        <a href="delete_dish.php?id=<?php echo $row['id']; ?>">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <h2>Realizar Pedido</h2>
        <form action="place_order.php" method="post">
            <select name="plato_id" required>
                <?php
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
                }
                ?>
            </select>
            <input type="number" name="cantidad" placeholder="Cantidad" required>
            <input type="submit" value="Realizar Pedido">
        </form>
        <h2>Pedidos</h2>
        <div id="orders"></div>
        
        <a href="view_orders.php" target="_blank">Ver Pedidos en Tiempo Real</a>
    </div>

    <div id="chat-container">
        <h1>Chat entre pestañas</h1>
        <div id="chat"></div>
        <input type="text" id="message" placeholder="Escribe tu mensaje...">
        <button id="send">Enviar</button>
    </div>

    <script>
        const chat = document.getElementById('chat');
        const messageInput = document.getElementById('message');
        const sendButton = document.getElementById('send');
        const ordersContainer = document.getElementById('orders');

        const generateRandomColor = () => {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        };

        const tabColor = generateRandomColor();

        function addMessage(message, color, fromLocalStorage = false) {
            const messageElement = document.createElement('div');
            messageElement.textContent = message;
            messageElement.style.backgroundColor = color;
            messageElement.className = 'message';
            chat.appendChild(messageElement);
            chat.scrollTop = chat.scrollHeight;

            if (!fromLocalStorage) {
                localStorage.setItem('chat_message', JSON.stringify({ message, color }));
            }
        }

        function addOrder(order, fromLocalStorage = false) {
            const orderElement = document.createElement('div');
            orderElement.textContent = `Pedido #${order.id} - Plato: ${order.plato} - Cantidad: ${order.cantidad} - Estado: ${order.estado}`;
            orderElement.className = 'order';
            ordersContainer.appendChild(orderElement);

            addMessage(`Nuevo pedido realizado: Plato - ${order.plato}, Cantidad - ${order.cantidad}`, tabColor, fromLocalStorage);

            if (!fromLocalStorage) {
                localStorage.setItem('order_update', JSON.stringify(order));
            }
        }

        window.addEventListener('storage', (event) => {
            if (event.key === 'chat_message') {
                const { message, color } = JSON.parse(event.newValue);
                addMessage(message, color, true);
            }

            if (event.key === 'order_update') {
                const order = JSON.parse(event.newValue);
                addOrder(order, true);
            }
        });

        sendButton.addEventListener('click', () => {
            const message = messageInput.value;
            if (message.trim()) {
                addMessage(message, tabColor);
                messageInput.value = '';
            }
        });

        messageInput.addEventListener('keypress', (event) => {
            if (event.key === 'Enter') {
                sendButton.click();
            }
        });

        window.addEventListener('load', () => {
            const previousMessages = JSON.parse(localStorage.getItem('chat_history')) || [];
            previousMessages.forEach((msg) => addMessage(msg.message, msg.color, true));

            const previousOrders = JSON.parse(localStorage.getItem('order_history')) || [];
            previousOrders.forEach((order) => addOrder(order, true));
        });

        new MutationObserver(() => {
            const messages = Array.from(chat.children).map(child => ({
                message: child.textContent,
                color: child.style.backgroundColor
            }));
            localStorage.setItem('chat_history', JSON.stringify(messages));

            const orders = Array.from(ordersContainer.children).map(child => {
                const [id, plato, cantidad, estado] = child.textContent.split(' - ');
                return {
                    id: id.replace('Pedido #', ''),
                    plato: plato.replace('Plato: ', ''),
                    cantidad: cantidad.replace('Cantidad: ', ''),
                    estado: estado.replace('Estado: ', '')
                };
            });
            localStorage.setItem('order_history', JSON.stringify(orders));
        }).observe(chat, { childList: true });
    </script>
</body>
</html>

<?php
$conn->close();
?>
