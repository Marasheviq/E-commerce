<?php

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "ecommerce"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function deleteEntry($conn, $tableName, $idColumn, $id) {

    if (!is_numeric($id)) {
        echo "<p>Invalid ID provided.</p>";
        return;
    }

  
    if ($tableName === "Customer") {
       
        $sql = "DELETE FROM Comments WHERE CustomerId = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "<p>Error preparing statement: " . $conn->error . "</p>";
            return;
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            echo "<p>Error deleting related comments: " . $stmt->error . "</p>";
            return;
        }
        $stmt->close();

     
        $sql = "DELETE FROM Orders WHERE CustomerId = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "<p>Error preparing statement: " . $conn->error . "</p>";
            return;
        }
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            echo "<p>Error deleting related orders: " . $stmt->error . "</p>";
            return;
        }
        $stmt->close();
    }


    $sql = "DELETE FROM $tableName WHERE $idColumn = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "<p>Error preparing statement: " . $conn->error . "</p>";
        return;
    }


    $stmt->bind_param("i", $id);


    if ($stmt->execute()) {
        echo "<p>Entry deleted successfully.</p>";
    } else {
        echo "<p>Error deleting entry: " . $stmt->error . "</p>";
    }

 
    $stmt->close();
}


if (isset($_GET['delete_customer']) && !empty($_GET['delete_customer'])) {
    deleteEntry($conn, "Customer", "CustomerId", $_GET['delete_customer']);
} elseif (isset($_GET['delete_comment']) && !empty($_GET['delete_comment'])) {
    deleteEntry($conn, "Comments", "CommentId", $_GET['delete_comment']);
}

function displayView($conn, $viewName, $title, $deleteAction, $idColumn) {
    $sql = "SELECT * FROM $viewName";
    $result = $conn->query($sql);

    echo "<h2>$title</h2>";

    if ($result->num_rows > 0) {
        echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background-color: #f2f2f2;'>";

    
        while ($field = $result->fetch_field()) {
            echo "<th style='padding: 8px; border: 1px solid #ddd;'>" . $field->name . "</th>";
        }

     
        if (!empty($deleteAction)) {
            echo "<th style='padding: 8px; border: 1px solid #ddd;'>Action</th>";
        }
        echo "</tr>";

     
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>$value</td>";
            }

            if (isset($row[$idColumn]) && !empty($deleteAction)) {
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>
                        <form method='get' style='display:inline;'>
                            <input type='hidden' name='$deleteAction' value='{$row[$idColumn]}'>
                            <button type='submit' style='background-color: #ff4d4d; color: white; border: none; padding: 5px 10px; cursor: pointer;'>Delete</button>
                        </form>
                      </td>";
            }
            echo "</tr>";
        }

        echo "</table><br>";
    } else {
        echo "<p>No data available.</p>";
    }
}


displayView($conn, "InventoryView", "Inventory List", "", ""); 
displayView($conn, "CustomerList", "Customer List", "delete_customer", "CustomerId"); 
displayView($conn, "CommentsList", "Comments List", "delete_comment", "CommentId");


$conn->close();
?>
