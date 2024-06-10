<?php
include 'connection.php';

$id = $_GET['id'];
$sql = "SELECT * FROM platos WHERE id=$id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Plato</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Editar Plato</h1>
        <form action="update_dish.php" method="post">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" required>
            <input type="number" name="precio" value="<?php echo $row['precio']; ?>" required step="0.01">
            <textarea name="contenido" required><?php echo $row['contenido']; ?></textarea>
            <input type="submit" value="Actualizar">
        </form>
        <a href="index.php">Volver</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>
