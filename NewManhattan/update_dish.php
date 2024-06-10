<?php
include 'connection.php';

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$precio = $_POST['precio'];
$contenido = $_POST['contenido'];

$sql = "UPDATE platos SET nombre='$nombre', precio='$precio', contenido='$contenido' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header('Location: index.php');
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
