<?php
session_start(); 

$conn = mysqli_connect("localhost", "root", "", "Ecommerce");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_customer'])) {
    $customer_email = mysqli_real_escape_string($conn, $_POST['customer_email']);
    $customer_id = mysqli_real_escape_string($conn, $_POST['customer_id']);

    $query = "SELECT * FROM Customer WHERE CustomerEmail = ? AND CustomerId = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'si', $customer_email, $customer_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['customer_logged_in'] = true;
        $_SESSION['customer_id'] = mysqli_fetch_assoc($result)['CustomerId'];
        header("Location: customer_dashboard.php");
        exit();
    } else {
        $error_message = "Invalid credentials. Please try again.";
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
    <title>Customer Login</title>
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

        input[type="email"], input[type="number"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
            box-sizing: border-box;
        }

        input[type="submit"], .button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 1.1em;
            cursor: pointer;
        }

        input[type="submit"]:hover, .button:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            color: red;
            margin-top: 10px;
        }

        .leave-button {
            display: inline-block;
            text-align: center;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 1.1em;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
            margin-top: 10px;
            text-decoration: none;
        }

        .leave-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <form method="post" action="">
            <h2>Customer Login</h2>
            
            <label for="customer_email">Email:</label><br>
            <input type="email" name="customer_email" id="customer_email" required><br>

            <label for="customer_id">Customer ID:</label><br>
            <input type="number" name="customer_id" id="customer_id" required><br>

            <?php if (!empty($error_message)) echo "<p class='message'>$error_message</p>"; ?>

            <input type="submit" name="login_customer" value="Login">
            
            <a href="../Pages/AskingLogin.html" class="leave-button">Leave</a>
        </form>
    </div>

</body>
</html>
