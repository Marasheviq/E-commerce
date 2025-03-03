<?php

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
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

        input[type="text"], input[type="email"], input[type="number"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 1.1em;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Customer Registration</h2>
            <label for="CustomerId">Customer ID:</label><br>
            <input type="number" id="CustomerId" name="CustomerId" required><br>
            <label for="CustomerName">Username:</label><br>
            <input type="text" id="CustomerName" name="CustomerName" required><br>
            <label for="CustomerEmail">Email:</label><br>
            <input type="email" id="CustomerEmail" name="CustomerEmail" required><br>
            <input type="submit" name="submit" value="Register & Buy">
        </form>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $customerId = filter_input(INPUT_POST, "CustomerId", FILTER_SANITIZE_NUMBER_INT);
            $username = filter_input(INPUT_POST, "CustomerName", FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, "CustomerEmail", FILTER_SANITIZE_EMAIL);

            $check_existing_sql = "SELECT * FROM Customer WHERE CustomerId = ?";
            $stmt = mysqli_prepare($conn, $check_existing_sql);
            mysqli_stmt_bind_param($stmt, 'i', $customerId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 0) {
               
                $insert_sql = "INSERT INTO Customer (CustomerId, CustomerName, CustomerEmail) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insert_sql);
                mysqli_stmt_bind_param($stmt, 'iss', $customerId, $username, $email);
                if (!mysqli_stmt_execute($stmt)) {
                    echo "<p class='message'>Error inserting customer: " . mysqli_error($conn) . "</p>";
                }
            }
            
            $productId = 2;
            $productPrice = 1999; 
            $orderDate = date('Y-m-d');

            $order_sql = "INSERT INTO Orders (OrderDate, CustomerId, ProductId, ProductPrice) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $order_sql);
            mysqli_stmt_bind_param($stmt, 'siii', $orderDate, $customerId, $productId, $productPrice);

            if (mysqli_stmt_execute($stmt)) {


                $update_inventory_sql = "UPDATE Inventory SET Quantity = Quantity - 1, Sold = Sold + 1, Total = Total + ? WHERE ProductId = ?";
                $stmt = mysqli_prepare($conn, $update_inventory_sql);
                mysqli_stmt_bind_param($stmt, 'di', $productPrice, $productId);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<p class='message' style='color: green;'>Product purchased successfully. Inventory updated.</p>";
                } else {
                    echo "<p class='message'>Error updating inventory: " . mysqli_error($conn) . "</p>";
                }
            } else {
                echo "<p class='message'>Error adding order: " . mysqli_error($conn) . "</p>";
            }

            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);
        ?>
    </div>
</body>
</html>
