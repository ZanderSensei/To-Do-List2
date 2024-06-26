<?php
// Enable error reporting for debugging (remove or modify in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);


// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:zarserver2.database.windows.net,1433; Database = ZarApp2", "zander", "Zar12345");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "zander", "pwd" => "Zar12345", "Database" => "ZarApp2", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:zarserver2.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);


// Environment variables for database connection
$host = getenv('DB_HOST') ?: 'localhost'; // Using 'localhost' as a fallback
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');

// Initialize MySQLi object
$conn = mysqli_init();
if (!$conn) {
    die('mysqli_init failed');
}

// Specify MySQL connection needs to use SSL
mysqli_ssl_set($conn, NULL, NULL, '/home/site/wwwroot/DigiCertGlobalRootCA.crt.pem', NULL, NULL);

// Attempt to establish the secure database connection
if (!mysqli_real_connect($conn, $host, $username, $password, $dbname, 3306, NULL, MYSQLI_CLIENT_SSL)) {
    die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
}

// Actions
if(isset($_POST['add']) && !empty($_POST['task'])) {
    $task = $_POST['task'];
    $status = 'Pending'; // Default status
    $stmt = $conn->prepare("INSERT INTO tasks (task, status) VALUES (?, ?)");
    $stmt->bind_param("ss", $task, $status);
    $stmt->execute();
    header('Location: index.php');
    exit;
}

if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    $conn->query("DELETE FROM tasks WHERE `task_id` = $task_id");
    header('Location: index.php');
    exit;
}

if(isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    $conn->query("UPDATE tasks SET `status` = 'Done' WHERE `task_id` = $task_id");
    header('Location: index.php');
    exit;
}

echo "Connected successfully to the database using SSL.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1"/>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <a class="navbar-brand" href="https://www.linkedin.com/in/alex-samia-5066b51b4/">ZanderToDoList</a>
    </div>
</nav>
<div class="col-md-3"></div>
<div class="col-md-6 well">
    <h3 class="text-primary">PHP - Simple To Do List App</h3>
    <hr style="border-top:1px dotted #ccc;"/>
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <center>
            <form method="POST" class="form-inline">
                <input type="text" class="form-control" name="task" required/>
                <button type="submit" class="btn btn-primary form-control" name="add">Add Task</button>
            </form>
        </center>
    </div>
    <br /><br /><br />
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Task</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $query = $conn->query("SELECT * FROM tasks ORDER BY `task_id` ASC");
        $count = 1;
        while ($fetch = $query->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $count++ . "</td>";
            echo "<td>" . htmlspecialchars($fetch['task']) . "</td>";
            echo "<td>" . htmlspecialchars($fetch['status']) . "</td>";
            echo "<td colspan='2'><center>";
            echo '<a href="?action=update&task_id=' . $fetch['task_id'] . '" class="btn btn-success">Mark as Done</a> | ';
            echo '<a href="?action=delete&task_id=' . $fetch['task_id'] . '" class="btn btn-danger">Delete</a>';
            echo "</center></td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
