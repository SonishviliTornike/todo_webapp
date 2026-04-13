<br><h3 class="total-tasks"><?= 'Total tasks: '. htmlspecialchars((int)$totalTasks[0], ENT_QUOTES, 'UTF-8') ?></h3><br>
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
        <?php foreach ($tasks as $task): ?>
            <tr>    
                <td data-label="Title"><?= htmlspecialchars($task['task_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td data-label="Description"><?= htmlspecialchars($task['task_description'] ?? '', ENT_QUOTES,'UTF-8') ?></td>
                <td data-label="Due"><?= htmlspecialchars($task['due_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <?php
                    $priority = match ((int)$task['priority']){
                        1 => 'High',
                        3 => 'Low',
                        default => 'Medium',
                    }
                ?>
                <td data-label="Priority"><?= htmlspecialchars($priority, ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <form method="post" action="/tasks/setTaskCompleted">
                        <input type="hidden" name="id" value="<?= $task['id'] ?>">
                        <input type="hidden" name="is_completed" value="0">
                        <input type="checkbox"
                            name="is_completed"
                            value="1"
                            onchange="this.form.submit()"
                            <?= $task['is_completed'] ? 'checked' : '' ?>>
                    </form>
                </td>
                <td>
                    <form action="/tasks/insertEdit/<?= $task['id'] ?>" method="get">
                        <input type="hidden" name="id" value="<?= $task['id'] ?>">
                        <input type="submit" value="Edit Task">
                    </form>
                    <form action="/tasks/delete" method="post">
                        <input type="hidden" name='id' value="<?= $task['id'] ?>">
                        <input type="submit" value="Remove Task">
                    </form>
                </td>
                
            </tr>
            <?php endforeach; ?>
    </tbody>

</table>