<?php
function getCategories() {
    return ['Coffee', 'Frappe', 'Creamery', 'Fruity Soda'];
}

function getInventoryData($selectedCategory = null) {
    $dsn = 'mysql:host=127.0.0.1;dbname=cnmims;charset=utf8mb4';
    $username = 'root'; // replace with your database username
    $password = ''; // replace with your database password
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }

    $query = 'SELECT * FROM products';
    if ($selectedCategory) {
        $query .= ' WHERE category = :category';
    }

    $stmt = $pdo->prepare($query);
    if ($selectedCategory) {
        $stmt->execute(['category' => $selectedCategory]);
    } else {
        $stmt->execute();
    }

    $inventory = $stmt->fetchAll();
    return $inventory;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    $dsn = 'mysql:host=127.0.0.1;dbname=cnmims;charset=utf8mb4';
    $username = 'root'; // replace with your database username
    $password = ''; // replace with your database password
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }

    if ($action === 'add') {
        $stmt = $pdo->prepare('INSERT INTO products (name, category, purchase_price, retail_price, ideal_stock, available_stock, image) VALUES (:name, :category, :purchase_price, :retail_price, :ideal_stock, :available_stock, :image)');
        $imageName = 'images/' . basename($_FILES['productImage']['name']);
        move_uploaded_file($_FILES['productImage']['tmp_name'], $imageName);

        $stmt->execute([
            'name' => $_POST['productName'],
            'category' => $_POST['productCategory'],
            'purchase_price' => $_POST['purchasePrice'],
            'retail_price' => $_POST['retailPrice'],
            'ideal_stock' => $_POST['idealStock'],
            'available_stock' => $_POST['availableStock'],
            'image' => $imageName,
        ]);

        header('Location: inventory.php');
    } elseif ($action === 'edit') {
        $stmt = $pdo->prepare('UPDATE products SET name = :name, category = :category, purchase_price = :purchase_price, retail_price = :retail_price, ideal_stock = :ideal_stock, available_stock = :available_stock, image = :image WHERE id = :id');

        $imageName = $_POST['existingImage'];
        if (!empty($_FILES['productImage']['name'])) {
            $imageName = 'images/' . basename($_FILES['productImage']['name']);
            move_uploaded_file($_FILES['productImage']['tmp_name'], $imageName);
        }

        $stmt->execute([
            'name' => $_POST['productName'],
            'category' => $_POST['productCategory'],
            'purchase_price' => $_POST['purchasePrice'],
            'retail_price' => $_POST['retailPrice'],
            'ideal_stock' => $_POST['idealStock'],
            'available_stock' => $_POST['availableStock'],
            'image' => $imageName,
            'id' => $_POST['productId'],
        ]);

        header('Location: inventory.php');
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $dsn = 'mysql:host=127.0.0.1;dbname=cnmims;charset=utf8mb4';
    $username = 'root'; // replace with your database username
    $password = ''; // replace with your database password
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }

    $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute(['id' => (int) $_GET['id']]);

    header('Location: inventory.php');
}
?>