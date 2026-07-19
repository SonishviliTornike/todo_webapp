# Roadmap — Todo Webapp → Production

Rule: phases go in order. A phase is done when its tests pass and the work
is committed. No starting a new phase with an unfinished one open.

---

## Phase 0 — FINISH WHAT IS OPEN (current)

- [+] user_id binding (in progress):
  - [+] Authentication::getUserId(): int (throws RuntimeException when no session)
  - [+] Inject Authentication into Tasks controller (via TaskWebsite)
  - [+] Stamp user_id from session on task insert (never from POST)
  - [+] Scope ALL task reads to owner (list, index high-priority, totalTasks)
  - [+] Scope UPDATE / DELETE / edit-fetch to owner (WHERE ... AND user_id)
  - [+] Guest branch in index(): isLoggedIn() first, no query for guests
  - [+] Decide + document: other user's task → 404 (do not reveal existence)
  - [+] Two-account test: user B cannot see/complete/edit/delete user A's task
  - [+] Commit: "feat: scope tasks to their owner"

## Phase 1 — SECURITY CORE (blockers: no production before these)

- [+] CSRF protection on every state-changing endpoint
  (login, logout, register, task insert/edit, delete, complete).
  One token mechanism, session-stored, checked centrally.
- [ ] Session hardening:
  - [ ] session_regenerate_id(true) on login (delete old session)
  - [ ] Cookie params: HttpOnly, SameSite=Lax (or Strict), Secure (with HTTPS)
  - [ ] Full logout: destroy session data AND expire the cookie
- [ ] Login brute-force protection (minimum: per-account attempt counter + delay)
- [ ] Authentication should honor is_active flag; update last_login

## Phase 2 — CORRECTNESS DEBT (known bugs, small, do in one sweep)

- [ ] Register form repopulation reads wrong keys (userName/fullName
      vs user_name/full_name)
- [ ] "Password confrimation" label typo
- [ ] Dead link: hero "Start your list" → /users/registrationform (renamed)
- [ ] Dead link: "Forgot password?" → /login/forgot (404s; hide until Phase 4)
- [ ] checkLogin redirect: set explicit status code (no implicit 302)
- [ ] Global catch (\Throwable) safety net in EntryPoint (TypeError,
      UnhandledMatchError are Errors — current catches miss them)
- [ ] display_errors=0 in php.ini for prod profile; errors to log only

## Phase 3 — ARCHITECTURE CLEANUP

- [ ] Move EntryPoint.php + Website.php out of src/Model (routing, not model);
      update namespaces + references (index.php, TaskWebsite). Own commit.
- [ ] Resolve DatabaseTable vs TasksTable duplication: one gateway per table
      (TasksTable absorbs or extends; controller gets ONE dependency)
- [ ] insertEdit(int $taskId): use ?int + validate route params before typed
      calls (a string like /tasks/insertedit/abc must 404, not TypeError)
- [ ] Later (only when needed): narrow interface for Authentication
      (interface segregation); Response object instead of echo-in-controller

## Phase 4 — FORGOT PASSWORD (first full feature end-to-end)

- [ ] password_resets table: user_id, token HASH (never plain), expires_at, used_at
- [ ] Request form: always answer "if this email exists, we sent a link"
      (do not reveal which emails are registered)
- [ ] Single-use, time-limited token; invalidate on use and on password change
- [ ] Reset form + validation reusing RegisterValidation password rules
- [ ] Email sending (dev: log/mailpit container; prod: SMTP)

## Phase 5 — QUALITY TOOLING (parallel-friendly, small doses)

- [ ] PHPStan: phpstan.neon, start level 1, raise one level at a time,
      fix as you go; composer script "analyse"
- [ ] PHP-CS-Fixer with PSR-12 ruleset; composer script "fix"
- [ ] PHPUnit: start with validators (pure, no DB), then TasksTable
      against a test database
- [ ] Run analyse + tests before every commit (later: git hook)

## Phase 6 — PRODUCTION OPERATIONS

- [ ] Prod docker profile: no source volume mount (COPY in image),
      opcache on, display_errors off
- [ ] HTTPS (reverse proxy / Caddy or certbot) — required for Secure cookies
- [ ] MySQL backups (daily dump + restore test — a backup you never
      restored is not a backup)
- [ ] Secrets: prod .env never in git, different passwords than dev
- [ ] Log rotation / at least a habit of reading logs
- [+] Move repo out of OneDrive-synced folders (NUL-byte corruption
      happened twice; Desktop is often synced on Windows — verify)

## Phase 7 — PRODUCT POLISH (after everything above)

- [ ] Pagination or limit on task list
- [ ] Show completed state on home page list
- [ ] Flash messages ("task saved") instead of silent redirects
- [ ] Empty states, mobile pass, accessibility pass (keyboard on checkboxes)

---

Working rules (constant, every phase):
1. One logical change per commit; read `git diff HEAD` before committing.
2. Commit messages: what + why; you write them, mentor reviews.
3. Every change: test in browser + check logs before calling it done.
4. Every lesson: one line in NOTES.md in your own words.
