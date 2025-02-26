<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: CustomerLogIn.php');
    exit;
}

// Database connection
$conn = new mysqli("localhost", "root", "", "Ecommerce"); // Replace 'Ecommerce' with your actual database name

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in customer's ID
$customerId = $_SESSION['customer_id'];

// Fetch customer orders
$query_orders = "
    SELECT 
        O.OrderId, 
        O.OrderDate, 
        P.ProductName, 
        O.ProductPrice 
    FROM Orders O
    JOIN Product P ON O.ProductId = P.ProductId
    WHERE O.CustomerId = ?";
$stmt_orders = $conn->prepare($query_orders);
$stmt_orders->bind_param("i", $customerId);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();

// Fetch customer comments
$query_comments = "
    SELECT 
        C.CommentId, 
        C.CommentText, 
        P.ProductName 
    FROM Comments C
    JOIN Product P ON C.ProductId = P.ProductId
    WHERE C.CustomerId = ?";
$stmt_comments = $conn->prepare($query_comments);
$stmt_comments->bind_param("i", $customerId);
$stmt_comments->execute();
$result_comments = $stmt_comments->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .dashboard-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f8f8f8;
        }
        .logout {
            text-align: right;
        }
        .logout a {
            color: #007BFF;
            text-decoration: none;
        }
        .section-title {
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Your Orders</h1>
        <div class="logout">
            <a href="CustomerLogIn.php">Logout</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Product Price</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_orders->num_rows > 0): ?>
                    <?php while ($row = $result_orders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['OrderId']); ?></td>
                            <td><?php echo htmlspecialchars($row['OrderDate']); ?></td>
                            <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
                            <td>$<?php echo htmlspecialchars(number_format($row['ProductPrice'], 2)); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 class="section-title">Your Comments</h2>
        <table>
            <thead>
                <tr>
                    <th>Comment ID</th>
                    <th>Comment Text</th>
                    <th>Product Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_comments->num_rows > 0): ?>
                    <?php while ($row = $result_comments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['CommentId']); ?></td>
                            <td><?php echo htmlspecialchars($row['CommentText']); ?></td>
                            <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No comments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
