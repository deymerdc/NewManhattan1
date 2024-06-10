<?php
include 'connection.php';

$plato_id = $_POST['plato_id'];
$cantidad = $_POST['cantidad'];

$sql = "INSERT INTO pedidos (plato_id, cantidad) VALUES ('$plato_id', '$cantidad')";

if ($conn->query($sql) === TRUE) {
    $plato_sql = "SELECT nombre FROM platos WHERE id=$plato_id";
    $plato_result = $conn->query($plato_sql);
    $plato_row = $plato_result->fetch_assoc();
    $plato_nombre = $plato_row['nombre'];

    $order = json_encode([
        'id' => $conn->insert_id,
        'plato' => $plato_nombre,
        'cantidad' => $cantidad,
        'estado' => 'pendiente'
    ]);

    echo "<script>
        localStorage.setItem('order_update', '$order');
        window.location.href = 'index.php';
    </script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
