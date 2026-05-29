# Lab 10 - CI/CD pipeline for Laravel

Проект подготовлен под лабораторную работу 10: тесты с покрытием не ниже 50%, конфиги сред, GitHub Actions, Larastan/PHPStan, Laravel Pint и симуляция деплоя.

## Что добавлено

- `.github/workflows/ci.yml` - GitHub Actions pipeline.
- `.env.dev`, `.env.uat`, `.env.prod`, `.env.ci` - конфиги сред.
- `phpstan.neon` - конфиг Larastan/PHPStan.
- `pint.json` - конфиг Laravel Pint.
- `tests/Feature/*` и `tests/Unit/ModelConfigurationTest.php` - тесты приложения.
- `setup_lab10.sh`, `setup_lab10.ps1` - полная установка проекта одной командой.
- `scripts/run_ci_checks.sh`, `run_ci_checks.ps1` - локальный запуск проверок.
- `scripts/create_test_failure.sh`, `scripts/create_lint_failure.sh`, `scripts/cleanup_demo_failures.sh` - файлы-помощники для скриншотов с ошибками.

## Быстрый запуск после распаковки

Linux/macOS/Git Bash:

```bash
bash setup_lab10.sh
php artisan serve
```

Windows PowerShell:

```powershell
powershell -ExecutionPolicy Bypass -File .\setup_lab10.ps1
php artisan serve
```

Если не нужно запускать `npm install` и сборку фронта:

```bash
bash setup_lab10.sh --skip-npm
```

Скрипт делает следующее:

1. Копирует `.env.dev` в `.env`, если `.env` ещё нет.
2. Устанавливает Composer-зависимости и при необходимости докачивает `larastan/larastan` для статического анализа.
3. Создаёт `database/database.sqlite`.
4. Генерирует `APP_KEY`.
5. Очищает config cache.
6. Выполняет `php artisan migrate:fresh --seed --force`.
7. Устанавливает npm-зависимости и выполняет `npm run build`, если npm установлен.
8. Форматирует код через Pint.
9. Запускает тесты.
10. Если локально установлен Xdebug или PCOV, проверяет coverage командой `php artisan test --coverage --min=50`.
11. Запускает Larastan/PHPStan.
12. Запускает Pint в test mode.

Тестовый вход после seed:

```text
login: admin
password: admin12345
```

## Локальная проверка перед push

```bash
bash scripts/run_ci_checks.sh
```

На Windows:

```powershell
powershell -ExecutionPolicy Bypass -File .\run_ci_checks.ps1
```

Если локально нет Xdebug/PCOV, скрипт запустит тесты без coverage gate. В GitHub Actions coverage проверяется обязательно, потому что pipeline устанавливает Xdebug. Первый запуск требует доступ к интернету, если Larastan ещё не установлен в `vendor`.

## Ветки

Pipeline запускается на `push`, `pull_request` и manual `workflow_dispatch` для веток:

```text
dev, develop, development, qa, uat, main, master
```

Рекомендуемый набор для сдачи:

```bash
git checkout -b dev
git push -u origin dev

git checkout -b qa
git push -u origin qa

git checkout main
git push -u origin main
```

## Шаги pipeline

### 1. Tests and coverage gate

Команда:

```bash
XDEBUG_MODE=coverage php artisan test --coverage --min=50
```

Pipeline падает, если хотя бы один тест завершился ошибкой или покрытие ниже 50%.

### 2. Static analysis with Larastan / PHPStan

Команда:

```bash
php vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=1G --error-format=github
```

Pipeline падает при любой ошибке статического анализа.

### 3. Lint check with Laravel Pint

Команда:

```bash
php vendor/bin/pint --test
```

Pipeline запускает linter именно в test mode и падает при любом нарушении форматирования.

### 4. Simulated deploy

Deploy job запускается только после успешных тестов, Larastan и Pint:

```text
dev / develop / development -> .env.dev  -> Deploying to DEV with .env.dev
qa / uat                    -> .env.uat  -> Deploying to UAT with .env.uat
main / master               -> .env.prod -> Deploying to PROD with .env.prod
```

### 5. Manual approval for production

Для `main` и `master` job использует GitHub Environment `production`. Чтобы включить ручной approve:

1. Открой GitHub repository.
2. Перейди в `Settings -> Environments`.
3. Создай environment с названием `production`.
4. Включи `Required reviewers`.
5. Добавь себя, преподавателя или maintainer.

После этого production deploy будет ждать approve перед выполнением.

## Что показать на скриншотах

Нужно 3 скриншота.

### Скриншот 1 - успешный pipeline

Сделай обычный push в `dev`, `qa` или `main`. На скрине должно быть видно:

- `Actions -> Laravel CI/CD Lab 10`.
- Зелёный статус workflow.
- Успешные jobs: `Tests and coverage gate`, `Static analysis with Larastan / PHPStan`, `Lint check with Laravel Pint`.
- Deploy job с сообщением `Deploying to DEV with .env.dev`, `Deploying to UAT with .env.uat` или `Deploying to PROD with .env.prod`.

### Скриншот 2 - ошибка на тестах

Создать намеренно падающий тест:

```bash
bash scripts/create_test_failure.sh
git add tests/Feature/BrokenPipelineTest.php
git commit -m "test: demonstrate failing pipeline"
git push
```

На скрине должно быть видно, что job `Tests and coverage gate` упал.

После скрина удалить тест:

```bash
bash scripts/cleanup_demo_failures.sh
git add .
git commit -m "test: remove intentional failure"
git push
```

### Скриншот 3 - ошибка linter

Создать намеренно плохо отформатированный PHP-файл:

```bash
bash scripts/create_lint_failure.sh
git add app/Services/BadFormattedExample.php
git commit -m "style: demonstrate linter failure"
git push
```

На скрине должно быть видно, что job `Lint check with Laravel Pint` упал на команде `php vendor/bin/pint --test`.

После скрина удалить файл:

```bash
bash scripts/cleanup_demo_failures.sh
git add .
git commit -m "style: remove intentional linter failure"
git push
```

## Что сдавать

1. Ссылка на GitHub repository.
2. `.github/workflows/ci.yml`.
3. `.env.dev`, `.env.uat`, `.env.prod`, `.env.ci`.
4. `PIPELINE.md`.
5. 3 скриншота: успешный pipeline, ошибка тестов, ошибка linter.

## Важно про `.env`

Основной `.env` не должен попадать в репозиторий. В `.gitignore` уже есть строка `.env`. Скрипт создаёт `.env` только локально после распаковки проекта.
