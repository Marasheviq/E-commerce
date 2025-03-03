<?php
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "Ecommerce";

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = filter_input(INPUT_POST, "order_id", FILTER_SANITIZE_NUMBER_INT);
    $customerId = filter_input(INPUT_POST, "customer_id", FILTER_SANITIZE_NUMBER_INT);

    if ($orderId && $customerId) {
        $checkCustomerSql = "SELECT COUNT(*) FROM Customer WHERE CustomerId = ?";
        $stmt = $conn->prepare($checkCustomerSql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $stmt->bind_result($customerExists);
        $stmt->fetch();
        $stmt->close();

        if ($customerExists == 0) {
            $message = "<p class='error'>This Customer ID does not exist.</p>";
        } else {
            $checkOrderSql = "SELECT COUNT(*) FROM Orders WHERE OrderId = ? AND CustomerId = ?";
            $stmt = $conn->prepare($checkOrderSql);
            $stmt->bind_param("ii", $orderId, $customerId);
            $stmt->execute();
            $stmt->bind_result($orderExists);
            $stmt->fetch();
            $stmt->close();

            if ($orderExists == 0) {
                $message = "<p class='error'>This customer did not buy the product with Order ID $orderId.</p>";
            } else {
                $sql = "CALL DeleteOrder(?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $orderId, $customerId);

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $message = "<p class='success'>Order deleted successfully!</p>";
                    } else {
                        $message = "<p class='error'>Order ID and Customer ID do not match or Order not found!</p>";
                    }
                } else {
                    $message = "<p class='error'>Error: " . $stmt->error . "</p>";
                }

                $stmt->close();
            }
        }
    } else {
        $message = "<p class='error'>Please enter both Order ID and Customer ID.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #002244;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            font-size: 1.1em;
            color: #555;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }
        input[type="submit"] {
            background-color: #d9534f;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #c9302c;
        }
        .message {
            text-align: center;
            margin-top: 10px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Delete Order</h2>
            
            <label for="order_id">Order ID:</label>
            <input type="number" name="order_id" id="order_id" required>

            <label for="customer_id">Customer ID:</label>
            <input type="number" name="customer_id" id="customer_id" required>

            <?php if ($message) echo "<div class='message'>$message</div>"; ?>

            <input type="submit" value="Delete Order">
        </form>
    </div>
</body>
</html>