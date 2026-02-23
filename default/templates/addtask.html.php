
<form action="addtask.php" method="post">
    <label for="task_title">Enter Task Title:</label>
    <input type="text" name="task_title" id="task_title" required maxlength="150" value="<?= htmlspecialchars($old_values['task_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"><br><br>

    <label for="task_description">Enter Task:</label><br>
    <textarea id="task_description" name="task_description" rows="4" cols="50"  value="<?= htmlspecialchars($old_values['task_description'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required></textarea><br><br>

    <label for="due_at">Due at:</label>
    <input type="datetime-local" name="due_at" id="due_at" value="<?= htmlspecialchars($old_values['due_at'] ?? '' , ENT_QUOTES, 'UTF-8') ?>"><br><br>

    <label for="priority">Enter Priority:</label>
    <select name="priority" id="priority" required>
        <option value="1">High</option>
        <option value="2" selected>Medium</option>
        <option value="3">Low</option>
    </select><br><br>


    <input type="submit" name="submit" value="Add Task"><br><br>
    
</form>

<?php if (!empty($errors)): ?>
    <div class="errors-container">
        <?php foreach ($errors as $err_key => $err_message_array):?>
            <?php foreach ($err_message_array as $message):?>
                <p class="error-text">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?><br>
                </p>
            <?php endforeach; ?>
        <?php endforeach;?>
    </div>
<?php endif; ?>
