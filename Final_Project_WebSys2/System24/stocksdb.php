<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['id'])) {
        $action = $_POST['action'];
        $productId = (int)$_POST['id'];

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

        if ($action === 'increment') {
            $stmt = $pdo->prepare('UPDATE products SET available_stock = available_stock + 1 WHERE id = :id');
        } elseif ($action === 'decrement') {
            // Ensure stock does not go below zero
            $stmt = $pdo->prepare('UPDATE products SET available_stock = GREATEST(0, available_stock - 1) WHERE id = :id');
        } else {
            echo "Invalid action";
            exit;
        }

        $stmt->execute(['id' => $productId]);

        // Fetch the updated available_stock to return
        $stmt = $pdo->prepare('SELECT available_stock FROM products WHERE id = :id');
        $stmt->execute(['id' => $productId]);
        $result = $stmt->fetch();

        if ($result) {
            echo $result['available_stock'];
        } else {
            echo "Product not found";
        }
        exit;
    }
    echo "Invalid request";
}
?>
