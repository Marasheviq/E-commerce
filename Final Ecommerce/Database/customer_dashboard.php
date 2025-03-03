<?php
session_start(); 

$conn = mysqli_connect("localhost", "root", "", "Ecommerce");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['customer_logged_in']) || $_SESSION['customer_logged_in'] !== true) {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

$comments_query = "
    SELECT 
        CMV.CommentId, 
        CMV.CustomerName, 
        P.ProductName, 
        CMV.CommentText 
    FROM CommentsView CMV
    JOIN Comments C ON CMV.CommentId = C.CommentId  -- Join with Comments table to get ProductId
    JOIN Product P ON C.ProductId = P.ProductId  -- Now join Product table using ProductId
    WHERE CMV.CustomerId = ?
";

$orders_query = "
    SELECT 
        OV.OrderId, 
        OV.OrderDate, 
        OV.ProductName, 
        OV.ProductPrice AS OrderPrice 
    FROM OrdersView OV
    WHERE OV.CustomerId = ?
";

$comments_stmt = mysqli_prepare($conn, $comments_query);
mysqli_stmt_bind_param($comments_stmt, 'i', $customer_id);
mysqli_stmt_execute($comments_stmt);
$comments_result = mysqli_stmt_get_result($comments_stmt);

$orders_stmt = mysqli_prepare($conn, $orders_query);
mysqli_stmt_bind_param($orders_stmt, 'i', $customer_id);
mysqli_stmt_execute($orders_stmt);
$orders_result = mysqli_stmt_get_result($orders_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        td {
            background-color: white;
        }

        .logout-button {
            display: block;
            width: 120px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            border: none;
            text-decoration: none;
            font-size: 1.1em;
            border-radius: 4px;
        }

        .logout-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Customer Dashboard</h2>
    
    <h3>Comments</h3>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($comment_row = mysqli_fetch_assoc($comments_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($comment_row['ProductName']); ?></td>
                    <td><?php echo htmlspecialchars($comment_row['CommentText'] ? $comment_row['CommentText'] : 'No comment'); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3>Orders</h3>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Product Name</th>
                <th>Order Price</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order_row = mysqli_fetch_assoc($orders_result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order_row['OrderId']); ?></td>
                    <td><?php echo htmlspecialchars($order_row['OrderDate']); ?></td>
                    <td><?php echo htmlspecialchars($order_row['ProductName']); ?></td>
                    <td><?php echo htmlspecialchars($order_row['OrderPrice']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="../Database/CustomerLogIn.php" class="logout-button">Logout</a>
</div>

</body>
</html>

<?php
mysqli_close($conn);
?>
