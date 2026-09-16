---
type: Decision
title: Testing Strategy
description: 'Прагматичный подход к тестированию портфолио: Vitest + RTL для unit/integration, Playwright для E2E, фокус на бизнес-логике'
tags:
    - testing
    - vitest
    - playwright
    - react-testing-library
    - ci-cd
    - coverage
status: stable
generated:
    by: opencode/okf-note
    at: '2026-08-27T10:00:00Z'
verified:
    by: human:sogohlopec
    at: '2026-08-27T10:00:00Z'
sources:
    - id: testing-note
      resource: /docs/notes/Стоит ли покрывать тестами проект Портфолио.md
      title: Стоит ли покрывать тестами проект Портфолио (raw notes)
      author: human:sogohlopec
      last_modified: '2026-04-11'
---

# Обзор

Портфолио — статический контентный сайт. Нет сложных транзакций, корзин, стейт-менеджмента. Тесты нужны не для "галочки покрытия", а чтобы **показать senior-level engineering mindset** на собеседованиях и защитить критическую логику.

**Стек 2026**: Vitest + React Testing Library (unit/integration) + Playwright (E2E). Не Jest (медленный), не Cypress (Playwright быстрее, лучше GH Actions интеграция).

---

# Что тестировать (✅) и что НЕ тестировать (❌)

## ❌ Антипаттерны (не тратить время)

| Категория            | Пример                                                 | Почему нет                                               |
| -------------------- | ------------------------------------------------------ | -------------------------------------------------------- |
| shadcn/ui компоненты | Button, Card, Badge                                    | Уже протестированы авторами (Radix UI + их тесты)        |
| Snapshot верстки     | `toMatchSnapshot()` на JSX                             | Верстка меняется постоянно — обновление снапшотов = боль |
| Статические тексты   | "Проверить, что на главной есть 'Fullstack Developer'" | Нет бизнес-ценности, ломается при любом правке копирайта |
| 100% coverage        | Гнаться за цифрой                                      | Пустая трата времени, ложное чувство безопасности        |

## ✅ Прагматичный фокус (что тестировать)

### Уровень 1: Unit-тесты (Vitest) — чистые функции

**Цель**: Показать умение тестировать бизнес-логику, утилиты, парсеры.

| Что тестировать                      | Пример теста                                         |
| ------------------------------------ | ---------------------------------------------------- |
| `formatDate(date, locale)`           | Парсинг дат для EN/RU, edge cases (невалидные входы) |
| `getTopProjects(projects, count)`    | Возвращает массив нужной длины, только `isTop: true` |
| `filterProjectsByTag(projects, tag)` | Фильтрация работает, пустой массив при отсутствии    |
| MDX frontmatter парсинг              | Извлечение `title`, `date`, `tags` из `.mdx` файла   |
| `cn()` utility                       | Склеивание классов, разрешение конфликтов Tailwind   |

### Уровень 2: Интеграционные (RTL) — сложные UI-механики

**Цель**: Показать работу с React Testing Library, user-event, мокирование контекстов.

| Компонент          | Сценарий                                                                        |
| ------------------ | ------------------------------------------------------------------------------- |
| `ThemeToggle`      | Клик → вызов `setTheme` / проверка класса `.dark` на `document.documentElement` |
| `LanguageSwitcher` | Клик на язык → `router.push` с правильным путем                                 |
| Project filters    | Клик на тег "React" → список отфильтровался (проверка DOM)                      |
| `ThemeProvider`    | Монтирование → правильный `defaultTheme` применяется                            |

### Уровень 3: E2E (Playwright) — Happy Path

**Цель**: Один-два теста критического пользовательского пути. Playwright пишет видео прохода.

| Сценарий         | Шаги                                                                                  |
| ---------------- | ------------------------------------------------------------------------------------- |
| **Main flow**    | Главная → клик "Мои проекты" → страница проектов → клик первый проект → кейс открылся |
| **Theme toggle** | Главная → клик тема → переключилась → рефреш → тема сохранилась (localStorage)        |

---

# Когда внедрять (Timeline)

**НЕ на Phase 1-2** (пустой проект / настройка стилей — тесты только тормозят).

**Идеальный момент**: **между Phase 4 (UI) и Phase 5 (Content)**, когда готов базовый каркас, утилиты, компоненты переключения.

План как отдельная микро-фаза **Phase 4.5: Quality Assurance Setup**:

1. `chore(test): install Vitest and React Testing Library, configure setup files`
2. `feat(test): write unit tests for date formatting and data fetching utilities`
3. `feat(test): write integration tests for ThemeToggle and LanguageSwitcher components`
4. `chore(test): install Playwright and write basic E2E navigation test`

---

# CI/CD Integration (GitHub Actions)

Тесты должны блокировать мерж в `main`. В `.github/workflows/ci.yml` (или расширять `deploy.yml`):

```yaml
jobs:
    test:
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@v4
            - uses: actions/setup-node@v4
              with: { node-version: '20', cache: npm }
            - run: npm ci
            - run: npm run lint # ESLint
            - run: npx tsc --noEmit # Typecheck
            - run: npm run test # Vitest (unit + integration)
            - run: npx playwright install --with-deps chromium
            - run: npm run test:e2e # Playwright
```

**Branch Protection**: добавить `test` job в required status checks для `main`. Зелёная кнопка Merge неактивна, пока тесты не пройдут.

---

# Локальная защита (Husky)

В `.husky/pre-commit` (или отдельный `pre-push` для E2E):

```bash
# pre-commit: быстрые unit/integration тесты только для changed files
npx lint-staged
# или через vitest --related (требует настройки)

# pre-push: полный прогон (опционально, можно оставить только на CI)
# npx vitest run
# npx playwright test
```

_Важно_: E2E тесты медленные — не запускать на каждом коммите. Достаточно на CI при PR.

---

# Конфигурация Vitest (пример)

`vitest.config.ts`:

```typescript
import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
    plugins: [react()],
    test: {
        environment: 'jsdom',
        setupFiles: ['./vitest.setup.ts'],
        include: ['src/**/*.{test,spec}.{ts,tsx}'],
        coverage: {
            provider: 'v8',
            reporter: ['text', 'json', 'html'],
            // Не ставить пороги — фокус на качестве, не цифре
        },
    },
    resolve: {
        alias: { '@': path.resolve(__dirname, './src') },
    },
});
```

`vitest.setup.ts`:

```typescript
import '@testing-library/jest-dom';
import { vi } from 'vitest';

// Мок next-themes для тестов компонентов с темой
vi.mock('next-themes', () => ({
    useTheme: () => ({
        theme: 'light',
        setTheme: vi.fn(),
        resolvedTheme: 'light',
    }),
}));
```

---

# Конфигурация Playwright (пример)

`playwright.config.ts`:

```typescript
import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './e2e',
    fullyParallel: true,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 2 : 0,
    workers: process.env.CI ? 1 : undefined,
    reporter: 'html',
    use: {
        baseURL: 'http://localhost:3000',
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
    },
    projects: [
        { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
        { name: 'mobile-chrome', use: { ...devices['Pixel 5'] } },
    ],
    webServer: {
        command: 'npm run start', // serve out/ после build
        url: 'http://localhost:3000',
        reuseExistingServer: !process.env.CI,
        timeout: 120000,
    },
});
```

---

# Как отвечать на собеседовании про тесты

> _"Я не сторонник покрытия 100% ради красивой цифры, особенно в UI. В своём проекте я сфокусировался на тестировании критической бизнес-логики (парсеры данных, фильтры, форматирование дат) через Vitest, а сложные UI-интеракции (переключение темы, смена языка, фильтры проектов) — через React Testing Library. Критический пользовательский путь (Happy Path) покрыт E2E-тестами на Playwright с записью видео. Всё это автоматизировано в CI-пайплайне через GitHub Actions: линт, тайпчек, юнит/интеграционные, E2E — в ветку `main` физически не может попасть сломанный билд."_

Это ответ Senior-инженера. Показывает: понимание trade-offs, выбор правильных инструментов, CI/CD интеграция, фокус на ценности.

---

# Связанные концепты

- [Phase 4.5 QA Setup](/phases/phase-4-5-qa-setup.md) — план внедрения (планируется)
- [CI/CD Deployment](/decisions/ci-cd-deployment.md) — pipeline с тестами (планируется)

[^testing-note]: Стоит ли покрывать тестами проект Портфолио (raw notes)
