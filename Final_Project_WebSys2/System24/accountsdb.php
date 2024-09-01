<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = ""; // Update with your database password
$dbname = "cnmims";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'create':
            $fullname = $_POST['fullname'];
            $contact = $_POST['contact'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Check if the username already exists
            $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo 'Error: Username already exists.';
            } else {
                // Insert new user
                $stmt = $conn->prepare("INSERT INTO users (fullname, contact, username, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $fullname, $contact, $username, $password);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo 'Account registered successfully!';
                } else {
                    echo 'Error: Could not register account.';
                }
            }
            $stmt->close();
            break;

        case 'update':
            $id = $_POST['id'];
            $fullname = $_POST['fullname'];
            $contact = $_POST['contact'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Update user details
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, contact = ?, username = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $fullname, $contact, $username, $password, $id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo 'Account updated successfully!';
            } else {
                echo 'Error: Could not update account.';
            }
            $stmt->close();
            break;

        case 'delete':
            $id = $_POST['id'];

            // Delete user
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo 'Account deleted successfully!';
            } else {
                echo 'Error: User not found.';
            }
            $stmt->close();
            break;

        default:
            echo 'Invalid action.';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'read') {
    // Read users
    $result = $conn->query("SELECT * FROM users");

    $output = '';
    while ($row = $result->fetch_assoc()) {
        $userArray = json_encode($row);

        $output .= '<tr>
                        <td>' . $row['id'] . '</td>
                        <td>' . $row['fullname'] . '</td>
                        <td>' . $row['contact'] . '</td>
                        <td>' . $row['username'] . '</td>
                        <td class="action-buttons">
                            <button class="edit-button" data-user=\'' . $userArray . '\'>Edit</button>
                            <button class="delete-button" data-id="' . $row['id'] . '">Delete</button>
                        </td>
                    </tr>';
    }

    echo $output;
}

// Close connection
$conn->close();
?>
