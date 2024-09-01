<?php
include 'sessions.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Purchase History</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    color: #333;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: 250px 1fr;
    grid-template-rows: 60px 1fr;
    grid-template-areas:
        "sidebar header"
        "sidebar content";
    height: 100vh;
}

#sidebar {
    grid-area: sidebar;
    background-color: #4b3832;
    padding: 20px;
    color: white;
    height: calc(100vh - 60px);
    position: sticky;
    top: 60px;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.fixed-header {
    grid-area: header;
    background-color: #fff;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1000; /* Ensure it's above other elements */
}

.main {
    grid-area: content;
    padding: 20px;
}

h1, h2 {
    color: #444;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f8f8f8;
    font-weight: bold;
}

tr:hover {
    background-color: #f1f1f1;
}
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="main">
        <h1>Purchase History</h1>
        <table>
            <thead>
                <tr>
                    <th>Total Bill</th>
                    <th>Customer Payment</th>
                    <th>Change</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection details
                $dsn = 'mysql:host=127.0.0.1;dbname=cnmims;charset=utf8mb4';
                $username = 'root'; // Replace with your database username
                $password = ''; // Replace with your database password
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ];

                try {
                    $pdo = new PDO($dsn, $username, $password, $options);
                } catch (PDOException $e) {
                    echo 'Database connection failed: ' . $e->getMessage();
                    exit;
                }

                try {
                    $stmt = $pdo->query('SELECT
                                            o.totalBill,
                                            o.customerPayment,
                                            o.changeAmount,
                                            GROUP_CONCAT(CONCAT(p.name, " (", i.quantity, ") - $", p.retail_price * i.quantity) SEPARATOR "<br>") AS items
                                         FROM
                                            orders o
                                         JOIN
                                            items i ON o.id = i.order_id
                                         JOIN
                                            products p ON i.product_id = p.id
                                         GROUP BY
                                            o.id');

                    if ($stmt->rowCount() > 0) {
                        foreach ($stmt->fetchAll() as $row) {
                            echo '<tr>';
                            echo '<td>' . $row['totalBill'] . '</td>';
                            echo '<td>' . $row['customerPayment'] . '</td>';
                            echo '<td>' . $row['changeAmount'] . '</td>';
                            echo '<td>' . $row['items'] . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="4">No purchase history available.</td></tr>';
                    }
                } catch (PDOException $e) {
                    echo 'Error retrieving purchase history: ' . $e->getMessage();
                }

                $pdo = null;
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>