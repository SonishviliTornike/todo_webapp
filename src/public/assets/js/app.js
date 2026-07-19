

const tasks = document.querySelectorAll('[data-id]');

tasks.forEach(function (task) {
    task.addEventListener('click', async function () {
        task.classList.toggle('is-done');
        const isCompleted = task.classList.contains('is-done') ? 1 : 0;
        
    
    const token = document.querySelector('meta[name="csrf-token"]').content;

    const res = await fetch('/tasks/settaskcompleted',  {
        method: 'POST',
        body: new URLSearchParams({
            id: task.dataset.id,
            is_completed: isCompleted,
            csrf_token: token
        })
    });

    const data = await res.json();
    console.log('server replied:', data);
    });
});