# Lessons — Todo Webapp

Rule: after each lesson, Tornike writes one line under "My words".
Short and simple is fine. If you cannot write the line, the lesson is not finished.

---

## Lesson 1 — The controller contract
- Every controller action has exactly 2 legal outcomes:
  1) return an array (template, pageTitle, variables) — run() renders it;
  2) send its own response and call exit() — control never returns to run().
- Anything else (returning null, falling through) is a bug.
- The guard in run() checks `is_array($page)` and throws if the contract is broken.
- A method that ends without `return` returns null. An `exit()` with no output sends an empty 200.

My words:

---

## Lesson 2 — Exceptions: throw site vs catch site
- The THROW site knows the facts (which controller, which action, what it returned).
  Put the facts in the exception message: `new \RuntimeException(...)`.
- The exception OBJECT carries the message up the stack.
- The CATCH block matches by TYPE only (instanceof, top to bottom, first match wins).
  It does not know or care who threw it.
- Order: subclass BEFORE superclass. PDOException extends RuntimeException,
  so PDOException must be caught first.
- The catch stays generic: tag + $e->getMessage() + file + line.

My words:

---

## Lesson 3 — HTTP status codes do not lie
- 200 = success. Never send 200 with an error page.
- 400 = client sent bad data. 404 = the thing does not exist.
- 500 = the application has a bug. 503 = a dependency is down (database), retry later.
- header('Location: ...') with no explicit code sends 302 automatically —
  you cannot grep for a default.

My words:

---

## Lesson 4 — Logs are searched, not read
- Every log line: CATEGORY TAG (stable, you control it) + message (the facts of this case).
- Tag answers "which kind of emergency?" (DatabaseError vs RuntimeError).
- Message answers "what exactly happened this time?"
- A log line is good if you can fix the bug without reproducing it.

My words:

---

## Lesson 5 — rowCount() and the three outcomes of UPDATE
- MySQL rowCount() on UPDATE counts CHANGED rows, not MATCHED rows.
  Same value written again = 0.
- So rowCount 0 means two different things: not found, OR already in that state.
- Design chosen: UPDATE first; only if rowCount is 0, run one existence query
  to decide between Unchanged and NotFound. Extra query only on the rare path.
- Check-then-act has a race window (TOCTOU). Know when a race is tolerable.

My words:

---

## Lesson 6 — Enums
- `case Changed;` is an enum case — a value of a closed type. `const CHANGED = 1;`
  is just an int. The whole point is the closed set + real return types.
- Pure enum = no values. Backed enum (`case High = 1`) only when the value
  crosses a boundary (database, JSON, URL).
- `match` on an enum with NO default = PHP/PHPStan force you to handle every case.
  `default` on an enum match is usually a mistake.
- Names: enum = noun (a fact-holder, e.g. UpdateResult), cases = facts (Changed,
  NotFound), not judgments (Success) and not actions (SetTaskCompleted).
- Facts live in the model; judgments (is Unchanged a success?) live in the controller.

My words:

---

## Lesson 7 — git diff is self-review
- git diff HEAD = everything changed since last commit. Read it before every
  commit and before showing code to anyone.
- One question per line: "did I mean to do this?"
- Would have caught: the deleted catch block, the stealth exit() on line 53.
- One logical change per commit. Move-files refactor = its own commit.

My words:

---

## Lesson 8 — Tests: arrange, act, assert
- Arrange: set up the state AND VERIFY it (is id 999 really dead?).
- Act: fire the request.
- Assert: all signals must agree — response body + status code + log line.
- A status code summarizes; the body testifies. An unexplained log line
  (the mystery 404) is a problem you have not met yet.

My words:
