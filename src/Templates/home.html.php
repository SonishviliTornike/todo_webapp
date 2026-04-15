<br><h1 class="welcome-h1">Welcome To do Web App</h1><br>


<table class="tasks-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Task</th>
            <th>Due at</th>
            <th>Priority</th>
        </tr>
        </thead>
    <tbody>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?= htmlspecialchars($task['task_title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($task['task_description'] ?? '', ENT_QUOTES,'UTF-8') ?></td>
                <td><?= htmlspecialchars($task['due_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                <?php
                    $priority = match((int)$task['priority']){
                        1 => 'High',
                        3 => 'Low',
                        default => 'Medium',
                    } 
                ?>
                <td><?= htmlspecialchars($priority, ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>