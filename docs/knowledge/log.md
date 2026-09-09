# Журнал изменений

## 2026-09-10

- **Рабочая заметка**: подключена интеграция Next.js для AI-агентов — managed-блок `nextjs-agent-rules` в `AGENTS.md`, `logging.browserToTerminal` в `next.config.ts`, MCP-сервер `next-devtools` в `.opencode/opencode.json` (только для opencode). См. [Настройка AI-агентов Next.js](/work/2026-09-10-ai-agents-setup.md).

## 2026-08-27

- **Создание**: Добавлены концепты архитектуры и фаз реализации в OKF бандл:
    - [Portfolio Architecture](/architecture/portfolio-architecture.md) — полная архитектура статического портфолио
    - [Phase 0 — Planning & DevOps Setup](/phases/phase-0-planning-devops.md) — настройка репозитория и CodeRabbit
    - [Phase 1 — Project Setup](/phases/phase-1-project-setup.md) — Next.js, static export, shadcn/ui, качество кода
    - [Phase 2 — Design System & Theming](/phases/phase-2-design-system.md) — темы, ThemeToggle, CSS переменные, шрифты
    - [Git Flow & Conventional Commits](/process/git-flow.md) — стратегия веток, коммиты, PR workflow
    - [Multilingual Strategy (i18n)](/process/multilingual-strategy.md) — динамические роуты, словари, решение про BE
    - [Testing Strategy](/process/testing-strategy.md) — Vitest + RTL + Playwright, прагматичный подход
    - [Project Plan & Roadmap](/decisions/project-plan.md) — 10 фаз, CI/CD, Content/MDX, чек-лист релиза
- **Обновление**: Корневой `index.md` и директориальные индексы обновлены для навигации по новым концептам.

## 2026-08-24

- **Рабочая заметка**: зафиксирована настройка OKF-инструментария — скиллы установлены в `.opencode/skills`, создана команда `/okf-note`, `visualize` удалён в пользу Obsidian. См. [Настройка OKF-инструментария](/work/2026-08-25-okf-tooling-setup.md).
- **Создание**: бандл Portfolio развёрнут через `okf_init.py` — см. [Начало работы](getting-started.md).
