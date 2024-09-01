<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Navigation</title>
    <style>
        /* CSS styles for the layout */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-rows: auto 1fr;
            grid-template-columns: 250px 1fr;
            height: 100vh;
            background-color: #f5f5f5;
        }

        .fixed-header {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000; /* Ensure it's above other elements */
        }

        

        .fixed-header .title {
            font-size: 24px;
            font-weight: bold;
            color: #4b3832;
        }

        .fixed-header .user-info {
            display: flex;
            align-items: center;
        }

        .fixed-header .user-info .username {
            margin-right: 10px;
            font-size: 18px;
            color: #333;
        }

        .fixed-header .user-info .notifications {
            font-size: 24px;
            cursor: pointer;
            color: #4b3832;
        }

        #sidebar {
            grid-row: 2 / 3;
            background-color: #4b3832;
            padding: 20px;
            color: white;
            height: calc(100vh - 60px);
            position: sticky;
            top: 60px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        #sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        #sidebar li {
            margin-bottom: 20px;
        }

        #sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            border-radius: 4px;
            font-size: 18px;
        }

        #sidebar a:hover {
            background-color: #7b5e57;
        }

        .main-content {
            grid-row: 2 / 3;
            padding: 20px;
            padding-top: 10px;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="fixed-header">
        <div class="title">COFFEE NEAR ME</div>
        <div class="user-info">
            <span class="username">Admin</span>
            <span class="notifications">☕</span>
        </div>
    </div>

    <nav id="sidebar">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="stocks.php">Stocks</a></li>
            <li><a href="orders.php">Orders</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="accounts.php">Accounts</a></li>
            <li><a href="logout.php" onclick="logoutUser()">Sign Out</a></li>
        </ul>
    </nav>

    <div class="main-content">
        <!-- Content will be injected here -->
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
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
</body>
</html>
