<?php
// Start session
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lab_5b";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$matric = $name = $role = "";

// Fetch data for the given matric
if (isset($_GET['matric'])) {
    $matric = $_GET['matric'];
    $sql = "SELECT name, ROLE FROM users WHERE matric = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $matric);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $role = $row['ROLE'];
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric = $_POST['matric'];
    $name = $_POST['name'];
    $role = $_POST['role'];

    $sql_update = "UPDATE users SET name = ?, ROLE = ? WHERE matric = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sss", $name, $role, $matric);

    if ($stmt->execute()) {
        header("Location: display_user.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        form {
            display: inline-block;
            text-align: left;
            padding: 50px;
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
    <h2>Update User</h2>
    <form method="POST" action="">
        <label>Matric:</label>
        <input type="text" name="matric" value="<?php echo htmlspecialchars($matric); ?>" required><br><br>
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br><br>
        <label>Role:</label>
        <select name="role" required>
            <option value="student" <?php if ($role == 'student') echo 'selected'; ?>>Student</option>
            <option value="lecturer" <?php if ($role == 'lecturer') echo 'selected'; ?>>Lecturer</option>
        </select><br><br>
        <input type="submit" value="Update">
    </form>
</body>
</html>
<?php $conn->close(); ?>
