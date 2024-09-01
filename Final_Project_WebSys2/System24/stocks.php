<?php
include 'sessions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        #wrapper {
            display: flex;
        }
        #main-content {
            flex: 1;
            padding: 20px;
        }
        h1 {
            color: #4b3832;
        }
        #searchBar {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
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
        .button {
            background-color: #4b3832;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        .button.red {
            background-color: peru;
        }
        img {
            border-radius: 8px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Search functionality
            $("#searchBar").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#stockTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Increment stock
            $(".increment").click(function() {
                var productId = $(this).data('id');
                $.ajax({
                    url: 'stocksdb.php',
                    type: 'POST',
                    data: { action: 'increment', id: productId },
                    success: function(response) {
                        if (!isNaN(response)) {
                            $("#stock-" + productId).text(response);
                        } else {
                            alert(response);
                        }
                    }
                });
            });

            // Decrement stock
            $(".decrement").click(function() {
                var productId = $(this).data('id');
                $.ajax({
                    url: 'stocksdb.php',
                    type: 'POST',
                    data: { action: 'decrement', id: productId },
                    success: function(response) {
                        if (!isNaN(response)) {
                            $("#stock-" + productId).text(response);
                        } else {
                            alert(response);
                        }
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        <div id="main-content">
            <h1>Stock Management</h1>
            <input type="text" id="searchBar" placeholder="Search for products...">
            <table id="stockTable">
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Available Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
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

                    $stmt = $pdo->query('SELECT * FROM products');
                    while ($row = $stmt->fetch()) {
                        echo "<tr>
                                <td><img src='" . htmlspecialchars($row['image']) . "' width='50' height='50'></td>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>" . htmlspecialchars($row['category']) . "</td>
                                <td id='stock-" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['available_stock']) . "</td>
                                <td>
                                    <button class='button increment' data-id='" . htmlspecialchars($row['id']) . "'>+</button>
                                    <button class='button red decrement' data-id='" . htmlspecialchars($row['id']) . "'>-</button>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
