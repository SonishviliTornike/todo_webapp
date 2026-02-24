<table class="tasks-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Task</th>
            <th>Due at</th>
            <th>Priority</th>
            <th>Completed</th>
            <th>Actions</th>
        </tr>
        </thead>
    <tbody>
        <br><h3 class="total-tasks"><?= 'Total tasks: '. $totalTasks ?></h3><br>
        <?php foreach ($tasks as $task): ?>
            <tr>    
                <td><?= htmlspecialchars($task['task_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($task['task_description'] ?? '', ENT_QUOTES,'UTF-8') ?></td>
                <td><?= htmlspecialchars($task['due_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <?php $priority = $task['priority'];
                    $priority = match ((int)$task['priority']){
                        1 => 'High',
                        default => 'Medium',
                        3 => 'Low',
                    }
                ?>
                <td><?= $priority ?></td>
                <td>
                    <form method="post" action="toggle_task.php">
                        <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                        <input type="hidden" name="is_completed" value="0">
                        <input type="checkbox"
                            name="is_completed"
                            value="1"
                            onchange="this.form.submit()"
                            <?= $task['is_completed'] ? 'checked' : '' ?>>
                    </form>
                </td>
                <td>
                    <form action="insertEdit.php" method="get">
                        <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                        <input type="submit" value="Edit Task">
                    </form>
                </td>
                <td>
                    <form action="delete.php" method="post">
                        <input type="hidden" name='task_id' value="<?= $task['task_id'] ?>">
                        <input type="submit" value="Remove Task">
                    </form>
                </td>
                
            </tr>
            <?php endforeach; ?>
    </tbody>

</table>