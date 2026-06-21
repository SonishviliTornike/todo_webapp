<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tally — add it, do it, cross it off</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

  <style>
    :root {
      --paper:    #F7F6F2;
      --ink:      #15161B;
      --muted:    #5A5B66;
      --brand:    #5B47E0;
      --brand-ink:#4A37C9;
      --done:     #1FA971;
      --hairline: #E3E1DA;
      --card:     #FFFFFF;

      --display: "Bricolage Grotesque", system-ui, sans-serif;
      --body:    "Inter", system-ui, sans-serif;
      --mono:    "JetBrains Mono", ui-monospace, monospace;

      --maxw: 1080px;
      --pad: clamp(1.25rem, 4vw, 3rem);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    html { scroll-behavior: smooth; }

    body {
      font-family: var(--body);
      color: var(--ink);
      background: var(--paper);
      line-height: 1.55;
      -webkit-font-smoothing: antialiased;
    }

    a { color: inherit; text-decoration: none; }

    .wrap {
      max-width: var(--maxw);
      margin-inline: auto;
      padding-inline: var(--pad);
    }

    .eyebrow {
      font-family: var(--mono);
      font-size: 0.72rem;
      letter-spacing: 0.16em;
      text-transform: uppercase;
      color: var(--muted);
    }

    /* ---------- Nav ---------- */
    .nav {
      position: sticky;
      top: 0;
      z-index: 10;
      background: color-mix(in srgb, var(--paper) 86%, transparent);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--hairline);
    }
    .nav__inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 68px;
    }
    .brand {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      font-family: var(--display);
      font-weight: 800;
      font-size: 1.28rem;
      letter-spacing: -0.02em;
    }
    .brand__mark {
      width: 26px; height: 26px;
      border-radius: 7px;
      background: var(--brand);
      display: grid;
      place-items: center;
      color: #fff;
      flex: 0 0 auto;
    }
    .brand__mark svg { width: 15px; height: 15px; }
    .nav__actions { display: flex; align-items: center; gap: 0.5rem; }

    /* ---------- Buttons ---------- */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-family: var(--body);
      font-weight: 600;
      font-size: 0.95rem;
      padding: 0.62rem 1.15rem;
      border-radius: 10px;
      border: 1px solid transparent;
      cursor: pointer;
      transition: transform 0.12s ease, background 0.15s ease, border-color 0.15s ease;
    }
    .btn:active { transform: translateY(1px); }
    .btn--primary {
      background: var(--brand);
      color: #fff;
    }
    .btn--primary:hover { background: var(--brand-ink); }
    .btn--ghost {
      background: transparent;
      color: var(--ink);
      border-color: var(--hairline);
    }
    .btn--ghost:hover { border-color: var(--ink); }
    .btn--text { background: transparent; padding-inline: 0.4rem; }
    .btn--text:hover { color: var(--brand); }
    .btn--lg { font-size: 1.02rem; padding: 0.8rem 1.5rem; }

    /* ---------- Hero ---------- */
    .hero {
      display: grid;
      grid-template-columns: 1.05fr 0.95fr;
      gap: clamp(2rem, 6vw, 5rem);
      align-items: center;
      padding-block: clamp(3.5rem, 9vw, 7rem);
    }
    .hero__title {
      font-family: var(--display);
      font-weight: 800;
      font-size: clamp(2.6rem, 6.2vw, 4.4rem);
      line-height: 1.02;
      letter-spacing: -0.035em;
      margin-block: 1.1rem 1.3rem;
    }
    .hero__title .struck {
      position: relative;
      color: var(--muted);
    }
    .hero__title .struck::after {
      content: "";
      position: absolute;
      left: -0.02em; right: -0.02em;
      top: 52%;
      height: 0.07em;
      background: var(--brand);
      border-radius: 2px;
      transform: scaleX(0);
      transform-origin: left;
      animation: strike 0.5s 0.7s ease forwards;
    }
    @keyframes strike { to { transform: scaleX(1); } }

    .hero__sub {
      font-size: clamp(1.05rem, 1.5vw, 1.18rem);
      color: var(--muted);
      max-width: 38ch;
      margin-bottom: 1.9rem;
    }
    .hero__cta { display: flex; flex-wrap: wrap; gap: 0.75rem; }
    .hero__note {
      margin-top: 1.1rem;
      font-family: var(--mono);
      font-size: 0.74rem;
      color: var(--muted);
      letter-spacing: 0.04em;
    }

    /* ---------- Signature: the live list ---------- */
    .demo {
      background: var(--card);
      border: 1px solid var(--hairline);
      border-radius: 18px;
      padding: 1.4rem 1.4rem 1.1rem;
      box-shadow: 0 1px 0 rgba(21,22,27,0.02), 0 24px 50px -28px rgba(21,22,27,0.22);
    }
    .demo__head {
      display: flex;
      align-items: baseline;
      justify-content: space-between;
      padding-bottom: 0.9rem;
      margin-bottom: 0.4rem;
      border-bottom: 1px solid var(--hairline);
    }
    .demo__title { font-family: var(--display); font-weight: 700; font-size: 1.05rem; }
    .demo__count { font-family: var(--mono); font-size: 0.74rem; color: var(--muted); }

    .task {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.7rem 0.4rem;
      border-radius: 9px;
      cursor: pointer;
      user-select: none;
      transition: background 0.12s ease;
    }
    .task:hover { background: #F4F3EF; }
    .task__box {
      width: 21px; height: 21px;
      flex: 0 0 auto;
      border: 2px solid var(--hairline);
      border-radius: 6px;
      display: grid;
      place-items: center;
      transition: background 0.18s ease, border-color 0.18s ease;
    }
    .task__box svg {
      width: 12px; height: 12px;
      stroke: #fff;
      stroke-width: 3;
      stroke-linecap: round;
      stroke-linejoin: round;
      fill: none;
      stroke-dasharray: 22;
      stroke-dashoffset: 22;
      transition: stroke-dashoffset 0.22s ease 0.04s;
    }
    .task__label {
      font-size: 0.98rem;
      position: relative;
      transition: color 0.18s ease;
    }
    .task__label::after {
      content: "";
      position: absolute;
      left: 0; right: 0; top: 50%;
      height: 2px;
      background: var(--muted);
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 0.24s ease;
    }
    .task.is-done .task__box { background: var(--done); border-color: var(--done); }
    .task.is-done .task__box svg { stroke-dashoffset: 0; }
    .task.is-done .task__label { color: var(--muted); }
    .task.is-done .task__label::after { transform: scaleX(1); }

    .demo__add {
      display: flex;
      align-items: center;
      gap: 0.6rem;
      margin-top: 0.5rem;
      padding: 0.7rem 0.4rem;
      color: var(--muted);
      font-size: 0.95rem;
    }
    .demo__add .task__box { border-style: dashed; }

    /* ---------- Features ---------- */
    .features { padding-block: clamp(3rem, 7vw, 5.5rem); border-top: 1px solid var(--hairline); }
    .features__grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: clamp(1.25rem, 3vw, 2.25rem);
      margin-top: 2.2rem;
    }
    .feature__num {
      font-family: var(--mono);
      font-size: 0.78rem;
      color: var(--brand);
      letter-spacing: 0.1em;
    }
    .feature h3 {
      font-family: var(--display);
      font-weight: 700;
      font-size: 1.3rem;
      letter-spacing: -0.02em;
      margin-block: 0.6rem 0.5rem;
    }
    .feature p { color: var(--muted); font-size: 0.98rem; }

    /* ---------- Closing CTA ---------- */
    .closing {
      padding-block: clamp(3.5rem, 8vw, 6rem);
      text-align: center;
      border-top: 1px solid var(--hairline);
    }
    .closing h2 {
      font-family: var(--display);
      font-weight: 800;
      font-size: clamp(2rem, 5vw, 3.1rem);
      letter-spacing: -0.03em;
      line-height: 1.05;
      max-width: 16ch;
      margin: 0.8rem auto 1.6rem;
    }

    /* ---------- Footer ---------- */
    .footer {
      border-top: 1px solid var(--hairline);
      padding-block: 2rem;
    }
    .footer__inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 1rem;
    }
    .footer small { color: var(--muted); font-size: 0.85rem; }
    .footer__links { display: flex; gap: 1.25rem; font-size: 0.9rem; }
    .footer__links a:hover { color: var(--brand); }

    /* ---------- Responsive ---------- */
    @media (max-width: 820px) {
      .hero { grid-template-columns: 1fr; }
      .demo { order: -1; }
      .features__grid { grid-template-columns: 1fr; gap: 1.5rem; }
    }
    @media (max-width: 480px) {
      .nav__actions .btn--text { display: none; }
    }

    @media (prefers-reduced-motion: reduce) {
      *, *::after { animation: none !important; transition: none !important; }
      .hero__title .struck::after { transform: scaleX(1); }
    }

    :focus-visible { outline: 2px solid var(--brand); outline-offset: 2px; border-radius: 4px; }
  </style>
</head>
<body>

  <!-- NAV -->
  <header class="nav">
    <div class="wrap nav__inner">
      <a class="brand" href="/" aria-label="Tally home">
        <span class="brand__mark" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        Tally
      </a>
      <nav class="nav__actions">
        <a class="btn btn--text" href="/login">Log in</a>
        <a class="btn btn--primary" href="/register">Start your list</a>
      </nav>
    </div>
  </header>

  <!-- HERO -->
  <main>
    <section class="wrap hero">
      <div class="hero__copy">
        <span class="eyebrow">A todo app that gets out of the way</span>
        <h1 class="hero__title">Add it. Do it.<br><span class="struck">Cross it off.</span></h1>
        <p class="hero__sub">Tally keeps every task in one plain list and nothing else. The only thing left to think about is finishing them.</p>
        <div class="hero__cta">
          <a class="btn btn--primary btn--lg" href="/register">Start your list</a>
          <a class="btn btn--ghost btn--lg" href="/login">Log in</a>
        </div>
        <p class="hero__note">// free to start · no setup · open it and type</p>
      </div>

      <!-- SIGNATURE: a real, clickable list -->
      <div class="demo" aria-label="Example task list">
        <div class="demo__head">
          <span class="demo__title">Today</span>
          <span class="demo__count" id="count">0 of 4 done</span>
        </div>

        <div class="task" data-task role="button" tabindex="0" aria-pressed="false">
          <span class="task__box"><svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg></span>
          <span class="task__label">Reply to the landlord</span>
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
          <span class="task__label">Read one chapter</span>
        </div>

        <div class="demo__add">
          <span class="task__box" aria-hidden="true"></span>
          Add a task…
        </div>
      </div>
    </section>

    <!-- FEATURES -->
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

    <!-- CLOSING -->
    <section class="wrap closing">
      <span class="eyebrow">Ready when you are</span>
      <h2>Your first task is signing up.</h2>
      <a class="btn btn--primary btn--lg" href="/register">Start your list</a>
    </section>
  </main>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="wrap footer__inner">
      <small>© <span id="year">2026</span> Tally</small>
      <nav class="footer__links">
        <a href="/login">Log in</a>
        <a href="/register">Sign up</a>
      </nav>
    </div>
  </footer>

  <script>
    // Presentation only — toggling the demo list. Real state lives server-side.
    (function () {
      const tasks = Array.from(document.querySelectorAll('[data-task]'));
      const count = document.getElementById('count');

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

      document.getElementById('year').textContent = new Date().getFullYear();

      // Gentle auto-demo: check the first two once, on load, unless reduced motion.
      const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      if (!reduce) {
        setTimeout(() => toggle(tasks[0]), 900);
        setTimeout(() => toggle(tasks[2]), 1500);
      }
    })();
  </script>
</body>
</html>