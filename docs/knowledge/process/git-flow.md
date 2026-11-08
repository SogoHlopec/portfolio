---
type: Process
title: Git Flow & Conventional Commits
description: Стратегия веток, формат коммитов, работа через Pull Requests с AI-ревью агента (ponytail)
tags:
    - git
    - workflow
    - conventional-commits
    - pull-requests
    - ponytail
    - code-review
status: stable
generated:
    by: opencode/okf-note
    at: '2026-08-27T10:00:00Z'
verified:
    by: human:sogohlopec
    at: '2026-08-27T10:00:00Z'
sources:
    - id: git-flow-note
      resource: /docs/notes/Git flow.md
      title: Git flow (raw notes)
      author: human:sogohlopec
      last_modified: '2026-03-21'
    - id: arch-note
      resource: /docs/notes/Архитектура - PORTFOLIO.md
      title: Архитектура - PORTFOLIO (раздел Git flow)
      author: human:sogohlopec
      last_modified: '2026-03-17'
---

# Обзор

Документированный и применяемый в проекте Git workflow: защищённая ветка `main`, feature-ветки для всей работы, Conventional Commits, обязательные Pull Requests с AI-ревью.

---

# Ветки

| Префикс      | Назначение                                                   | Примеры                                                   |
| ------------ | ------------------------------------------------------------ | --------------------------------------------------------- |
| `main`       | **Всегда production-ready**. Никаких экспериментов напрямую. | —                                                         |
| `feature/*`  | Новые фичи: страницы, компоненты, логика                     | `feature/projects-section`, `feature/theme-toggle`        |
| `fix/*`      | Багфиксы: сломанный рендер, ошибки в данных                  | `fix/mdx-render`, `fix/hydration-error`                   |
| `chore/*`    | Инфраструктура: конфиги, CI, депы, линтеры                   | `chore/config-static-export`, `chore/setup-quality-tools` |
| `content/*`  | Контент: новые проекты, статьи, переводы                     | `content/add-zennek-case`, `content/translate-ru`         |
| `refactor/*` | Переписывание кода без изменения функционала                 | `refactor/extract-hooks`, `refactor/simplify-toggle`      |

**Базовый минимум**: `main` + `feature/*` уже даёт 80% пользы. Остальные типы добавляются по мере роста.

---

# Conventional Commits

Формат: `<тип>(<область>): <сообщение>` — **на английском**.

| Тип        | Смысл                                    | Пример                                                  |
| ---------- | ---------------------------------------- | ------------------------------------------------------- |
| `feat`     | Новая функциональность                   | `feat(ui): add theme toggle component`                  |
| `fix`      | Исправление бага                         | `fix: resolve hydration mismatch in ThemeToggle`        |
| `chore`    | Инфраструктура, конфиги, депы            | `chore(deps): pin devDependencies to exact versions`    |
| `docs`     | Документация                             | `docs: update architecture decision log`                |
| `refactor` | Рефакторинг без изменения поведения      | `refactor(ui): use useSyncExternalStore in ThemeToggle` |
| `style`    | Форматирование, пробелы, точки с запятой | `style: prettier format globals.css`                    |
| `test`     | Добавление/исправление тестов            | `test: add unit tests for date formatting`              |

**Область (scope)**: опционально, в скобках — логическая единица (`ui`, `config`, `deps`, `theme`, `i18n`, `content`).

---

# Pull Request Workflow

**Полный цикл разработки задачи**:

1. **Создать ветку** из актуального `main`:

    ```bash
    git checkout main && git pull origin main
    git checkout -b feature/имя-задачи
    ```

2. **Работать**: писать код, делать атомарные коммиты (Husky проверяет линтер/преттиер на каждом).

3. **Запушить ветку**:

    ```bash
    git push origin feature/имя-задачи
    ```

4. **Открыть Pull Request** на GitHub: `feature/*` → `main`.
    - Описать PR: что сделано, скриншоты, проверено ли на мобильной.
    - Запустить ревью агентом: `/ponytail-review` (дифф ветки или ссылка на PR). Плагин ponytail подключён в `.opencode/opencode.json`.

5. **Self-review**: просмотреть свои же изменения во вкладке _Files changed_.

6. **Исправить замечания** (если есть) → новые коммиты в ту же ветку → пуш → повторить `/ponytail-review`.

7. **Merge**: _Squash and merge_ (история чистая, один коммит на PR).

8. **Удалить ветку** на GitHub и локально:
    ```bash
    git checkout main && git pull origin main
    git branch -d feature/имя-задачи
    ```

---

# Защита ветки `main` (Branch Protection Rules)

Настроено в GitHub Settings → Branches → `main`:

- ✅ Require a pull request before merging
- ✅ Require approvals (1)
- ✅ Dismiss stale PR approvals when new commits are pushed
- ✅ Require status checks to pass before merging (CI: build, lint, typecheck)
- ✅ Require branches to be up to date before merging
- ✅ Do not allow bypassing the above settings

**Результат**: Прямой пуш в `main` невозможен. Любой код проходит PR + CI + ревью агентом.

---

# Agent review (ponytail)

- Подключён как OpenCode-плагин в `.opencode/opencode.json` (ключ `plugins`, точная версия запинана), файл коммитится — плагин подтянется автоматически при старте opencode.
- Ревью по запросу: `/ponytail-review` — читает дифф **и** связанный код (callers, тесты, потоки данных), а не только изменённые строки. Находит баги, security, гонки/нагрузку, недостающие тесты, медленные пути и лишний код. Отчёт без правок кода: _Must fix / Should fix / Nice to have_ + `Verdict: Ship.` или `Verdict: fix 1 and 3 first.`
- Сопутствующие команды: `/ponytail-audit` (аудит всего репозитория, ранжированный), `/ponytail-debt` (реестр `shortcut:` комментариев), `/ponytail-help`.
- Плагин также инжектирует always-on ruleset («ленивый сеньор»: меньше лишнего кода) каждый ход и в субагентов. Уровень по умолчанию — `full`; переключается `/ponytail lite|full|ultra|off` (`off` отключает ruleset, команды работают). Машинный дефолт: `PONYTAIL_DEFAULT_MODE` или `~/.config/ponytail/config.json`.
- **Правило**: не слепо применять замечания агента — те же требования, что были к CodeRabbit: если агент ошибается (как бот с Radix UI unified package) — аргументированно отклонить и зафиксировать почему.

> Заменил CodeRabbit AI (удалён 2026-11-06: `.coderabbit.yaml` удалён; GitHub App uninstalled владельцем — см. `log.md`).

---

# Почему это не "бюрократия"

- **+1 команда** (`git checkout -b`) — это всё overhead.
- **Снижает риски**: сломанный билд не попадает в прод, история понятная, контекст сохранён в PR.
- **Масштабируемость**: если проект вырастет или появится команда — не придётся переучиваться.
- **Портфолио-ценность**: работодатель видит зрелый engineering process в истории коммитов и PR.

---

# Связанные концепты

- [Phase 0 Planning & DevOps](/phases/phase-0-planning-devops.md) — где настраивалась защита веток (ревью с тех пор переведено с CodeRabbit на ponytail)
- [Project Plan](/decisions/project-plan.md) — полная дорожная карта с привязкой к типам веток

[^git-flow-note]: Git flow (raw notes)

[^arch-note]: Архитектура - PORTFOLIO (раздел Git flow)
