

<form action="update.php" method="post">
    <label for="task_title">Edit Task Title:</label>
    <input type="text" name="task_title" id="task_title" required maxlength="150" value="<?= htmlspecialchars($old_tasks['task_title'], ENT_QUOTES, 'UTF-8');?>"><br><br>

    <label for="task_description">Enter Task:</label><br>
    <textarea id="task_description" name="task_description" rows="4" cols="50" ><?=htmlspecialchars($old_tasks['task_description'], ENT_QUOTES, 'UTF-8');?></textarea><br><br>

    <label for="due_at">Due at:</label>
    <input type="datetime-local" name="due_at" id="due_at" value="<?= htmlspecialchars($old_tasks['due_at'], ENT_QUOTES, 'UTF-8'); ?>"><br><br>

    <input type="hidden" name="task_id" id="task_id" value="<?= $old_tasks['task_id'] ?>">
    <input type="submit" name="submit" value="Update Task"><br><br>
</form>