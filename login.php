<?php
// Start session to store user login status
session_start();

// Database connection settings
$servername = "localhost";
$username = "root";    // Your database username
$password = "";        // Your database password
$dbname = "lab_5b";    // Replace with your database name

// Error message variable
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $matric = $_POST['matric'];
    $password_input = $_POST['password'];

    // Prepare and execute SQL query to fetch user details
    $sql = "SELECT matric, name, password, ROLE FROM users WHERE matric = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $matric);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password_input, $row['password'])) {
            // Authentication successful, set session and redirect based on role
            $_SESSION['user'] = $row['name'];
            $_SESSION['role'] = $row['ROLE']; // Store the role in session

            // Redirect to different pages based on user role
            if ($_SESSION['role'] == 'student') {
                header("Location: display_user.php"); // Redirect to the students' page
            } else if ($_SESSION['role'] == 'lecturer') {
                header("Location: display_update_user.php"); // Redirect to the lecturers' page
            }
            exit();
        } else {
            $error_message = "Invalid password!";
        }
    } else {
        $error_message = "Invalid matric number!";
    }

    // Close connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        form {
            display: inline-block;
            text-align: left;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        input, label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 3px;
        }
        a {
            display: block;
            margin-top: 10px;
            text-decoration: none;
            color: #007BFF;
        }
        p.error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>Login</h2>
    <form method="POST" action="">
        <label for="matric">Matric:</label>
        <input type="text" id="matric" name="matric" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Login">

        <?php
        if (!empty($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>
    </form>
    <a href="registration.php">Don't have an account? Register here</a>
</body>
</html>
