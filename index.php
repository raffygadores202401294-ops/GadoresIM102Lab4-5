<?php
require_once 'config.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "
SELECT
    p.productID,
    p.productName,
    p.description,
    p.price,
    p.stock,
    c.categoryName AS category,
    s.supplierName AS supplier,
    p.created_at
FROM products p
JOIN categories c ON p.categoryID = c.categoryID
JOIN suppliers s ON p.supplierID = s.supplierID
WHERE 1=1
";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " AND (p.productName LIKE '%$search%' OR p.description LIKE '%$search%')";
}

if (!empty($category)) {
    $category = $conn->real_escape_string($category);
    $sql .= " AND c.categoryName = '$category'";
}

$sql .= " ORDER BY p.productID ASC";

$result = $conn->query($sql);

if (!$result) {
    die("SQL Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management System</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <h1>Inventory Management System</h1>

    <a href="add.php" class="add-btn">+ Add Product</a>

    <form method="GET">
        <input type="text" name="search"
            placeholder="Search product..."
            value="<?= htmlspecialchars($search) ?>">

        <select name="category">
            <option value="">All Categories</option>

            <?php
            $categories = $conn->query("SELECT DISTINCT categoryName FROM categories ORDER BY categoryName");
            while ($c = $categories->fetch_assoc()):
            ?>
                <option value="<?= $c['categoryName'] ?>"
                    <?= ($category == $c['categoryName']) ? 'selected' : '' ?>>
                    <?= $c['categoryName'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Filter</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
            <th>Action</th>
            <th>Supplier</th>
            <th>Created At</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>

                <tr class="<?= ($row['stock'] < 20) ? 'low-stock' : '' ?>">

                    <td><?= $row['productID']; ?></td>
                    <td><?= htmlspecialchars($row['productName']); ?></td>
                    <td><?= htmlspecialchars($row['description']); ?></td>
                    <td>₱<?= number_format($row['price'], 2); ?></td>
                    <td><?= $row['stock']; ?></td>
                    <td><?= htmlspecialchars($row['category']); ?></td>

                    <td>
                        <a href="edit.php?id=<?= $row['productID'] ?>" class="edit-btn">
                            Edit
                        </a>
                    </td>

                    <td><?= htmlspecialchars($row['supplier']); ?></td>
                    <td><?= $row['created_at']; ?></td>

                </tr>

            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" style="text-align:center;">No products found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <?php
    $statsSql = "
    SELECT
        COUNT(*) AS total_products,
        SUM(stock) AS total_stock,
        SUM(price * stock) AS total_value,
        SUM(CASE WHEN stock < 20 THEN 1 ELSE 0 END) AS low_stock
    FROM products p
    JOIN categories c ON p.categoryID = c.categoryID
    JOIN suppliers s ON p.supplierID = s.supplierID
    WHERE 1=1
    ";

    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $statsSql .= " AND (p.productName LIKE '%$search%' OR p.description LIKE '%$search%')";
    }

    if (!empty($category)) {
        $category = $conn->real_escape_string($category);
        $statsSql .= " AND c.categoryName = '$category'";
    }

    $stats = $conn->query($statsSql)->fetch_assoc();
    ?>

    <div class="stats">
        <p><b>Total Products:</b> <?= $stats['total_products'] ?></p>
        <p><b>Total Stock:</b> <?= $stats['total_stock'] ?></p>
        <p><b>Total Value:</b> ₱<?= number_format($stats['total_value'], 2) ?></p>
        <p><b>Low Stock Items:</b> <?= $stats['low_stock'] ?></p>
    </div>

</div>

</body>
</html>