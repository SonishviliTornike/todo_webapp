

<form action="update.php" method="post">
    <label for="task_title">Edit Task Title:</label>
    <input type="text" name="task_title" id="task_title" required maxlength="150" value="<?=$task['task_title'];?>"><br><br>

    <label for="task_description">Enter Task:</label><br>
    <textarea id="task_description" name="task_description" rows="4" cols="50" ><?=$task['task_description'];?></textarea><br><br>

    <label for="due_at">Due at:</label>
    <input type="datetime-local" name="due_at" id="due_at" value="<?= $due_at; ?>"><br><br>

    <input type="hidden" name="task_id" id="task_id" value="<?= $task['task_id'] ?>">
    <input type="submit" name="submit" value="Update Task"><br><br>
</form>