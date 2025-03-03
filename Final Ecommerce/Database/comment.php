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

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $comment = filter_input(INPUT_POST, "comment", FILTER_SANITIZE_SPECIAL_CHARS);
    $CustomerId = filter_input(INPUT_POST, "CustomerId", FILTER_SANITIZE_NUMBER_INT);
    $ProductId = filter_input(INPUT_POST, "ProductId", FILTER_SANITIZE_NUMBER_INT);

    $customer_check_sql = "
    SELECT c.CustomerId 
    FROM Customer c
    JOIN Orders o ON c.CustomerId = o.CustomerId
    WHERE c.CustomerId = ? 
    LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $customer_check_sql);
    mysqli_stmt_bind_param($stmt, 'i', $CustomerId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
      
        if ($comment && $ProductId) {
            $sql = "INSERT INTO Comments (CustomerId, ProductId, CommentText) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'iis', $CustomerId, $ProductId, $comment);

            if (mysqli_stmt_execute($stmt)) {
                $message = "<p class='message' style='color: green;'>Your comment has been successfully submitted!</p>";
            } else {
                $message = "<p class='message' style='color: red;'>Error submitting comment: " . mysqli_error($conn) . "</p>";
            }
        } else {
            $message = "<p class='message' style='color: red;'>Please provide a comment and select a product.</p>";
        }
    } else {
        $message = "<p class='message' style='color: red;'>You must have made a purchase to leave a comment.</p>";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Comment</title>
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
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        label {
            font-size: 1.1em;
            color: #555;
        }

        textarea, input[type="text"], select {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
            box-sizing: border-box;
        }

        input[type="submit"], button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 1.1em;
            cursor: pointer;
            margin-top: 10px;
        }

        input[type="submit"]:hover, button:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            color: red;
            margin-top: 10px;
        }

        .message.green {
            color: green;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Submit Your Comment</h2>
            
            <label for="comment">Your Comment:</label><br>
            <textarea name="comment" id="comment" rows="5" required></textarea><br>
            
            <label for="CustomerId">Your ID:</label><br>
            <input type="text" name="CustomerId" id="CustomerId" required><br>

            <label for="ProductId">Choose Product:</label><br>
            <select name="ProductId" id="ProductId" required>
                <option value="">Select a Product</option>
                <option value="1">HP Pavilion Aero Laptop 13</option>
                <option value="2">Apple MacBook Pro M4 Pro</option>
                <option value="3">Dell XPS 13 Laptop</option>
                <option value="4">ThinkPad P1 Gen 7 (16″ Intel) Mobile Workstation</option>
                <option value="5">Razer Blade 15 Gaming Laptop</option>
                <option value="6">HP Pavilion Laptop 16</option>
                <option value="7">MacBook Air 13'' M1 (space grey, 512GB)</option>
                <option value="8">Dell Vostro Laptop</option>
                <option value="9">HP Spectre Laptop</option>
            </select><br>
            
            <?php if ($message) echo $message; ?> 

            <input type="submit" value="Submit Comment">

            <button type="button" onclick="window.location.href='../Pages/ecommerce.html'">Leave</button>
        </form>
    </div>
</body>
</html>
