<?php
session_start();

// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Leave this empty if you are using the default XAMPP settings
$dbname = "cnmims";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to authenticate user
function authenticate($username, $password, $conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

// Check if the AJAX request is made
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        // Authenticate user
        if (authenticate($username, $password, $conn)) {
            // Set session variables
            $_SESSION['user_id'] = $username; // Assuming `username` is unique and used as user_id
            $_SESSION['logged_in'] = true;

            // Prepare JSON response
            $response = array(
                'success' => true,
                'message' => 'Login successful'
            );
        } else {
            // Prepare JSON response
            $response = array(
                'success' => false,
                'message' => 'Invalid username or password'
            );
        }

        // Send JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    } catch (Exception $e) {
        // Handle exceptions
        $response = array(
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        );
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-screen {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container {
            margin-bottom: 20px;
        }
        .back-button {
            font-size: 24px;
            color: #4b3832;
            cursor: pointer;
            text-align: left;
        }
        .avatar img {
            width: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .account-select {
            font-size: 24px;
            color: #4b3832;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .login-button {
            background-color: #4b3832;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
        }
        .error-message {
            color: red;
            font-size: 14px;
            display: none;
        }
        .app-name {
            font-size: 20px;
            color: #4b3832;
            margin-top: 20px;
        }
    </style>
    <script>
        // Function to prevent going back to the previous page
        function preventBack() {
            window.history.forward();
        }

        // Call the preventBack function on page load
        setTimeout("preventBack()", 0);

        // Handle the window's unload event
        window.onunload = function() {
            null;
        };
    </script>
</head>
<body>
    <div class="login-screen">
        <div class="login-container">
            <div class="avatar">
                <img src="cnm logoooo.jpg" alt="Avatar">
            </div>
            <div class="account-select">Select Account</div>
            <form id="login-form">
                <div class="form-group">
                    <input type="text" placeholder="USERNAME" class="form-input" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <input type="password" placeholder="PASSWORD" class="form-input" id="password" name="password" required>
                </div>
                <p class="error-message"></p>
                <button type="submit" class="login-button">LOGIN</button>
            </form>
        </div>
        <div class="app-name">COFFEE NEAR ME</div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#login-form").submit(function(event) {
                event.preventDefault();
                const username = $("#username").val();
                const password = $("#password").val();
                $.ajax({
                    type: "POST",
                    url: "<?php echo $_SERVER['PHP_SELF']; ?>",
                    data: { username: username, password: password },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = "dashboard.php"; // Redirect to dashboard or desired page
                        } else {
                            $(".error-message").text(response.message).show();
                        }
                    },
                    error: function() {
                        $(".error-message").text("An error occurred. Please try again.").show();
                    }
                });
            });
        });
    </script>
</body>
</html>
