
<section class="wrap hero">
  <div class="hero__copy">
    <span class="eyebrow">A todo app that gets out of the way</span>
    <h1 class="hero__title">Add it. Do it.<br><span class="struck">Cross it off.</span></h1>
    <p class="hero__sub">Tally keeps every task in one plain list and nothing else. The only thing left to think about is finishing them.</p>
    <?php if ($isLoggedIn === false): ?>
      <div class="hero__cta">
        <a class="btn btn--primary btn--lg" href="/users/registrationform">Start your list</a>
        <a class="btn btn--ghost btn--lg" href="/login/login">Log in</a>
      </div>
    <?php endif;?>
    <p class="hero__note">// free to start · no setup · open it and type</p>
  </div>

  <?php if ($isLoggedIn === true): ?>
    <?php if (isset($tasks)):?>
    <div class="demo" aria-label="Example task list">
      <div class="demo__head">
        <span class="demo__title">Today</span>
        <span class="demo__count" id="count">0 of 4 done</span>
      </div>
          <?php foreach ($tasks as $task):?>
            <div class="task" data-task role="button" tabindex="0" aria-pressed="false">
              <span class="task__box"><svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></span>
              <span class="task__label"><?= htmlspecialchars($task['task_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
          <?php endforeach;?>
      </div>
    <?php endif;?>
  <?php  else: ?>
    
    <div class="demo" aria-label="Example task list">
      <div class="demo__head">
        <span class="demo__title">Today</span>
        <span class="demo__count" id="count">0 of 4 done</span>
      </div>
      <div class="task" data-task role="button" tabindex="0" aria-pressed="false">
        <span class="task__box"><svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></span>
        <span class="task__label">Push the migration script</span>
      </div>
      <div class="task" data-task role="button" tabindex="0" aria-pressed="false">
        <span class="task__box"><svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></span>
        <span class="task__label">Book the dentist</span>
      </div>
      <div class="task" data-task role="button" tabindex="0" aria-pressed="false">
        <span class="task__box"><svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></span>
        <span class="task__label">Clean the house</span>
      </div>
      <div class="task" data-task role="button" tabindex="0" aria-pressed="false">
        <span class="task__box"><svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></span>
        <span class="task__label">Read one chapter</span>
      </div>
      <div class="demo__add">
        <span class="task__box" aria-hidden="true"></span>
        Add a task…
      </div>
    <?php endif; ?>
  </div>
  
</section>

<section class="wrap features">
  <span class="eyebrow">Three moves, that's the whole app</span>
  <div class="features__grid">
    <article class="feature">
      <span class="feature__num">01</span>
      <h3>Capture</h3>
      <p>Type a task on one line and it's saved. No forms, no fields, no setup screen standing between you and the thing you remembered.</p>
    </article>
    <article class="feature">
      <span class="feature__num">02</span>
      <h3>Organize</h3>
      <p>Keep today separate from someday. What's urgent rises to the top; the rest waits quietly until you're ready for it.</p>
    </article>
    <article class="feature">
      <span class="feature__num">03</span>
      <h3>Complete</h3>
      <p>Check it off and watch it strike through. That small, satisfying moment is the entire point of keeping a list.</p>
    </article>
  </div>
</section>

<section class="wrap closing">
  <span class="eyebrow">Ready when you are</span>
  <h2>Your first task is signing up.</h2>
  <?php if ($isLoggedIn === false): ?>
    <a class="btn btn--primary btn--lg" href="/users/registrationform">Start your list</a>
  <?php else: ?>
    <a class="btn btn--primary btn--lg" href="/tasks/insertedit">Start your list</a>
  <?php endif;?>
</section>

<script>
  (function () {
    const tasks = Array.from(document.querySelectorAll('[data-task]'));
    const count = document.getElementById('count');
    if (!tasks.length || !count) return;

    function render() {
      const done = tasks.filter(t => t.classList.contains('is-done')).length;
      count.textContent = done + ' of ' + tasks.length + ' done';
    }
    function toggle(el) {
      const done = el.classList.toggle('is-done');
      el.setAttribute('aria-pressed', String(done));
      render();
    }

    tasks.forEach(t => {
      t.addEventListener('click', () => toggle(t));
      t.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(t); }
      });
    });

    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduce) {
      setTimeout(() => toggle(tasks[0]), 900);
      setTimeout(() => toggle(tasks[2]), 1500);
    }
  })();
</script>