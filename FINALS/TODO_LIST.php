<?php

session_start();

if (!isset($_SESSION['userDbName'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "todo";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['task']) && !empty(trim($_POST['task']))) { 
    $task = $_POST['task'];
    $sql = "INSERT INTO todos (task) VALUES ('$task')";
    $conn->query($sql);
}

if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $isDone = isset($_POST['is_done']) ? 1 : 0;
    $sql = "UPDATE todos SET is_done=$isDone WHERE id=$id";
    $conn->query($sql);
}

if (isset($_POST['edit_task']) && !empty(trim($_POST['new_task']))) {
    $id = $_POST['id'];
    $newTask = $_POST['new_task'];
    $sql = "UPDATE todos SET task='$newTask' WHERE id=$id";
    $conn->query($sql);
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM todos WHERE id=$id";
    $conn->query($sql);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT * FROM todos ORDER BY id DESC LIMIT 500";
$result = $conn->query($sql);

$totalTasks = $result->num_rows;
$completedTasks = 0;
while ($row = $result->fetch_assoc()) {
    if ($row['is_done'] == 1) {
        $completedTasks++;
    }
}
$progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

$result->data_seek(0); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Edu+TAS+Beginner:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Edu+TAS+Beginner:wght@400;700&family=Signika+Negative:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        .edu-tas-beginner-task-header {
            font-family: "Edu TAS Beginner", cursive;
            font-optical-sizing: auto;
            font-weight: 500;
            font-style: normal;
        }

        .edu-tas-beginner-list-item {
            font-family: "Edu TAS Beginner", cursive;
            font-optical-sizing: auto;
            font-weight: 530;
            font-style: normal;
        }

        .signika-negative-list-item {
            font-family: "Signika Negative", sans-serif;
            font-optical-sizing: auto;
            font-weight: 700;
            font-style: normal;
        }

        .signika-negative-list-item1 {
            font-family: "Signika Negative", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

        .signika-negative-button {
            font-family: "Signika Negative", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }

    </style>
</head>

<body class="background-image">
    <div class="container">
        <h2 class="header signika-negative-list-item">
            <img src="pink (1).png" class="pink-image1">
            TODO LIST
            <img src="pink.png" class="pink-image">
        </h2>
        <div style="display: flex; justify-content: center;">
            <form method="post" style="display: flex; align-items: center;">
                <input type="text" name="task" placeholder="Enter task"> 
                <button type="submit" class="signika-negative-button">Add</button>
            </form>
        </div>
        <p class="signika-negative-list-item1">Progress: <?php echo $progress; ?>%</p>
        <ul>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="li-box">
            <li style="list-style: none;">
                <form method="post" id="form_<?php echo $row['id']; ?>" style="display: flex; align-items: center; justify-content: space-between; margin: 0; padding: 5px;">
                    <div style="display: flex; align-items: center;">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="checkbox" name="is_done" <?php if ($row['is_done']) echo "checked"; ?> onchange="submitForm(<?php echo $row['id']; ?>)" style="margin-right: 5px;">
                        <span id="task_<?php echo $row['id']; ?>" class="edu-tas-beginner-list-item" style="text-decoration: <?php echo $row['is_done'] ? 'line-through' : 'none'; ?>"> 🌸 <?php echo $row['task']; ?> 💮 </span>
                        <input type="text" name="new_task" id="edit_input_<?php echo $row['id']; ?>" value="<?php echo $row['task']; ?>" style="display: none; margin-right: 5px;">
                    </div>
                    <div>
                        <button type="button" onclick="toggleEdit(<?php echo $row['id']; ?>)" class="signika-negative-button">Edit</button>
                        <button type="submit" name="delete" class="signika-negative-button">Delete</button>
                        <button type="submit" name="edit_task" id="save_<?php echo $row['id']; ?>" style="display: none;" class="signika-negative-button">Save</button>
                        <input type="hidden" name="update_status" value="1">
                    </div>
                </form>
            </li>
            </div>
            <?php endwhile; ?>
        </ul>
    </div>
    <script>
        function submitForm(id) {
            document.getElementById('form_' + id).submit();
        }

        function toggleEdit(id) {
            var taskSpan = document.getElementById('task_' + id);
            var editInput = document.getElementById('edit_input_' + id);
            var saveButton = document.getElementById('save_' + id);

            if (taskSpan.style.display === 'none') {
                taskSpan.style.display = 'block';
                editInput.style.display = 'none';
                saveButton.style.display = 'none';
            } else {
                taskSpan.style.display = 'none';
                editInput.style.display = 'block';
                saveButton.style.display = 'inline';
            }
        }
    </script>
</body>
</html>
