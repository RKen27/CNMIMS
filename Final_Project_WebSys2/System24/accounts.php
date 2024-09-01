<?php
include 'sessions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accounts</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .form-container, .table-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-container h2, .table-container h2 {
            margin-bottom: 20px;
            font-family: 'Georgia', serif;
            color: #4b3832;
        }

        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #4b3832;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #4b3832;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container button:hover {
            background-color: #7b5e57;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
            font-size: 16px;
        }

        th {
            background-color: #f2f2f2;
            color: #4b3832;
        }

        .action-buttons button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .action-buttons .edit-button {
            background-color: #4b3832;
            color: #fff;
        }

        .action-buttons .edit-button:hover {
            background-color: #7b5e57;
        }

        .action-buttons .delete-button {
            background-color: #cc0000;
            color: #fff;
        }

        .action-buttons .delete-button:hover {
            background-color: #ff3333;
        }

        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="main-content">
        <div class="form-container" style="display: none;" id="editFormContainer">
            <h2>Edit Account</h2>
            <form id="editAccountForm">
                <input type="hidden" id="editId" name="id">
                <label for="editFullname">Full Name:</label>
                <input type="text" id="editFullname" name="fullname" required>
                
                <label for="editContact">Contact:</label>
                <input type="text" id="editContact" name="contact" required>
                
                <label for="editUsername">Username:</label>
                <input type="text" id="editUsername" name="username" required>
                
                <label for="editPassword">Password:</label>
                <input type="password" id="editPassword" name="password" required>
                
                <button type="submit">Update Account</button>
            </form>
        </div>

        <div class="form-container" id="registerFormContainer">
            <h2>Register New Account</h2>
            <form id="registerAccountForm">
                <label for="newFullname">Full Name:</label>
                <input type="text" id="newFullname" name="fullname" required>
                
                <label for="newContact">Contact:</label>
                <input type="text" id="newContact" name="contact" required>
                
                <label for="newUsername">Username:</label>
                <input type="text" id="newUsername" name="username" required>
                
                <label for="newPassword">Password:</label>
                <input type="password" id="newPassword" name="password" required>
                
                <button type="submit">Register Account</button>
            </form>
        </div>

        <div class="table-container">
            <h2>Existing Accounts</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Contact</th>
                        <th>Username</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <!-- User rows will be appended here by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadUsers() {
            $.ajax({
                url: 'accountsdb.php',
                type: 'GET',
                data: { action: 'read' },
                success: function(response) {
                    $('#userTableBody').html(response);
                },
                error: function() {
                    alert('Failed to load users.');
                }
            });
        }

        $(document).ready(function() {
            loadUsers();

            $('#registerAccountForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'accountsdb.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=create',
                    success: function(response) {
                        alert(response);
                        loadUsers();
                        $('#registerAccountForm')[0].reset();
                    },
                    error: function() {
                        alert('Failed to register account.');
                    }
                });
            });

            $('#editAccountForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'accountsdb.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=update',
                    success: function(response) {
                        alert(response);
                        loadUsers();
                        $('#editFormContainer').slideUp();
                        $('#registerFormContainer').slideDown();
                        $('#editAccountForm')[0].reset();
                    },
                    error: function() {
                        alert('Failed to update account.');
                    }
                });
            });

            $(document).on('click', '.edit-button', function() {
                var user = $(this).data('user');
                $('#editId').val(user.id);
                $('#editFullname').val(user.fullname);
                $('#editContact').val(user.contact);
                $('#editUsername').val(user.username);
                $('#editPassword').val(user.password);
                $('#editFormContainer').slideDown();
                $('#registerFormContainer').slideUp();
            });

            $(document).on('click', '.delete-button', function() {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this account?')) {
                    $.ajax({
                        url: 'accountsdb.php',
                        type: 'POST',
                        data: { action: 'delete', id: id },
                        success: function(response) {
                            alert(response);
                            loadUsers();
                        },
                        error: function() {
                            alert('Failed to delete account.');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
