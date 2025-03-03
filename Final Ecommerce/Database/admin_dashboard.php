<?php
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); 
    exit();
}

$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "Ecommerce"; 

$conn = null; 
try {
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
    if (!$conn) {
        throw new Exception("Could not connect to the database!");
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

$query = "SELECT * FROM Customer";
$result = mysqli_query($conn, $query);

$order_query = "SELECT * FROM Orders ORDER BY OrderId DESC";
$order_result = mysqli_query($conn, $order_query);

$comment_query = "SELECT * FROM Comments";
$comment_result = mysqli_query($conn, $comment_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_customer'])) {
        $customer_id = $_POST['customer_id'];

        $delete_orders_query = "DELETE FROM Orders WHERE CustomerId = ?";
        $stmt = mysqli_prepare($conn, $delete_orders_query);
        mysqli_stmt_bind_param($stmt, 'i', $customer_id);
        mysqli_stmt_execute($stmt);

        $delete_comments_query = "DELETE FROM Comments WHERE CustomerId = ?";
        $stmt = mysqli_prepare($conn, $delete_comments_query);
        mysqli_stmt_bind_param($stmt, 'i', $customer_id);
        mysqli_stmt_execute($stmt);

        $delete_customer_query = "DELETE FROM Customer WHERE CustomerId = ?";
        $stmt = mysqli_prepare($conn, $delete_customer_query);
        mysqli_stmt_bind_param($stmt, 'i', $customer_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Customer and their related data deleted successfully!";
        } else {
            $message = "Error deleting customer: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['delete_comment'])) {
        
        $comment_id = $_POST['comment_id'];

        $delete_comment_query = "DELETE FROM Comments WHERE CommentId = ?";
        $stmt = mysqli_prepare($conn, $delete_comment_query);
        mysqli_stmt_bind_param($stmt, 'i', $comment_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Comment deleted successfully!";
        } else {
            $message = "Error deleting comment: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            margin: 20px;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        .message {
            text-align: center;
            margin-top: 10px;
            color: green;
        }

        .table-container {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .actions {
            display: flex;
            justify-content: space-between;
        }

        button {
            background-color: #f44336;
            color: white;
            cursor: pointer;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        button:hover {
            background-color: #e6362d;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <h1>Welcome to Admin Dashboard</h1>

    
    <?php if (isset($message)) { ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>


    <div class="table-container">
        <h2>Customer List</h2>

        <table>
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['CustomerId']; ?></td>
                        <td><?php echo $row['CustomerName']; ?></td>
                        <td><?php echo $row['CustomerEmail']; ?></td>
                        <td class="actions">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="customer_id" value="<?php echo $row['CustomerId']; ?>">
                                <button type="submit" name="delete_customer">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <h2>Order List</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Customer ID</th>
                    <th>Product ID</th>
                    <th>Product Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order_row = mysqli_fetch_assoc($order_result)) { ?>
                    <tr>
                        <td><?php echo $order_row['OrderId']; ?></td>
                        <td><?php echo $order_row['OrderDate']; ?></td>
                        <td><?php echo $order_row['CustomerId']; ?></td>
                        <td><?php echo $order_row['ProductId']; ?></td>
                        <td><?php echo $order_row['ProductPrice']; ?></td>
                        <td class="actions">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order_row['OrderId']; ?>">
                                <button type="submit" name="delete_order">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>


    <div class="table-container">
        <h2>Comment List</h2>
        <table>
            <thead>
                <tr>
                    <th>Comment ID</th>
                    <th>Comment Text</th>
                    <th>Customer ID</th>
                    <th>Product ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($comment_row = mysqli_fetch_assoc($comment_result)) { ?>
                    <tr>
                        <td><?php echo $comment_row['CommentId']; ?></td>
                        <td><?php echo $comment_row['CommentText']; ?></td>
                        <td><?php echo $comment_row['CustomerId']; ?></td>
                        <td><?php echo $comment_row['ProductId']; ?></td>
                        <td class="actions">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="comment_id" value="<?php echo $comment_row['CommentId']; ?>">
                                <button type="submit" name="delete_comment">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
