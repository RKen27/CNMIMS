<?php
include 'sessions.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        #wrapper {
            display: flex;
        }
        #main-content {
            flex: 1;
            padding: 20px;
            margin-top: 60px;
        }
        h1, h2 {
            color: #4b3832;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: #fff;
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f8f8;
        }
        tr:hover {
            background-color: #f5f5f5;
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
        .red {
            background-color: peru;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 8px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <?php include 'nav.php'; ?>
        <div id="main-content">
            <h1>Inventory</h1>
            <button class="button" id="openModalBtn">Create New Product</button>
            <div>
                <h2>Categories</h2>
                <?php
                include 'inventorydb.php';
                $categories = getCategories();
                foreach ($categories as $category) {
                    echo "<a href='inventory.php?category=$category' class='button'>$category</a> ";
                }
                ?>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Purchase Price</th>
                        <th>Retail Price</th>
                        <th>Ideal Stock</th>
                        <th>Available Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;
                    $inventory = getInventoryData($selectedCategory);

                    foreach ($inventory as $product) {
                        echo "<tr>
                                <td><img src='" . $product['image'] . "' width='50' height='50'></td>
                                <td>" . $product['id'] . "</td>
                                <td>" . $product['name'] . "</td>
                                <td>" . $product['category'] . "</td>
                                <td>" . $product['purchase_price'] . "</td>
                                <td>" . $product['retail_price'] . "</td>
                                <td>" . $product['ideal_stock'] . "</td>
                                <td>" . $product['available_stock'] . "</td>
                                <td>
                                    <button class='button' onclick='openEditModal(" . json_encode($product) . ")'>Edit</button>
                                    <button class='button red' onclick='confirmDelete(" . $product['id'] . ")'>Delete</button>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Create New Product</h2>
            <form action="inventorydb.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <label for="productName">Product Name:</label><br>
                <input type="text" id="productName" name="productName" required><br><br>
                <label for="productCategory">Category:</label><br>
                <select id="productCategory" name="productCategory" required>
                    <option value="Coffee">Coffee</option>
                    <option value="Frappe">Frappe</option>
                    <option value="Creamery">Creamery</option>
                    <option value="Fruity Soda">Fruity Soda</option>
                </select><br><br>
                <label for="purchasePrice">Purchase Price:</label><br>
                <input type="text" id="purchasePrice" name="purchasePrice" required><br><br>
                <label for="retailPrice">Retail Price:</label><br>
                <input type="text" id="retailPrice" name="retailPrice" required><br><br>
                <label for="idealStock">Ideal Stock:</label><br>
                <input type="text" id="idealStock" name="idealStock" required><br><br>
                <label for="availableStock">Available Stock:</label><br>
                <input type="text" id="availableStock" name="availableStock" required><br><br>
                <label for="productImage">Product Image:</label><br>
                <input type="file" id="productImage" name="productImage" accept="image/*" required><br><br>
                <input type="submit" value="Add Product" class="button">
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Product</h2>
            <form action="inventorydb.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="editProductId" name="productId">
                <label for="editProductName">Product Name:</label><br>
                <input type="text" id="editProductName" name="productName" required><br><br>
                <label for="editProductCategory">Category:</label><br>
                <select id="editProductCategory" name="productCategory" required>
                    <option value="Coffee">Coffee</option>
                    <option value="Frappe">Frappe</option>
                    <option value="Creamery">Creamery</option>
                    <option value="Fruity Soda">Fruity Soda</option>
                </select><br><br>
                <label for="editPurchasePrice">Purchase Price:</label><br>
                <input type="text" id="editPurchasePrice" name="purchasePrice" required><br><br>
                <label for="editRetailPrice">Retail Price:</label><br>
                <input type="text" id="editRetailPrice" name="retailPrice" required><br><br>
                <label for="editIdealStock">Ideal Stock:</label><br>
                <input type="text" id="editIdealStock" name="idealStock" required><br><br>
                <label for="editAvailableStock">Available Stock:</label><br>
                <input type="text" id="editAvailableStock" name="availableStock" required><br><br>
                <label for="editProductImage">Product Image:</label><br>
                <input type="file" id="editProductImage" name="productImage" accept="image/*"><br><br>
                <input type="submit" value="Save Changes" class="button">
            </form>
        </div>
    </div>

    <script>
        // Get the modals
        var productModal = document.getElementById('productModal');
        var editProductModal = document.getElementById('editProductModal');

        // Get the buttons that open the modals
        var openModalBtn = document.getElementById('openModalBtn');

        // Get the <span> elements that close the modals
        var closeModalElements = document.getElementsByClassName('close');

        // When the user clicks the button, open the create product modal
        openModalBtn.onclick = function() {
            productModal.style.display = 'block';
        }

        // When the user clicks on <span> (x), close the modal
        Array.prototype.forEach.call(closeModalElements, function(element) {
            element.onclick = function() {
                productModal.style.display = 'none';
                editProductModal.style.display = 'none';
            }
        });

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == productModal) {
                productModal.style.display = 'none';
            }
            if (event.target == editProductModal) {
                editProductModal.style.display = 'none';
            }
        }

        // Open edit product modal and populate the form with product data
        function openEditModal(product) {
            document.getElementById('editProductId').value = product.id;
            document.getElementById('editProductName').value = product.name;
            document.getElementById('editProductCategory').value = product.category;
            document.getElementById('editPurchasePrice').value = product.purchase_price;
            document.getElementById('editRetailPrice').value = product.retail_price;
            document.getElementById('editIdealStock').value = product.ideal_stock;
            document.getElementById('editAvailableStock').value = product.available_stock;

            editProductModal.style.display = 'block';
        }

        // Confirm deletion of product
        function confirmDelete(productId) {
            if (confirm("Are you sure you want to delete this product?")) {
                window.location.href = 'inventorydb.php?action=delete&id=' + productId;
            }
        }
    </script>
</body>
</html>
