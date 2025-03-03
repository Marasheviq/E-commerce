<?php
session_start();

$correct_username = 'Arbjon';
$correct_password = '20062006';


$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);


    if ($username === $correct_username && $password === $correct_password) {

        $_SESSION['admin_logged_in'] = true;


        header("Location: AdminView.php");
        exit();
    } else {

        $message = "<p class='message' style='color: red;'>Incorrect username or password!</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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

        input[type="text"], input[type="password"] {
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
            text-decoration: none;
            margin-top: 6px;
        }

        .leave-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Admin Login</h2>
            
            <label for="username">Username:</label><br>
            <input type="text" name="username" id="username" required><br>

            <label for="password">Password:</label><br>
            <input type="password" name="password" id="password" required><br>

            <?php if ($message) echo $message; ?> 

            <input type="submit" value="Login">
            
            <a href="../Pages/AskingLogin.html" class="leave-button">Leave</a>
        </form>
    </div>
</body>
</html>
