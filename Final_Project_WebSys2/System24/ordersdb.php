<?php
header('Content-Type: application/json');

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
    error_log('Database connection failed: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'getProducts') {
        try {
            $stmt = $pdo->query('SELECT * FROM products');
            $products = $stmt->fetchAll();
            echo json_encode($products);
        } catch (Exception $e) {
            error_log('Error fetching products: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error fetching products']);
        }
    } elseif ($action === 'getProductById' && isset($_GET['id'])) {
        $productId = (int)$_GET['id'];
        try {
            $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            echo json_encode($product);
        } catch (Exception $e) {
            error_log('Error fetching product by ID: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error fetching product']);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'confirmOrder') {
        $orderDetails = json_decode(file_get_contents('php://input'), true);
    
        if (!$orderDetails) {
            error_log('Invalid input data received');
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }
    
        // Add logging
        error_log('Order details: ' . print_r($orderDetails, true));
    
        $cart = $orderDetails['cart'];
        $totalBill = $orderDetails['totalBill'];
        $customerPayment = $orderDetails['customerPayment'];
        $change = $orderDetails['change'];
    
        try {
            $pdo->beginTransaction();
    
            // Update inventory stock
            foreach ($cart as $item) {
                $stmt = $pdo->prepare('UPDATE products SET available_stock = available_stock - ? WHERE id = ?');
                $stmt->execute([(int)$item['quantity'], (int)$item['id']]);
            }
    
            // Save order to orders table
            $stmt = $pdo->prepare('INSERT INTO orders (totalBill, customerPayment, changeAmount) VALUES (?, ?, ?)');
            $stmt->execute([$totalBill, $customerPayment, $change]);
            $orderId = $pdo->lastInsertId();
    
            // Save order items to items table
            foreach ($cart as $item) {
                $stmt = $pdo->prepare('INSERT INTO items (order_id, product_id, quantity) VALUES (?, ?, ?)');
                $stmt->execute([$orderId, (int)$item['id'], (int)$item['quantity']]);
            }
    
            $pdo->commit();
    
            $response = [
                'success' => true,
                'totalBill' => $totalBill,
                'customerPayment' => $customerPayment,
                'change' => $change,
                'cart' => $cart
            ];
    
            echo json_encode($response);
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('Order confirmation failed: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Order confirmation failed: ' . $e->getMessage()]);
        }
    }
}
?>
