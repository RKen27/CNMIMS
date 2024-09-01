<?php
include 'sessions.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-rows: auto 1fr;
            grid-template-columns: 250px 1fr;
            height: 100vh;
            }

            .fixed-header {
                grid-column: 1 / span 2;
                background-color: #fff;
                padding: 10px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                z-index: 1000;
                position: sticky;
                top: 0;
                left: 0;
                width: 100%;
            }

            #sidebar {
                background-color: #4b3832;
                color: white;
                padding: 20px;
                height: calc(100vh - 60px); /* Adjust for header height */
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
                grid-row: 2;
                position: sticky;
                top: 60px; /* Adjust for header height */
            }

            .main {
                padding: 20px;
                grid-column: 2 / 3; /* Adjust to ensure content fills the space next to the sidebar */
                grid-row: 2;
                overflow-y: auto;
            }

            h1, h2 {
                color: #4b3832;
            }

            form {
                background: #fff;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }

            label {
                display: block;
                margin-bottom: 10px;
                font-weight: bold;
            }

            select, input[type="number"] {
                width: 100%;
                padding: 10px;
                margin-bottom: 20px;
                border: 1px solid #ccc;
                border-radius: 4px;
            }

            .button {
                background-color: #4b3832;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 16px;
            }

            .button.red {
                background-color: #000;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                background: #fff;
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
                font-weight: bold;
            }

            tr:hover {
                background-color: #f1f1f1;
            }

            .modal {
                display: none;
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.5);
                padding-top: 60px;
            }

            .modal-content {
                background-color: #fff;
                margin: 5% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                box-shadow: 0 5px 8px rgba(0, 0, 0, 0.3);
                border-radius: 8px;
                animation: fadeIn 0.4s;
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

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="main">
        <h1>Orders</h1>
        <form id="orderForm">
            <label for="product">Product:</label>
            <select id="product" name="product"></select>
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" required>
            <button type="button" class="button" onclick="addToCart()">Add to Cart</button>
        </form>
        
        <h2>Cart</h2>
        <table id="cartTable">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <p><strong>Total Bill: </strong><span id="totalBill">0</span></p>
        <button class="button" onclick="confirmOrder()">Confirm Order</button>

        <div id="receiptModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeReceiptModal()">&times;</span>
                <h2>Receipt</h2>
                <div id="receiptContent"></div>
                <button class="button" onclick="printReceipt()">Print</button>
            </div>
        </div>
    </div>

    <script>
    let cart = [];
    let totalBill = 0;

    document.addEventListener("DOMContentLoaded", function() {
        loadProducts();
    });

    function loadProducts() {
        fetch('ordersdb.php?action=getProducts')
            .then(response => response.json())
            .then(data => {
                const productSelect = document.getElementById('product');
                data.forEach(product => {
                    const option = document.createElement('option');
                    option.value = product.id;
                    option.text = `${product.name} - $${product.retail_price}`;
                    productSelect.appendChild(option);
                });
            });
    }

    function addToCart() {
        const productSelect = document.getElementById('product');
        const quantityInput = document.getElementById('quantity');
        const productId = productSelect.value;
        const productName = productSelect.options[productSelect.selectedIndex].text;
        const quantity = parseInt(quantityInput.value);

        fetch(`ordersdb.php?action=getProductById&id=${productId}`)
            .then(response => response.json())
            .then(product => {
                const cartItem = {
                    id: product.id,
                    name: product.name,
                    quantity: quantity,
                    price: parseFloat(product.retail_price),
                    total: quantity * parseFloat(product.retail_price)
                };
                cart.push(cartItem);
                updateCartTable();
            });
    }

    function updateCartTable() {
        const cartTableBody = document.getElementById('cartTable').getElementsByTagName('tbody')[0];
        cartTableBody.innerHTML = '';
        totalBill = 0;

        cart.forEach((item, index) => {
            const row = cartTableBody.insertRow();
            row.insertCell(0).innerText = item.name;
            row.insertCell(1).innerText = item.quantity;
            row.insertCell(2).innerText = item.price;
            row.insertCell(3).innerText = item.total.toFixed(2);
            row.insertCell(4).innerHTML = `<button class="button red" onclick="removeFromCart(${index})">Remove</button>`;
            totalBill += item.total;
        });

        document.getElementById('totalBill').innerText = totalBill.toFixed(2);
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartTable();
    }

    function confirmOrder() {
    const customerPayment = prompt('Enter customer payment amount:');
    const change = customerPayment - totalBill;

    if (change < 0) {
        alert('Insufficient payment!');
        return;
    }

    const orderDetails = {
        cart: cart,
        totalBill: totalBill,
        customerPayment: parseFloat(customerPayment),
        change: change.toFixed(2)
    };

    fetch('ordersdb.php?action=confirmOrder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderDetails)
    })
    .then(response => response.json())
    .then(data => {
        if (data && data.success) {
            showReceipt(data);
        } else {
            console.error('Error confirming order:', data.message);
            alert(`An error occurred while confirming the order: ${data.message}`);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred while confirming the order.');
    });
}

    function showReceipt(data) {
        const receiptContent = document.getElementById('receiptContent');
        receiptContent.innerHTML = `
            <p><strong>Total Bill: </strong>${data.totalBill}</p>
            <p><strong>Payment: </strong>${data.customerPayment}</p>
            <p><strong>Change: </strong>${data.change}</p>
            <p><strong>Items:</strong></p>
            <ul>
                ${data.cart.map(item => `<li>${item.name} - ${item.quantity} @ $${item.price} each = $${item.total.toFixed(2)}</li>`).join('')}
            </ul>
        `;
        document.getElementById('receiptModal').style.display = 'block';
    }

    function closeReceiptModal() {
        document.getElementById('receiptModal').style.display = 'none';
    }

    function printReceipt() {
        const receiptContent = document.getElementById('receiptContent').innerHTML;
        const newWindow = window.open('', '', 'width=600,height=400');
        newWindow.document.write('<html><head><title>Print Receipt</title></head><body>');
        newWindow.document.write(receiptContent);
        newWindow.document.write('</body></html>');
        newWindow.document.close();
        newWindow.print();
    }
</script>

</body>
</html>
