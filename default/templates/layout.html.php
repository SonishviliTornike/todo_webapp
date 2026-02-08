<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="/assets/imgs/favicon.ico">
</head>
<body>

<header>
    <h1 class="welcome">To Do WebApp</h1>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home Page</a></li>
        <li><a href="addtask.php">Add Task</a></li>
        <li><a href="view_tasks.php">View Task List</a></li>
    </ul>
</nav>

<main>
    <?= $output ?>
</main>

<footer>
    &copy;Todo WebApp — sonishvili.tornike@gmail.com & 2026
</footer>

</body>
</html>
