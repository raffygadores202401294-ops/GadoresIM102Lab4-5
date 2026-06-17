<?php
include 'config.php';

$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$suppliers = $conn->query("SELECT id, name FROM suppliers ORDER BY name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $category_id = (int) $_POST['category_id'];
    $supplier_id = (int) $_POST['supplier_id'];

    $sql = "
        INSERT INTO products (name, description, price, stock, category_id, supplier_id)
        VALUES ('$name', '$description', $price, $stock, $category_id, $supplier_id)
    ";

    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit;
}
?><!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">
    <h2>Add Product</h2>

    <form method="POST" class="form-box">

        <input type="text" name="name" placeholder="Product Name" required>

        <textarea name="description" placeholder="Description" required></textarea>

        <input type="number" name="price" placeholder="Price" required>

        <input type="number" name="stock" placeholder="Stock" required>

        <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>">
                    <?= $cat['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="supplier_id" required>
            <option value="">-- Select Supplier --</option>
            <?php while ($sup = $suppliers->fetch_assoc()): ?>
                <option value="<?= $sup['id'] ?>">
                    <?= $sup['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Save Product</button>

    </form>
</div>

</body>
</html>