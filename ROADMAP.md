# Roadmap — Todo Webapp → Production

Updated: 2026-07-19 (full project review by mentor)

Rule: phases go in order. A phase is done when its tests pass and the work
is committed. No starting a new phase with an unfinished one open.

Legend: [ ] open · [+] done · (!) new finding from 2026-07-19 review

---

## DONE (committed, verified)

- [+] Phase 0: scope ALL task operations to their owner
  (user_id from session, never from POST; other user's task → 404;
  two-account test passed). Commit c65ea94.
- [+] CSRF protection on every state-changing endpoint, one token,
  session-stored, checked centrally in EntryPoint. Real token in
  layout meta tag; AJAX sends it. Commit b8df297.
- [+] Column whitelist enforced in DatabaseTable insert/update/find via
  one private helper assertColumnsAllowed(); strict in_array; guard
  clauses first. Blocks column-name SQL injection AND mass assignment
  (users.user_role ENUM includes 'admin' — this was a REAL target).
  Tested positive + negative + throwaway script for unreachable
  update(). Commit 8d4f387.

---

## Phase 1 — SECURITY CORE (current; blockers: no production before these)

- [ ] Session hardening:
  - [ ] session_regenerate_id(true) on login (true = delete old session
        file; without it the pre-login session id stays valid →
        session fixation)
  - [ ] Full logout: session_unset + destroy AND expire the cookie
        (setcookie with past date — destroy() alone leaves the cookie
        in the browser)
  - [+] Cookie params: HttpOnly, SameSite=Lax (Secure flag waits for
        HTTPS in Phase 6)
- [ ] Login brute-force protection (minimum: per-account attempt
      counter + delay; store attempts + last_attempt_at)
- [ ] Authentication must honor is_active flag (column exists in schema,
      is ignored in code); update last_login on successful login
- [ ] AJAX completion endpoint: 403 CSRF failure returns plain text,
      but app.js calls res.json() → throws. Dispatcher should detect
      AJAX (Accept / X-Requested-With) and return JSON error

## Phase 2 — CORRECTNESS DEBT (known bugs, small, one sweep, one commit each)

Broken flows:
- [ ] (!) Register success page unreachable: registerSubmit redirects to
      /users/registersuccess but checkLogin allowedPages does not include
      it → guest gets bounced to /login/login and never sees the page.
      Decide: add to allowedPages, or auto-login after register, or
      redirect to login with a flash message
- [ ] (!) Schema vs validation length mismatch:
      tasks.task_title VARCHAR(50) but TaskValidation allows 100;
      task_description VARCHAR(255) but validation allows 1000.
      In strict mode a 51-char title = SQL error = 500.
      Pick ONE source of truth and align both sides (migration or
      validation change — document the decision)
- [ ] Tasks::deleteSubmit error path returns errors at TOP level of the
      array, but EntryPoint reads $page['variables'] → template gets no
      $tasks/$totalTasks → broken page. Same shape as every other action
- [ ] Login::loginSubmit reads $rawData['identity'] after failed
      validation without ?? '' → undefined-key warning on malformed POST

Small fixes:
- [ ] http_response_code(200) before Location redirects (insertEditSubmit,
      deleteSubmit) — meaningless; the redirect is 302. Remove
- [ ] Inconsistent error shape: [['Invalid credentials']] vs getErrors()
      structure vs Users errors-as-string ('Error occured invalid input').
      One shape everywhere: ['field' => ['message', ...]]
- [ ] Register form repopulation reads wrong keys (userName/fullName
      vs user_name/full_name)
- [ ] "Password confrimation" label typo
- [ ] Dead link: hero "Start your list" → /users/registrationform (renamed)
- [ ] Dead link: "Forgot password?" → /login/forgot (404s; hide until Phase 4)
- [ ] app.js: checkbox toggles class BEFORE server confirms — if request
      fails, UI shows wrong state. Check res.ok, revert the toggle on
      failure
- [ ] Global catch (\Throwable) safety net in EntryPoint — TypeError,
      UnhandledMatchError, InvalidArgumentException are NOT caught by
      current PDOException/RuntimeException catches
- [ ] LoginValidation: unused `use DatabaseTable` import, empty
      constructor — remove

## Phase 3 — ARCHITECTURE CLEANUP

- [ ] Move EntryPoint.php + Website.php out of src/Model (routing, not
      model); update namespaces + references (index.php, TaskWebsite).
      Own commit
- [ ] Merge DatabaseTable vs TasksTable: one gateway per table, controller
      gets ONE dependency. During the merge implement the choke point
      agreed on 2026-07-19: private buildColumnList() that validates
      INSIDE query construction, so no method can build SQL while
      forgetting the whitelist (today it is a call-the-helper convention;
      a future method could forget)
- [ ] Route params: taskForm(int $taskId) etc. — /tasks/taskform/abc must
      404, not TypeError. Validate route params as strings before typed
      calls (?int + ctype_digit check in dispatcher or controller)
- [ ] RegisterValidation: dns_get_record() on email domain blocks the
      request on slow DNS and fails with no network. Decide: drop it
      (FILTER_VALIDATE_EMAIL is enough for now) or make it non-blocking.
      Also: strlen vs mb_strlen inconsistency across validators — pick
      mb_strlen everywhere
- [ ] password_hash: use PASSWORD_DEFAULT instead of hard-coded
      PASSWORD_BCRYPT (bcrypt also silently truncates at 72 bytes —
      document or cap max length accordingly)
- [ ] Later (only when needed): narrow interface for Authentication
      (interface segregation); Response object instead of
      header/echo/exit inside controllers

## Phase 4 — FORGOT PASSWORD (first full feature end-to-end)

- [ ] password_resets table: user_id, token HASH (never plain),
      expires_at, used_at
- [ ] Request form: always answer "if this email exists, we sent a link"
      (do not reveal which emails are registered)
- [ ] Single-use, time-limited token; invalidate on use and on password
      change
- [ ] Reset form + validation reusing RegisterValidation password rules
- [ ] Email sending (dev: mailpit container / log; prod: SMTP)

## Phase 5 — QUALITY TOOLING (start EARLY, parallel-friendly)

Priority raised 2026-07-19: eight identifier typos in one session
($valyes, $columName, ...), all invisible to php -l, one of them made
the whitelist reject EVERY column. PHPStan level 0 catches all of them
before commit.

- [ ] PHPStan: phpstan.neon, start level 0-1, raise one level at a time,
      fix as you go; composer script "analyse". DatabaseTable first
- [ ] PHP-CS-Fixer with PSR-12 ruleset; composer script "fix"
- [ ] PHPUnit: start with validators (pure, no DB — TaskValidation,
      LoginValidation), then DatabaseTable/TasksTable against a test
      database. Unit tests are the only way to cover paths the browser
      cannot reach (DatabaseTable::update() lesson)
- [ ] Run analyse + tests before every commit (later: git pre-commit hook)

## Phase 6 — PRODUCTION OPERATIONS

- [ ] Prod docker profile: no source volume mount (COPY in image),
      opcache on, display_errors=Off + log_errors=On (dev php.ini has
      display_errors=On — correct for dev, forbidden for prod)
- [ ] HTTPS (reverse proxy: Caddy or certbot) — then add Secure flag to
      session cookie params
- [ ] MySQL backups: daily dump + restore test (a backup you never
      restored is not a backup)
- [ ] Secrets: prod .env never in git, different passwords than dev;
      verify .gitignore covers .env
- [ ] Log rotation / habit of reading logs
- [+] OneDrive removed after NUL-byte corruption (twice). Still verify:
      repo lives on Desktop — move to C:\dev to be safe

## Phase 7 — PRODUCT POLISH (after everything above)

- [ ] Pagination or LIMIT on task list (findAllTasks is unbounded)
- [ ] Soft delete decision: schema has deleted_at but delete() hard-deletes.
      Either implement soft delete (filter deleted_at IS NULL everywhere)
      or drop the column — half-done is worse than either
- [ ] Show completed state on home page high-priority list
- [ ] Flash messages ("task saved") instead of silent redirects
- [ ] Empty states, mobile pass, accessibility pass (keyboard on
      checkboxes — div role=button needs Enter/Space handling)

---

Working rules (constant, every phase):
1. One logical change per commit; read `git diff HEAD` before committing.
2. Commit messages: Georgian what/why first → own English translation →
   mentor fixes grammar only.
3. Every change: browser test (positive AND negative/attack path) + check
   logs before calling it done.
4. Every lesson: one line in NOTES.md in your own words.
5. Proofread code once as if it were someone else's before every commit
   (typo rule).
