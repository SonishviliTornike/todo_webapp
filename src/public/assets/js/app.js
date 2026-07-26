

const tasks = document.querySelectorAll('[data-id]');

tasks.forEach(function (task) {
    task.addEventListener('click', async function () {
        task.classList.toggle('is-done');
        const isCompleted = task.classList.contains('is-done') ? 1 : 0;
        const token = document.querySelector('meta[name="csrf-token"]').content;
        
    
    try {
        const res = await fetch('/tasks/settaskcompleted',  {
            method: 'POST',
            body: new URLSearchParams({
                id: task.dataset.id,
                is_completed: isCompleted,
                csrf_token: token
            })
        
        });
        if (!res.ok) {
            task.classList.toggle('is-done');
            return;
        }
    } catch (err) {
        task.classList.toggle('is-done');
    }

    });
});