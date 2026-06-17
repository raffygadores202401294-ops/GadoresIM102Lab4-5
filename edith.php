<?php
require_once 'config.php';

$id = (int) $_GET['id'];

$product = $conn->query("SELECT * FROM products WHERE productID = $id")->fetch_assoc();

$categories = $conn->query("SELECT categoryID, categoryName FROM categories ORDER BY categoryName");
$suppliers = $conn->query("SELECT supplierID, supplierName FROM suppliers ORDER BY supplierName");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $conn->real_escape_string($_POST['productName']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $categoryID = (int) $_POST['categoryID'];
    $supplierID = (int) $_POST['supplierID'];

    $sql = "
        UPDATE products SET
            productName='$name',
            description='$description',
            price=$price,
            stock=$stock,
            categoryID=$categoryID,
            supplierID=$supplierID
        WHERE productID=$id
    ";

    if ($conn->query($sql)) {
        header("Location: index.php");
        exit;
    } else {
        die("Error: " . $conn->error);
    }
}
?>

<h2>Edit Product</h2>

<form method="POST">

    <input type="text" name="productName"
        value="<?= htmlspecialchars($product['productName']) ?>" required><br><br>
    <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea><br><br>
    <input type="number" name="price" step="0.01"
        value="<?= $product['price'] ?>" required><br><br>
    <input type="number" name="stock"
        value="<?= $product['stock'] ?>" required><br><br>

    <select name="categoryID" required>
        <option value="">-- Select Category --</option>
        <?php while ($c = $categories->fetch_assoc()): ?>
            <option value="<?= $c['categoryID'] ?>"
                <?= $c['categoryID'] == $product['categoryID'] ? 'selected' : '' ?>>
                <?= $c['categoryName'] ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <select name="supplierID" required>
        <option value="">-- Select Supplier --</option>
        <?php while ($s = $suppliers->fetch_assoc()): ?>
            <option value="<?= $s['supplierID'] ?>"
                <?= $s['supplierID'] == $product['supplierID'] ? 'selected' : '' ?>>
                <?= $s['supplierName'] ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>

    <button type="submit">Update</button>
</form>