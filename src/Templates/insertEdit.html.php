

<form action="/tasks/insertedit" method="post">
    <label for="task_title">Enter Task Title:</label>
    <input type="text" name="task[task_title]" id="task_title" required maxlength="150" value="<?= htmlspecialchars($task['task_title'] ?? '', ENT_QUOTES, 'UTF-8');?>"><br><br>

    <label for="task_description">Enter Task:</label><br>
    <textarea id="task_description" name="task[task_description]" rows="4" cols="50" ><?=htmlspecialchars($task['task_description'] ?? '', ENT_QUOTES, 'UTF-8');?></textarea><br><br>

    <label for="due_at">Due at:</label>
    <input type="datetime-local" name="task[due_at]" id="due_at" value="<?= htmlspecialchars($task['due_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br><br>

    <?php $currentPrioirty = $task['priority'] ?? 2 ?>
    <select name="task[priority]" id="priority" required>
        <option value="1" <?= $currentPrioirty == 1 ? 'selected' : '' ?>>High</option>
        <option value="2" <?= $currentPrioirty == 2 ? 'selected' : '' ?>>Medium</option>
        <option value="3" <?= $currentPrioirty == 3 ? 'selected' : '' ?>>Low</option>
    </select><br><br>

    <input type="hidden" name="task[id]" id="id" value="<?= $task['id']?>">
    <div style="display:flex; gap:12px; align-items:center;">
        <input type="submit" name="submit" value="Save">
        <a href="/tasks/list" class="btn">Return</a>
    </div>
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