"""# Dockerized PHP + Nginx + MySQL — Deep Learning Notes (for me)

> This is **my** learning document (not “project documentation for others”).  
> Goal: understand the **logic** behind a Dockerized PHP backend and how each line in the configs affects **security, control, and behavior**.

---

## A) The one-sentence mental model

**docker-compose.yml** wires containers together, **Nginx** handles HTTP, **PHP-FPM** executes PHP, **MySQL** stores data, and **only `public/` is exposed**.

---

## B) What I’m building (and why)

### B1) What I want
- A **portable** local environment: clone repo anywhere → run one command → same setup.
- Separation of concerns: web server vs PHP runtime vs database.
- A layout that matches common production patterns.

### B2) What I’m *not* doing yet
- Not deploying to production.
- Not adding TLS, reverse proxy, caching layers, CI/CD, etc.
- Not adding auth, migrations, queues, etc.

---

## C) Folder & file structure (what exists and why)



task_manager/
docker-compose.yml
PHP.Dockerfile
.env.example
.gitignore

public/
index.php

src/
db.php

templates/
layout.html.php

docker/
nginx/
default.conf
php/
php.ini
mysql/
init/
001_schema.sql

Always show details

### C1) Why `public/` exists
- `public/` is the **HTTP boundary**.
- Anything outside `public/` should **not be directly reachable** from the browser.

### C2) Why `src/` exists
- Application logic lives here: DB connection, services, controllers, etc.
- Keeps code organized and prevents accidental public access.

### C3) Why `templates/` exists
- Rendering layer (HTML templates).
- Keeps HTML separate from logic.

### C4) Why `docker/` exists
- All infrastructure config in one place (nginx, php ini, db init).
- Makes the repo understandable and portable.

---

## D) docker-compose.yml — “the orchestrator”

**Purpose:** define services (containers), networking, volumes, ports, environment variables.

### D1) Full file

```yaml
services:
  nginx:
    image: nginx:alpine
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html:delegated
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
    depends_on:
      - php

  php:
    build:
      context: .
      dockerfile: PHP.Dockerfile
    volumes:
      - ./:/var/www/html:delegated
      - ./docker/php/php.ini:/usr/local/etc/php/conf.d/99-custom.ini:ro

  mysql:
    image: mysql:8.0
    command: --default-authentication-plugin=mysql_native_password
    environment:
      MYSQL_DATABASE: ${MYSQL_DATABASE}
      MYSQL_USER: ${MYSQL_USER}
      MYSQL_PASSWORD: ${MYSQL_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
    ports:
      - "3307:3306"
    volumes:
      - dbdata:/var/lib/mysql
      - ./docker/mysql/init:/docker-entrypoint-initdb.d:ro

volumes:
  dbdata:

D2) Line-by-line explanation
services:

Declares multiple containers that will run together as one application stack.

D3) Nginx service block
nginx:

This is the service name.

Inside Docker networking, other containers can reach it by this name (not by IP).

image: nginx:alpine

Uses an official Nginx image.

alpine is a minimal Linux base → small and fast for development.

ports: - "8080:80"

Port mapping format: "HOST:CONTAINER".

Host 8080 (your browser hits this) → container port 80 (Nginx listens here).

Why not 80:80? Because 8080 avoids conflicts with other services.

volumes: - ./:/var/www/html:delegated

Mount your project folder (./) into container at /var/www/html.

Result: editing files on your machine instantly affects container.

:delegated is a performance hint (especially on Docker Desktop). It’s optional.

volumes: - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro

Mount a custom Nginx config file into the container.

:ro means read-only → container can’t modify it (safety + reproducibility).

depends_on: - php

Starts PHP container before Nginx.

Note: it does not guarantee PHP is “ready”, only “started”. For dev, fine.

D4) PHP service block
php:

Service name php becomes the internal DNS hostname.

Nginx uses it in fastcgi_pass php:9000;.

build: context: .

Build the PHP image using files in the current directory.

It allows installing PHP extensions or custom configs.

dockerfile: PHP.Dockerfile

Use PHP.Dockerfile instead of default Dockerfile name (explicit clarity).

volumes: - ./:/var/www/html:delegated

Same codebase visible to PHP runtime.

Critical: both Nginx and PHP must agree on the same file paths.

volumes: - ./docker/php/php.ini:/usr/local/etc/php/conf.d/99-custom.ini:ro

Inject custom PHP settings (errors, timezone, etc.)

Use /conf.d/ so it auto-loads.

99- prefix ensures it loads after defaults.

D5) MySQL service block
mysql:

Service name. PHP connects using host mysql (not localhost).

image: mysql:8.0

Uses MySQL server version 8.

command: --default-authentication-plugin=mysql_native_password

Compatibility setting.

Some clients/tools may have issues with newer default auth; this avoids that.

environment: ...

Defines the initial DB/user/password.

Values come from .env file via ${...} placeholders.

ports: - "3307:3306"

Exposes MySQL to your host machine at port 3307.

Why 3307? Avoid conflicts if you have local MySQL on 3306.

Tools like Workbench connect to 127.0.0.1:3307.

volumes: - dbdata:/var/lib/mysql

Persists database data.

Without this, DB resets every container rebuild (you lose tables/data).

volumes: - ./docker/mysql/init:/docker-entrypoint-initdb.d:ro

Special MySQL entrypoint feature:

On first startup (empty data dir), runs .sql files here.

:ro read-only keeps scripts immutable.

D6) volumes: dbdata:

Declares a named volume called dbdata.

Docker manages it (stored outside the repo).

This is good: DB state is separated from code.

E) PHP.Dockerfile — define the PHP runtime

Purpose: specify PHP version and install required extensions.

E1) Full file
Always show details
FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash \
  && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

E2) Line-by-line
FROM php:8.3-fpm-alpine

Base image includes PHP + PHP-FPM.

PHP-FPM listens on port 9000 inside the container (FastCGI protocol).

Nginx will hand PHP requests to FPM.

RUN apk add --no-cache bash

Installs bash (optional).

apk is Alpine’s package manager.

--no-cache keeps image smaller.

docker-php-ext-install pdo pdo_mysql

Adds PDO and MySQL driver so PHP can connect to MySQL with PDO.

Without pdo_mysql, new PDO("mysql:...") will fail.

WORKDIR /var/www/html

Default working directory inside container.

Matches the volume mount.

F) docker/php/php.ini — PHP behavior (dev settings)
F1) Full file
Always show details
display_errors=On
display_startup_errors=On
error_reporting=E_ALL
date.timezone=Asia/Tbilisi

F2) Why these settings matter

display_errors=On: show errors in browser (development only).

display_startup_errors=On: show startup errors.

error_reporting=E_ALL: report all errors/notices.

date.timezone=Asia/Tbilisi: prevents timezone warnings and makes date() consistent.

Security note: In production, display_errors should be Off (do not leak details).

G) docker/nginx/default.conf — HTTP routing + PHP handoff
G1) Full file
Always show details
server {
    listen 80;
    server_name localhost;

    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri /index.php?$query_string;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass php:9000;
        fastcgi_index index.php;

        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

G2) Line-by-line
server { ... }

Defines one virtual host.

listen 80;

Nginx listens on port 80 inside its container.

server_name localhost;

Hostname match (mostly irrelevant for local dev unless multiple vhosts).

root /var/www/html/public;

CRITICAL SECURITY LINE

Browser-visible root is only public/.

If root was /var/www/html, users could attempt to access /templates/... or other internal files.

index index.php index.html;

Default files when requesting /.

G3) Clean URLs / front controller
location / { ... }

Handles all requests.

try_files $uri /index.php?$query_string;

If the requested file exists (like /style.css) → serve it.

Otherwise route to index.php while preserving query string.

This enables “pretty” URLs and MVC routing patterns.

G4) PHP handling
location ~ \.php$ { ... }

Any URL ending with .php is handled as PHP script.

try_files $uri =404;

If the PHP file doesn’t exist → return 404.

Prevents weird fallback behavior.

fastcgi_pass php:9000;

Sends PHP execution to the PHP container at port 9000.

php is the Docker service name (internal DNS).

fastcgi_index index.php;

Default script for directory requests (FastCGI context).

include fastcgi_params;

Loads standard FastCGI parameters (headers/environment).

fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

Tells PHP-FPM the real filesystem path to the script.

$document_root is the root directive path.

$fastcgi_script_name is the requested script name.

G5) Blocking hidden files
location ~ /\.(?!well-known).* { deny all; }

Denies access to files like .env, .git, .htaccess, etc.

Security hardening.

.well-known is allowed because it’s used for certain protocols (not crucial in dev but safe).

H) public/index.php — “is my stack running?”
H1) Minimal file
Always show details
<?php
echo "Server is running ✅";


Purpose:

Confirm Nginx + PHP are wired correctly.

If this prints, then Nginx → PHP handoff works.

I) src/db.php — database connection (PDO)
I1) File content
Always show details
<?php

$dsn = 'mysql:host=mysql;dbname=ijdb;charset=utf8mb4';
$user = getenv('MYSQL_USER') ?: 'tornike';
$pass = getenv('MYSQL_PASSWORD') ?: 'secret';

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

I2) Line-by-line
$dsn = 'mysql:host=mysql;dbname=ijdb;charset=utf8mb4';

host=mysql means connect to the mysql container, not localhost.

dbname=ijdb must match MYSQL_DATABASE.

charset=utf8mb4 supports full Unicode (including emojis).

getenv('MYSQL_USER')

Reads environment variables from container environment.

In our compose config, we set MYSQL_USER / MYSQL_PASSWORD for mysql container.

For php container, if not set, this fallback ensures it still works.

Better practice later: also pass env vars to PHP container.

PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION

Throws exceptions on SQL errors.

Without this, PDO may fail silently and debugging becomes painful.

PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC

Fetch results as associative arrays by default ($row['title']).

Avoids numeric+assoc mixed arrays.

J) templates/layout.html.php — basic layout template
Always show details
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= $title ?? 'Task Manager' ?></title>
</head>
<body>
  <?= $output ?? '' ?>
</body>
</html>

J1) Notes

<?= ... ?> prints the expression (short echo).

$title ?? 'Task Manager' means: use $title if set, otherwise fallback.

$output should contain page content from controllers.

Security note:

For real output, HTML should be escaped unless it is trusted content.

K) .env, .env.example, and .gitignore — security + portability
K1) .env (LOCAL ONLY)

Contains secrets (passwords).

Must never be committed.

K2) .env.example (COMMITTED)
Always show details
MYSQL_DATABASE=ijdb
MYSQL_USER=tornike
MYSQL_PASSWORD=secret
MYSQL_ROOT_PASSWORD=rootsecret


A template that tells me (or future me) what variables are required.

When I clone the repo on a new machine:

copy .env.example → .env

set my local secrets

K3) .gitignore (COMMITTED)
Always show details
.env
/vendor


.env stays out of Git.

vendor stays out if using Composer (dependencies are installable).

L) docker/mysql/init/001_schema.sql — reproducible schema
Always show details
CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

L1) Why this exists

Fresh clone → docker compose up → tables created automatically.

I don’t manually create schema each time.

L2) Important behavior

MySQL init scripts run only on first initialization, when /var/lib/mysql is empty.

If I already have data in dbdata, changes to init SQL won’t re-run automatically.

To reset:

Always show details
docker compose down -v
docker compose up -d --build

M) Security & control checklist (what this setup prevents)
M1) Prevents serving internal PHP files

Because Nginx root is /public.

M2) Prevents leaking hidden files

Because .env/dotfiles are blocked by Nginx rule.

M3) Prevents “works on my machine” drift

Docker pins versions and dependencies.

M4) Prevents accidental credential leaks

.env not in Git because of .gitignore.

.env.example provides structure without secrets.

N) Debug workflow (what to do when it breaks)
N1) Check containers are up
Always show details
docker compose ps

N2) Check Nginx logs
Always show details
docker compose logs nginx

N3) Check PHP logs
Always show details
docker compose logs php

N4) Check MySQL logs
Always show details
docker compose logs mysql

N5) Verify file mounts inside container
Always show details
docker exec -it <nginx_container> ls -la /var/www/html

O) The brutally honest advice to myself

Learning this is good. But I must avoid this trap:

“I’m learning infrastructure” = excuse to delay building the CRUD app.

Rule:

Spend a fixed time (e.g., 1–2 hours) getting Docker stable.

Then immediately build the Task Manager CRUD inside this structure.

If I don’t ship the CRUD, the Docker knowledge is just theory.

P) Next step: integrate with Task Manager

Once the stack is running:

Replace public/index.php with a real controller.

Use src/db.php to query tasks table.

Render output with templates/.

That’s the real learning.

End of learning notes.
"""

path = Path("/mnt/data/Docker_PHP_Nginx_MySQL_Learning_Notes.md")
path.write_text(content, encoding="utf-8")
str(path)

Always show details
  Cell In[4], line 1
    ```python
    ^
SyntaxError: invalid syntax


Analysis errored
Always show details
```python
from pathlib import Path

content = """# Dockerized PHP + Nginx + MySQL — Deep Learning Notes (for me)

> This is **my** learning document (not “project documentation for others”).  
> Goal: understand the **logic** behind a Dockerized PHP backend and how each line in the configs affects **security, control, and behavior**.

---

## A) The one-sentence mental model

**docker-compose.yml** wires containers together, **Nginx** handles HTTP, **PHP-FPM** executes PHP, **MySQL** stores data, and **only `public/` is exposed**.

---

## B) What I’m building (and why)

### B1) What I want
- A **portable** local environment: clone repo anywhere → run one command → same setup.
- Separation of concerns: web server vs PHP runtime vs database.
- A layout that matches common production patterns.

### B2) What I’m *not* doing yet
- Not deploying to production.
- Not adding TLS, reverse proxy, caching layers, CI/CD, etc.
- Not adding auth, migrations, queues, etc.

---

## C) Folder & file structure (what exists and why)



task_manager/
docker-compose.yml
PHP.Dockerfile
.env.example
.gitignore

public/
index.php

src/
db.php

templates/
layout.html.php

docker/
nginx/
default.conf
php/
php.ini
mysql/
init/
001_schema.sql

Always show details

### C1) Why `public/` exists
- `public/` is the **HTTP boundary**.
- Anything outside `public/` should **not be directly reachable** from the browser.

### C2) Why `src/` exists
- Application logic lives here: DB connection, services, controllers, etc.
- Keeps code organized and prevents accidental public access.

### C3) Why `templates/` exists
- Rendering layer (HTML templates).
- Keeps HTML separate from logic.

### C4) Why `docker/` exists
- All infrastructure config in one place (nginx, php ini, db init).
- Makes the repo understandable and portable.

---

## D) docker-compose.yml — “the orchestrator”

**Purpose:** define services (containers), networking, volumes, ports, environment variables.

### D1) Full file

```yaml
services:
  nginx:
    image: nginx:alpine
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html:delegated
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
    depends_on:
      - php

  php:
    build:
      context: .
      dockerfile: PHP.Dockerfile
    volumes:
      - ./:/var/www/html:delegated
      - ./docker/php/php.ini:/usr/local/etc/php/conf.d/99-custom.ini:ro

  mysql:
    image: mysql:8.0
    command: --default-authentication-plugin=mysql_native_password
    environment:
      MYSQL_DATABASE: ${MYSQL_DATABASE}
      MYSQL_USER: ${MYSQL_USER}
      MYSQL_PASSWORD: ${MYSQL_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
    ports:
      - "3307:3306"
    volumes:
      - dbdata:/var/lib/mysql
      - ./docker/mysql/init:/docker-entrypoint-initdb.d:ro

volumes:
  dbdata:

D2) Line-by-line explanation
services:

Declares multiple containers that will run together as one application stack.

D3) Nginx service block
nginx:

This is the service name.

Inside Docker networking, other containers can reach it by this name (not by IP).

image: nginx:alpine

Uses an official Nginx image.

alpine is a minimal Linux base → small and fast for development.

ports: - "8080:80"

Port mapping format: "HOST:CONTAINER".

Host 8080 (your browser hits this) → container port 80 (Nginx listens here).

Why not 80:80? Because 8080 avoids conflicts with other services.

volumes: - ./:/var/www/html:delegated

Mount your project folder (./) into container at /var/www/html.

Result: editing files on your machine instantly affects container.

:delegated is a performance hint (especially on Docker Desktop). It’s optional.

volumes: - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro

Mount a custom Nginx config file into the container.

:ro means read-only → container can’t modify it (safety + reproducibility).

depends_on: - php

Starts PHP container before Nginx.

Note: it does not guarantee PHP is “ready”, only “started”. For dev, fine.

D4) PHP service block
php:

Service name php becomes the internal DNS hostname.

Nginx uses it in fastcgi_pass php:9000;.

build: context: .

Build the PHP image using files in the current directory.

It allows installing PHP extensions or custom configs.

dockerfile: PHP.Dockerfile

Use PHP.Dockerfile instead of default Dockerfile name (explicit clarity).

volumes: - ./:/var/www/html:delegated

Same codebase visible to PHP runtime.

Critical: both Nginx and PHP must agree on the same file paths.

volumes: - ./docker/php/php.ini:/usr/local/etc/php/conf.d/99-custom.ini:ro

Inject custom PHP settings (errors, timezone, etc.)

Use /conf.d/ so it auto-loads.

99- prefix ensures it loads after defaults.

D5) MySQL service block
mysql:

Service name. PHP connects using host mysql (not localhost).

image: mysql:8.0

Uses MySQL server version 8.

command: --default-authentication-plugin=mysql_native_password

Compatibility setting.

Some clients/tools may have issues with newer default auth; this avoids that.

environment: ...

Defines the initial DB/user/password.

Values come from .env file via ${...} placeholders.

ports: - "3307:3306"

Exposes MySQL to your host machine at port 3307.

Why 3307? Avoid conflicts if you have local MySQL on 3306.

Tools like Workbench connect to 127.0.0.1:3307.

volumes: - dbdata:/var/lib/mysql

Persists database data.

Without this, DB resets every container rebuild (you lose tables/data).

volumes: - ./docker/mysql/init:/docker-entrypoint-initdb.d:ro

Special MySQL entrypoint feature:

On first startup (empty data dir), runs .sql files here.

:ro read-only keeps scripts immutable.

D6) volumes: dbdata:

Declares a named volume called dbdata.

Docker manages it (stored outside the repo).

This is good: DB state is separated from code.

E) PHP.Dockerfile — define the PHP runtime

Purpose: specify PHP version and install required extensions.

E1) Full file
Always show details
FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash \\
  && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

E2) Line-by-line
FROM php:8.3-fpm-alpine

Base image includes PHP + PHP-FPM.

PHP-FPM listens on port 9000 inside the container (FastCGI protocol).

Nginx will hand PHP requests to FPM.

RUN apk add --no-cache bash

Installs bash (optional).

apk is Alpine’s package manager.

--no-cache keeps image smaller.

docker-php-ext-install pdo pdo_mysql

Adds PDO and MySQL driver so PHP can connect to MySQL with PDO.

Without pdo_mysql, new PDO("mysql:...") will fail.

WORKDIR /var/www/html

Default working directory inside container.

Matches the volume mount.

F) docker/php/php.ini — PHP behavior (dev settings)
F1) Full file
Always show details
display_errors=On
display_startup_errors=On
error_reporting=E_ALL
date.timezone=Asia/Tbilisi

F2) Why these settings matter

display_errors=On: show errors in browser (development only).

display_startup_errors=On: show startup errors.

error_reporting=E_ALL: report all errors/notices.

date.timezone=Asia/Tbilisi: prevents timezone warnings and makes date() consistent.

Security note: In production, display_errors should be Off (do not leak details).

G) docker/nginx/default.conf — HTTP routing + PHP handoff
G1) Full file
Always show details
server {
    listen 80;
    server_name localhost;

    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri /index.php?$query_string;
    }

    location ~ \\.php$ {
        try_files $uri =404;
        fastcgi_pass php:9000;
        fastcgi_index index.php;

        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\\.(?!well-known).* {
        deny all;
    }
}

G2) Line-by-line
server { ... }

Defines one virtual host.

listen 80;

Nginx listens on port 80 inside its container.

server_name localhost;

Hostname match (mostly irrelevant for local dev unless multiple vhosts).

root /var/www/html/public;

CRITICAL SECURITY LINE

Browser-visible root is only public/.

If root was /var/www/html, users could attempt to access /templates/... or other internal files.

index index.php index.html;

Default files when requesting /.

G3) Clean URLs / front controller
location / { ... }

Handles all requests.

try_files $uri /index.php?$query_string;

If the requested file exists (like /style.css) → serve it.

Otherwise route to index.php while preserving query string.

This enables “pretty” URLs and MVC routing patterns.

G4) PHP handling
location ~ \\.php$ { ... }

Any URL ending with .php is handled as PHP script.

try_files $uri =404;

If the PHP file doesn’t exist → return 404.

Prevents weird fallback behavior.

fastcgi_pass php:9000;

Sends PHP execution to the PHP container at port 9000.

php is the Docker service name (internal DNS).

fastcgi_index index.php;

Default script for directory requests (FastCGI context).

include fastcgi_params;

Loads standard FastCGI parameters (headers/environment).

fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

Tells PHP-FPM the real filesystem path to the script.

$document_root is the root directive path.

$fastcgi_script_name is the requested script name.

G5) Blocking hidden files
location ~ /\\.(?!well-known).* { deny all; }

Denies access to files like .env, .git, .htaccess, etc.

Security hardening.

.well-known is allowed because it’s used for certain protocols (not crucial in dev but safe).

H) public/index.php — “is my stack running?”
H1) Minimal file
Always show details
<?php
echo "Server is running ✅";


Purpose:

Confirm Nginx + PHP are wired correctly.

If this prints, then Nginx → PHP handoff works.

I) src/db.php — database connection (PDO)
I1) File content
Always show details
<?php

$dsn = 'mysql:host=mysql;dbname=ijdb;charset=utf8mb4';
$user = getenv('MYSQL_USER') ?: 'tornike';
$pass = getenv('MYSQL_PASSWORD') ?: 'secret';

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

I2) Line-by-line
$dsn = 'mysql:host=mysql;dbname=ijdb;charset=utf8mb4';

host=mysql means connect to the mysql container, not localhost.

dbname=ijdb must match MYSQL_DATABASE.

charset=utf8mb4 supports full Unicode (including emojis).

getenv('MYSQL_USER')

Reads environment variables.

We use fallbacks so the file still works if env vars aren’t passed into PHP container yet.

Best practice later: pass env vars to PHP container too.

PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION

Throws exceptions on SQL errors (better debugging, safer failures).

PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC

Fetch associative arrays by default: $row['title'].

J) templates/layout.html.php — basic layout template
Always show details
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= $title ?? 'Task Manager' ?></title>
</head>
<body>
  <?= $output ?? '' ?>
</body>
</html>

J1) Notes

<?= ... ?> prints the expression.

$title ?? 'Task Manager' fallback.

$output holds the page content.

Security note:

Output should be escaped when printing user-controlled text.

K) .env, .env.example, and .gitignore — security + portability
K1) .env (LOCAL ONLY)

Contains secrets.

Must never be committed.

K2) .env.example (COMMITTED)
Always show details
MYSQL_DATABASE=ijdb
MYSQL_USER=tornike
MYSQL_PASSWORD=secret
MYSQL_ROOT_PASSWORD=rootsecret


A template listing required variables.

K3) .gitignore (COMMITTED)
Always show details
.env
/vendor


Prevent secret leaks (.env).

Prevent committing dependency folders (if using Composer).

L) docker/mysql/init/001_schema.sql — reproducible schema
Always show details
CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

L1) Why this exists

Fresh clone → docker compose up → tables created automatically.

L2) Important behavior

Runs only when DB volume is empty.

To rerun init scripts, wipe volume:

Always show details
docker compose down -v
docker compose up -d --build

M) Security & control checklist (what this setup prevents)

Prevents direct access to internal code: root points to /public.

Blocks dotfiles like .env and .git via Nginx rule.

Pins versions of services (Nginx/PHP/MySQL) via Docker.

Avoids credential leaks with .env ignored by git.

Keeps DB persistent but separate from code using a named volume.

N) Debug workflow (when it breaks)
Always show details
docker compose ps
docker compose logs nginx
docker compose logs php
docker compose logs mysql


Check file mounts:

Always show details
docker exec -it <nginx_container> ls -la /var/www/html

O) Brutally honest rule for myself (avoid procrastination)

Learning infrastructure is useful — but it’s also an easy excuse to delay building.

Timebox Docker setup.
Once it prints Server is running ✅, immediately build the Task Manager CRUD.

P) Next step after the stack runs

Replace public/index.php with a controller that lists tasks.

Query DB using src/db.php.

Render HTML with templates/.

That’s where real learning happens.

End of notes.
"""

path = Path("/mnt/data/Docker_PHP_Nginx_MySQL_Learning_Notes.md")
path.write_text(content, encoding="utf-8")

str(path)

Always show details
  Cell In[5], line 1
    ```python
    ^
SyntaxError: invalid syntax


Analyzed
Always show details
from pathlib import Path

content = """# Dockerized PHP + Nginx + MySQL — Deep Learning Notes (for me)

> This is **my** learning document (not “project documentation for others”).  
> Goal: understand the **logic** behind a Dockerized PHP backend and how each line in the configs affects **security, control, and behavior**.

---

## A) The one-sentence mental model

**docker-compose.yml** wires containers together, **Nginx** handles HTTP, **PHP-FPM** executes PHP, **MySQL** stores data, and **only `public/` is exposed**.

---

## B) What I’m building (and why)

### B1) What I want
- A **portable** local environment: clone repo anywhere → run one command → same setup.
- Separation of concerns: web server vs PHP runtime vs database.
- A layout that matches common production patterns.

### B2) What I’m *not* doing yet
- Not deploying to production.
- Not adding TLS, reverse proxy, caching layers, CI/CD, etc.
- Not adding auth, migrations, queues, etc.

---

## C) Folder & file structure (what exists and why)



task_manager/
docker-compose.yml
PHP.Dockerfile
.env.example
.gitignore

public/
index.php

src/
db.php

templates/
layout.html.php

docker/
nginx/
default.conf
php/
php.ini
mysql/
init/
001_schema.sql

Always show details

### C1) Why `public/` exists
- `public/` is the **HTTP boundary**.
- Anything outside `public/` should **not be directly reachable** from the browser.

### C2) Why `src/` exists
- Application logic lives here: DB connection, services, controllers, etc.
- Keeps code organized and prevents accidental public access.

### C3) Why `templates/` exists
- Rendering layer (HTML templates).
- Keeps HTML separate from logic.

### C4) Why `docker/` exists
- All infrastructure config in one place (nginx, php ini, db init).
- Makes the repo understandable and portable.

---

## D) docker-compose.yml — “the orchestrator”

**Purpose:** define services (containers), networking, volumes, ports, environment variables.

### D1) Full file

```yaml
services:
  nginx:
    image: nginx:alpine
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html:delegated
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
    depends_on:
      - php

  php:
    build:
      context: .
      dockerfile: PHP.Dockerfile
    volumes:
      - ./:/var/www/html:delegated
      - ./docker/php/php.ini:/usr/local/etc/php/conf.d/99-custom.ini:ro

  mysql:
    image: mysql:8.0
    command: --default-authentication-plugin=mysql_native_password
    environment:
      MYSQL_DATABASE: ${MYSQL_DATABASE}
      MYSQL_USER: ${MYSQL_USER}
      MYSQL_PASSWORD: ${MYSQL_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
    ports:
      - "3307:3306"
    volumes:
      - dbdata:/var/lib/mysql
      - ./docker/mysql/init:/docker-entrypoint-initdb.d:ro

volumes:
  dbdata:

D2) Line-by-line explanation
services:

Declares multiple containers that will run together as one application stack.

D3) Nginx service block
nginx:

This is the service name.

Inside Docker networking, other containers can reach it by this name (not by IP).

image: nginx:alpine

Uses an official Nginx image.

alpine is a minimal Linux base → small and fast for development.

ports: - "8080:80"

Port mapping format: "HOST:CONTAINER".

Host 8080 (your browser hits this) → container port 80 (Nginx listens here).

Why not 80:80? Because 8080 avoids conflicts with other services.

volumes: - ./:/var/www/html:delegated

Mount your project folder (./) into container at /var/www/html.

Result: editing files on your machine instantly affects container.

:delegated is a performance hint (especially on Docker Desktop). It’s optional.

volumes: - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro

Mount a custom Nginx config file into the container.

:ro means read-only → container can’t modify it (safety + reproducibility).

depends_on: - php

Starts PHP container before Nginx.

Note: it does not guarantee PHP is “ready”, only “started”. For dev, fine.

D4) PHP service block
php:

Service name php becomes the internal DNS hostname.

Nginx uses it in fastcgi_pass php:9000;.

build: context: .

Build the PHP image using files in the current directory.

It allows installing PHP extensions or custom configs.

dockerfile: PHP.Dockerfile

Use PHP.Dockerfile instead of default Dockerfile name (explicit clarity).

volumes: - ./:/var/www/html:delegated

Same codebase visible to PHP runtime.

Critical: both Nginx and PHP must agree on the same file paths.

volumes: - ./docker/php/php.ini:/usr/local/etc/php/conf.d/99-custom.ini:ro

Inject custom PHP settings (errors, timezone, etc.)

Use /conf.d/ so it auto-loads.

99- prefix ensures it loads after defaults.

D5) MySQL service block
mysql:

Service name. PHP connects using host mysql (not localhost).

image: mysql:8.0

Uses MySQL server version 8.

command: --default-authentication-plugin=mysql_native_password

Compatibility setting.

Some clients/tools may have issues with newer default auth; this avoids that.

environment: ...

Defines the initial DB/user/password.

Values come from .env file via ${...} placeholders.

ports: - "3307:3306"

Exposes MySQL to your host machine at port 3307.

Why 3307? Avoid conflicts if you have local MySQL on 3306.

Tools like Workbench connect to 127.0.0.1:3307.

volumes: - dbdata:/var/lib/mysql

Persists database data.

Without this, DB resets every container rebuild (you lose tables/data).

volumes: - ./docker/mysql/init:/docker-entrypoint-initdb.d:ro

Special MySQL entrypoint feature:

On first startup (empty data dir), runs .sql files here.

:ro read-only keeps scripts immutable.

D6) volumes: dbdata:

Declares a named volume called dbdata.

Docker manages it (stored outside the repo).

This is good: DB state is separated from code.

E) PHP.Dockerfile — define the PHP runtime

Purpose: specify PHP version and install required extensions.

E1) Full file
Always show details
FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash \\
  && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

E2) Line-by-line
FROM php:8.3-fpm-alpine

Base image includes PHP + PHP-FPM.

PHP-FPM listens on port 9000 inside the container (FastCGI protocol).

Nginx will hand PHP requests to FPM.

RUN apk add --no-cache bash

Installs bash (optional).

apk is Alpine’s package manager.

--no-cache keeps image smaller.

docker-php-ext-install pdo pdo_mysql

Adds PDO and MySQL driver so PHP can connect to MySQL with PDO.

Without pdo_mysql, new PDO("mysql:...") will fail.

WORKDIR /var/www/html

Default working directory inside container.

Matches the volume mount.

F) docker/php/php.ini — PHP behavior (dev settings)
F1) Full file
Always show details
display_errors=On
display_startup_errors=On
error_reporting=E_ALL
date.timezone=Asia/Tbilisi

F2) Why these settings matter

display_errors=On: show errors in browser (development only).

display_startup_errors=On: show startup errors.

error_reporting=E_ALL: report all errors/notices.

date.timezone=Asia/Tbilisi: prevents timezone warnings and makes date() consistent.

Security note: In production, display_errors should be Off (do not leak details).

G) docker/nginx/default.conf — HTTP routing + PHP handoff
G1) Full file
Always show details
server {
    listen 80;
    server_name localhost;

    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files $uri /index.php?$query_string;
    }

    location ~ \\.php$ {
        try_files $uri =404;
        fastcgi_pass php:9000;
        fastcgi_index index.php;

        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\\.(?!well-known).* {
        deny all;
    }
}

G2) Line-by-line
server { ... }

Defines one virtual host.

listen 80;

Nginx listens on port 80 inside its container.

server_name localhost;

Hostname match (mostly irrelevant for local dev unless multiple vhosts).

root /var/www/html/public;

CRITICAL SECURITY LINE

Browser-visible root is only public/.

If root was /var/www/html, users could attempt to access /templates/... or other internal files.

index index.php index.html;

Default files when requesting /.

G3) Clean URLs / front controller
location / { ... }

Handles all requests.

try_files $uri /index.php?$query_string;

If the requested file exists (like /style.css) → serve it.

Otherwise route to index.php while preserving query string.

This enables “pretty” URLs and MVC routing patterns.

G4) PHP handling
location ~ \\.php$ { ... }

Any URL ending with .php is handled as PHP script.

try_files $uri =404;

If the PHP file doesn’t exist → return 404.

Prevents weird fallback behavior.

fastcgi_pass php:9000;

Sends PHP execution to the PHP container at port 9000.

php is the Docker service name (internal DNS).

fastcgi_index index.php;

Default script for directory requests (FastCGI context).

include fastcgi_params;

Loads standard FastCGI parameters (headers/environment).

fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

Tells PHP-FPM the real filesystem path to the script.

$document_root is the root directive path.

$fastcgi_script_name is the requested script name.

G5) Blocking hidden files
location ~ /\\.(?!well-known).* { deny all; }

Denies access to files like .env, .git, .htaccess, etc.

Security hardening.

.well-known is allowed because it’s used for certain protocols (not crucial in dev but safe).

H) public/index.php — “is my stack running?”
H1) Minimal file
Always show details
<?php
echo "Server is running ✅";


Purpose:

Confirm Nginx + PHP are wired correctly.

If this prints, then Nginx → PHP handoff works.

I) src/db.php — database connection (PDO)
I1) File content
Always show details
<?php

$dsn = 'mysql:host=mysql;dbname=ijdb;charset=utf8mb4';
$user = getenv('MYSQL_USER') ?: 'tornike';
$pass = getenv('MYSQL_PASSWORD') ?: 'secret';

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

I2) Line-by-line
$dsn = 'mysql:host=mysql;dbname=ijdb;charset=utf8mb4';

host=mysql means connect to the mysql container, not localhost.

dbname=ijdb must match MYSQL_DATABASE.

charset=utf8mb4 supports full Unicode (including emojis).

getenv('MYSQL_USER')

Reads environment variables.

We use fallbacks so the file still works if env vars aren’t passed into PHP container yet.

Best practice later: pass env vars to PHP container too.

PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION

Throws exceptions on SQL errors (better debugging, safer failures).

PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC

Fetch associative arrays by default: $row['title'].

J) templates/layout.html.php — basic layout template
Always show details
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= $title ?? 'Task Manager' ?></title>
</head>
<body>
  <?= $output ?? '' ?>
</body>
</html>

J1) Notes

<?= ... ?> prints the expression.

$title ?? 'Task Manager' fallback.

$output holds the page content.

Security note:

Output should be escaped when printing user-controlled text.

K) .env, .env.example, and .gitignore — security + portability
K1) .env (LOCAL ONLY)

Contains secrets.

Must never be committed.

K2) .env.example (COMMITTED)
Always show details
MYSQL_DATABASE=ijdb
MYSQL_USER=tornike
MYSQL_PASSWORD=secret
MYSQL_ROOT_PASSWORD=rootsecret


A template listing required variables.

K3) .gitignore (COMMITTED)
Always show details
.env
/vendor


Prevent secret leaks (.env).

Prevent committing dependency folders (if using Composer).

L) docker/mysql/init/001_schema.sql — reproducible schema
Always show details
CREATE TABLE IF NOT EXISTS tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

L1) Why this exists

Fresh clone → docker compose up → tables created automatically.

L2) Important behavior

Runs only when DB volume is empty.

To rerun init scripts, wipe volume:

Always show details
docker compose down -v
docker compose up -d --build

M) Security & control checklist (what this setup prevents)

Prevents direct access to internal code: root points to /public.

Blocks dotfiles like .env and .git via Nginx rule.

Pins versions of services (Nginx/PHP/MySQL) via Docker.

Avoids credential leaks with .env ignored by git.

Keeps DB persistent but separate from code using a named volume.

N) Debug workflow (when it breaks)
Always show details
docker compose ps
docker compose logs nginx
docker compose logs php
docker compose logs mysql


Check file mounts:

Always show details
docker exec -it <nginx_container> ls -la /var/www/html

O) Brutally honest rule for myself (avoid procrastination)

Learning infrastructure is useful — but it’s also an easy excuse to delay building.

Timebox Docker setup.
Once it prints Server is running ✅, immediately build the Task Manager CRUD.

P) Next step after the stack runs

Replace public/index.php with a controller that lists tasks.

Query DB using src/db.php.

Render HTML with templates/.

That’s where real learning happens.

End of notes.
"""