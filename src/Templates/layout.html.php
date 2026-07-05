<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <script>
        /* Set theme before first paint to avoid a flash of the wrong theme.
           Manual choice (localStorage) wins; otherwise follow the OS setting. */
        (function () {
            try {
                var t = localStorage.getItem('theme');
                if (t !== 'light' && t !== 'dark') {
                    t = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }
                document.documentElement.dataset.theme = t;
            } catch (e) {}
        })();
    </script>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/app.js" defer></script>
    <link rel="icon" href="/assets/imgs/favicon.ico">
</head>
<body>

<header>
    <h1 class="welcome">To Do WebApp</h1>
</header>

<nav>
    <ul>
        <li><a href="/">Home Page</a></li>
        <li><a href="/tasks/insertedit">Add Task</a></li>
        <li><a href="/tasks/list">View Task List</a></li>
        <?php if ($isLoggedIn === false): ?>
            <li><a href="/login/login">Log in</a></li>
        <?php else:?>
            <li><form action="/login/logout" method="post" class="logout-form">
                <button type="submit">Log out</button>
            </form></li>
        <?php endif;?>
        <li class="nav-spacer"></li>
        <li>
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode" title="Toggle theme">
                <svg class="icon-moon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg class="icon-sun" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
            </button>
        </li>
    </ul>
</nav>

<main>
    <?= $output ?>
</main>

<footer>
    &copy;Todo WebApp — sonishvili.tornike@gmail.com & 2026
</footer>

<script>
    (function () {
        var btn = document.getElementById('themeToggle');
        if (!btn) return;
        btn.addEventListener('click', function () {
            var root = document.documentElement;
            var next = root.dataset.theme === 'dark' ? 'light' : 'dark';
            root.dataset.theme = next;
            try { localStorage.setItem('theme', next); } catch (e) {}
        });
    })();
</script>

</body>
</html>
