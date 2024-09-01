<?php
include 'sessions.php';
include 'db.php';

// Function to get the total number of products
function getTotalProducts($conn) {
    $sql = "SELECT COUNT(*) AS total FROM products";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Function to get the products with low stock
function getLowStockProducts($conn) {
    $lowStockProducts = [];
    $sql = "SELECT * FROM products WHERE available_stock < ideal_stock";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $lowStockProducts[] = $row;
    }
    return $lowStockProducts;
}

// Function to get the most bought product
function getMostBoughtProduct($conn) {
    $sql = "SELECT items.product_id, SUM(items.quantity) AS total_quantity, products.name, products.category, products.image
            FROM items
            JOIN products ON items.product_id = products.id
            GROUP BY items.product_id
            ORDER BY total_quantity DESC
            LIMIT 1";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Function to get the product sales data
function getProductSalesData($conn) {
    $productSales = [];
    $sql = "SELECT products.name, SUM(items.quantity) AS total_sales
            FROM items
            JOIN products ON items.product_id = products.id
            GROUP BY items.product_id";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $productSales[] = $row;
    }
    return $productSales;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
       body {
    font-family: 'Arial', sans-serif;
    background-color: #f5f5f5;
    color: #333;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-areas: 
        "header header"
        "sidebar main";
    grid-template-rows: auto 1fr;
    grid-template-columns: 250px 1fr;
    height: 100vh;
}

.fixed-header {
    grid-area: header;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: #fff;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1000;
}

#sidebar {
    grid-area: sidebar;
    background-color: #4b3832;
    padding: 20px;
    color: white;
    height: 100vh;
    position: fixed;
    top: 60px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    overflow-y: auto;
}

.main-content {
    grid-area: main;
    padding: 20px;
    margin-top: 60px; /* Matches the height of the fixed header */
    height: calc(100vh - 60px); /* Ensures the content fits within the available space */
    overflow-y: auto; /* Allows scrolling if content overflows */
}

.container {
    max-width: 100%;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dashboard-section {
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dashboard-section h2 {
    color: #4b3832;
}

.product-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.product-item img {
    width: 50px;
    height: 50px;
    border-radius: 4px;
}

.product-item .product-name {
    font-weight: bold;
}

.product-item .product-stock {
    color: #666;
}

.row {
    display: flex;
    align-items: center;
    gap: 20px;
}

.row img {
    max-width: 100px;
    border-radius: 8px;
}

.col-md-6 {
    flex: 1;
}

.card-img {
    width: 100%;
    border-radius: 8px;
}

canvas {
    max-width: 100%;
}


    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="main-content">
        <div class="container">
            <h1 class="text-center mb-4">Dashboard</h1>

            <div class="dashboard-section">
                <h2>Total Products</h2>
                <p id="totalProducts"><?php echo getTotalProducts($conn); ?></p>
            </div>

            <div class="dashboard-section">
                <h2>Low Stock Products</h2>
                <ul id="lowStockProducts">
                    <?php foreach (getLowStockProducts($conn) as $product): ?>
                        <li class="product-item">
                            <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                            <span class="product-name"><?php echo $product['name']; ?></span>
                            <span class="product-stock">(Available Stock: <?php echo $product['available_stock']; ?>)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="dashboard-section">
                <h2>Most Bought Product</h2>
                <div id="mostBoughtProduct">
                    <?php $mostBoughtProduct = getMostBoughtProduct($conn); ?>
                    <?php if ($mostBoughtProduct): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <img class="card-img" src="<?php echo $mostBoughtProduct['image']; ?>" alt="<?php echo $mostBoughtProduct['name']; ?>">
                            </div>
                            <div class="col-md-6">
                                <h3><?php echo $mostBoughtProduct['name']; ?></h3>
                                <p>Category: <?php echo $mostBoughtProduct['category']; ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>No orders found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dashboard-section">
                <h2>Product Sales</h2>
                <canvas id="productSalesChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Get the product sales data
            var productSalesData = <?php echo json_encode(getProductSalesData($conn)); ?>;
            var ctx = document.getElementById('productSalesChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: productSalesData.map(item => item.name),
                    datasets: [{
                        label: 'Sales',
                        data: productSalesData.map(item => item.total_sales),
                        backgroundColor: 'rgba(75, 57, 50, 0.2)',
                        borderColor: 'rgba(75, 57, 50, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        function logoutUser() {
            // Make an Ajax request to handle the logout process
            $.ajax({
                url: 'logout.php',
                type: 'POST',
                success: function() {
                    // Redirect the user to the login page
                    window.location.href = 'login.php';
                }
            });
        }
    </script>

    <?php $conn->close(); ?>
</body>
</html>
