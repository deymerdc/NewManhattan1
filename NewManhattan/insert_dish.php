<?php
include 'connection.php';

$nombre = $_POST['nombre'];
$precio = $_POST['precio'];
$contenido = $_POST['contenido'];

$sql = "INSERT INTO platos (nombre, precio, contenido) VALUES ('$nombre', '$precio', '$contenido')";

if ($conn->query($sql) === TRUE) {
    header('Location: index.php');
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
