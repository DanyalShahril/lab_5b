<?php
// Start the session to check user authentication
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Database connection settings
$servername = "localhost";
$username = "root";    // Your database username
$password = "";        // Your database password
$dbname = "lab_5b"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete operation
if (isset($_GET['delete'])) {
    $matric_to_delete = $_GET['delete'];
    $sql_delete = "DELETE FROM users WHERE matric = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("s", $matric_to_delete);
    $stmt->execute();
    $stmt->close();
    header("Location: display_user.php"); // Refresh the page
    exit();
}

// Fetch all users data
$sql = "SELECT matric, name, ROLE AS accessLevel FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Users Table</title>
    <style>
        table {
            width: 60%;
            border-collapse: collapse;
            margin: 20px auto;
            text-align: center;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
        }
        th {
            background-color: #f2f2f2;
        }
        a {
            margin: 0 5px;
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Users Table</h2>
    <table>
        <tr>
            <th>Matric</th>
            <th>Name</th>
            <th>Level</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['matric']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['accessLevel']) . "</td>";
                echo "<td>
                        <a href='update_user.php?matric=" . urlencode($row['matric']) . "'>Update</a>
                        <a href='display_update_user.php?delete=" . urlencode($row['matric']) . "' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No records found</td></tr>";
        }
        ?>
    </table>
    <div style="text-align: center;">
        <a href='logout.php'>Logout</a>
    </div>
</body>
</html>
<?php $conn->close(); ?>
