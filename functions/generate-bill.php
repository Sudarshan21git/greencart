<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    die("Order ID not provided");
}

$order_id = intval($_GET['order_id']);

// Database connection
include("../database/database.php");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch order details
$order_query = "SELECT o.*, u.first_name, u.last_name, u.email, u.phone 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                WHERE o.order_id = $order_id AND o.user_id = " . $_SESSION['user_id'];

$order_result = mysqli_query($conn, $order_query);

if (!$order_result || mysqli_num_rows($order_result) == 0) {
    die("Order not found or access denied");
}

$order = mysqli_fetch_assoc($order_result);

// Fetch order items
$items_query = "SELECT oi.quantity, oi.price, p.name, p.image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.product_id 
                WHERE oi.order_id = $order_id";

$items_result = mysqli_query($conn, $items_query);

if (!$items_result) {
    die("Error fetching order items: " . mysqli_error($conn));
}

$order_items = [];
while ($item = mysqli_fetch_assoc($items_result)) {
    $order_items[] = $item;
}

// Format dates
$order_date = date('F j, Y, g:i a', strtotime($order['created_at']));
$invoice_date = date('F j, Y', strtotime($order['created_at']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo $order['order_number']; ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            padding: 30px;
            border-bottom: 2px solid #eee;
        }
        
        .invoice-info, .customer-info {
            flex: 1;
        }
        
        .invoice-info h3, .customer-info h3 {
            color: #4CAF50;
            margin-bottom: 15px;
            font-size: 1.2em;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #666;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .items-table th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            color: #495057;
            font-weight: 600;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: top;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .product-name {
            font-weight: 600;
            color: #333;
        }
        
        .quantity {
            text-align: center;
            font-weight: 600;
        }
        
        .price {
            text-align: right;
            font-weight: 600;
        }
        
        .total {
            text-align: right;
            font-weight: 600;
            color: #4CAF50;
        }
        
        .summary {
            padding: 30px;
            background-color: #f8f9fa;
            border-top: 2px solid #eee;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
        }
        
        .summary-row.total-row {
            border-top: 2px solid #4CAF50;
            margin-top: 15px;
            padding-top: 15px;
            font-size: 1.2em;
            font-weight: bold;
            color: #4CAF50;
        }
        
        .footer {
            padding: 20px 30px;
            text-align: center;
            color: #666;
            border-top: 1px solid #eee;
            background-color: #f8f9fa;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: background-color 0.3s;
        }
        
        .print-btn:hover {
            background-color: #45a049;
        }
        
        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn no-print">
        🖨️ Print Invoice
    </button>
    
    <div class="invoice-container">
        <div class="header">
            <h1>GreenCart</h1>
            <p>Your Trusted Plant Store</p>
        </div>
        
        <div class="invoice-details">
            <div class="invoice-info">
                <h3>Invoice Details</h3>
                <div class="info-row">
                    <span class="info-label">Invoice Number:</span>
                    <span><?php echo htmlspecialchars($order['order_number']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Invoice Date:</span>
                    <span><?php echo $invoice_date; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Date:</span>
                    <span><?php echo $order_date; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span><?php echo strtoupper(htmlspecialchars($order['payment_method'])); ?></span>
                </div>
            </div>
            
            <div class="customer-info">
                <h3>Customer Information</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span><?php echo htmlspecialchars($order['email']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span><?php echo htmlspecialchars($order['phone']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Shipping Address:</span>
                    <span><?php echo htmlspecialchars($order['shipping_address']); ?></span>
                </div>
            </div>
        </div>
        
        <div style="padding: 30px;">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <img src="../img/<?php echo htmlspecialchars($item['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                     class="product-image">
                                <span class="product-name"><?php echo htmlspecialchars($item['name']); ?></span>
                            </div>
                        </td>
                        <td class="quantity"><?php echo $item['quantity']; ?></td>
                        <td class="price">Rs.<?php echo number_format($item['price']); ?></td>
                        <td class="total">Rs.<?php echo number_format($item['price'] * $item['quantity']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>Rs.<?php echo number_format($order['subtotal']); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span>Rs.0.00</span>
            </div>
            <div class="summary-row total-row">
                <span>Total Amount:</span>
                <span>Rs.<?php echo number_format($order['total']); ?></span>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Thank you for your purchase!</strong></p>
            <p>For any questions or concerns, please contact us at support@greencart.com</p>
            <p>GreenCart - Making your world greener, one plant at a time.</p>
        </div>
    </div>
</body>
</html> 