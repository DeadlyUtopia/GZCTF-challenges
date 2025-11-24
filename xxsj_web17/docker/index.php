<?php
$dbPath = '/var/ctf/sqlite/ctf.db';

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT product_id, name, sku, price, image FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>VC SHOP</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: center; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; margin-top: 30px; }
        .product-img { width: 100px; height: auto; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>产品展示列表</h1>
    <table>
        <tr>
            <th>产品ID</th>
            <th>名称</th>
            <th>编码</th>
            <th>价格（元）</th>
            <th>产品图片</th>
        </tr>
        <?php foreach($products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['product_id']); ?></td>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['sku']); ?></td>
            <td><?php echo htmlspecialchars($product['price']); ?></td>
            <td><img src="<?php echo htmlspecialchars($product['image']); ?>" class="product-img" alt="产品图片"></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>