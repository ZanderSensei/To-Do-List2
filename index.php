<?php
// Assuming you've set the connection string in Azure as MYSQL_CONN_STRING
$connectionString = getenv('MYSQL_CONN_STRING');

// Parse the connection string
$matches = [];
preg_match('/mysql:host=(.+);dbname=(.+);user=(.+);password=(.+)/', $connectionString, $matches);
$host = $matches[1] ?? null;
$dbname = $matches[2] ?? null;
$username = $matches[3] ?? null;
$password = $matches[4] ?? null;

// Establish the database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully to the database.";
}

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
				<form method="POST" class="form-inline" action="add_query.php">
					<input type="text" class="form-control" name="task" required/>
					<button class="btn btn-primary form-control" name="add">Add Task</button>
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
					require 'conn.php';
					$query = $conn->query("SELECT * FROM `task` ORDER BY `task_id` ASC");
					$count = 1;
					while($fetch = $query->fetch_array()){
				?>
				<tr>
					<td><?php echo $count++?></td>
					<td><?php echo $fetch['task']?></td>
					<td><?php echo $fetch['status']?></td>
					<td colspan="2">
						<center>
							<?php
								if($fetch['status'] != "Done"){
									echo 
									'<a href="update_task.php?task_id='.$fetch['task_id'].'" class="btn btn-success"><span class="glyphicon glyphicon-check"></span></a> |';
								}
							?>
							 <a href="delete_query.php?task_id=<?php echo $fetch['task_id']?>" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>
						</center>
					</td>
				</tr>
				<?php
					}
				?>
			</tbody>
		</table>
	</div>
</body>
</html>
?>
