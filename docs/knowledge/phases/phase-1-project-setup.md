---
type: Phase Notes
title: Phase 1 — Project Setup (Фундамент)
description: Инициализация Next.js, настройка static export, shadcn/ui, ESLint/Prettier/Husky/lint-staged
tags:
    - phase-1
    - project-setup
    - nextjs
    - tailwind
    - shadcn-ui
    - eslint
    - prettier
    - husky
status: stable
generated:
    by: opencode/okf-note
    at: '2026-08-27T10:00:00Z'
verified:
    by: human:sogohlopec
    at: '2026-08-27T10:00:00Z'
sources:
    - id: phase1-init
      resource: /docs/notes/Реализация Phase 0 chore(init) create Next.js app with App Router, TypeScript, and Tailwind.md
      title: Инициализация Next.js приложения (raw notes)
      author: human:sogohlopec
      last_modified: '2026-05-20'
    - id: phase1-config
      resource: /docs/notes/Реализация Phase 1 Project Setup (Фундамент) chore(config) configure next.config.ts for static export and unoptimized images.md
      title: Настройка next.config.ts для static export (raw notes)
      author: human:sogohlopec
      last_modified: '2026-05-27'
    - id: phase1-shadcn
      resource: /docs/notes/Phase 1 Project Setup (Фундамент) chore(ui) initialize shadcn ui CLI and configure base styles.md
      title: Инициализация shadcn/ui (raw notes)
      author: human:sogohlopec
      last_modified: '2026-06-09'
    - id: phase1-tools
      resource: /docs/notes/Реализация Phase 1 Project Setup (Фундамент) chore(tools) setup ESLint, Prettier, Husky, and lint-staged (чтобы плохой код физически не мог попасть в коммит).md
      title: Настройка инструментов качества кода (raw notes)
      author: human:sogohlopec
      last_modified: '2026-06-07'
---

# Обзор

Phase 1 создала полноценный фундамент проекта: Next.js 16 с App Router, TypeScript, Tailwind CSS v4, shadcn/ui, и автоматизированный pipeline качества кода (ESLint + Prettier + Husky + lint-staged). Все задачи выполнены последовательно через отдельные PR.

---

# Задачи и выполнение

## 1. `chore(init): create Next.js app with App Router, TypeScript, and Tailwind`

**Цель**: Создать проект Next.js локально через `npx create-next-app@latest .` с правильными флагами.

**Выборы в интерактивном опросе**:

- TypeScript: **Yes** — стандарт индустрии, защита от ошибок до рантайма
- ESLint: **Yes** — статический анализ, правила React
- Tailwind CSS: **Yes** — utility-first стилизация
- `src/` directory: **Yes** — разделение кода и конфигов
- App Router: **Yes** — современная архитектура (RSC, Server Components)
- Import alias `@/*`: **No** — дефолт `@/*` устраивает

**Результат**: Проект в папке `portfolio/` с `package.json`, `tsconfig.json`, `tailwind.config.ts` (позже удалён — см. Tailwind v4), `src/app/`.

**Важно**: Все зависимости — **только локальные** (`npm install`, не `npm install -g`). `npx` запускает локальные бинарники из `node_modules/.bin/`. Это гарантирует воспроизводимость на CI/CD и изоляцию проектов.

---

## 2. `chore(config): configure next.config.ts for static export and unoptimized images`

**Цель**: Подружить Next.js с GitHub Pages (статический хостинг без Node.js).

**Настройки в `next.config.ts`**:

```typescript
const isProd = process.env.NODE_ENV === 'production';
const repoName = '/portfolio';

const nextConfig: NextConfig = {
    output: 'export', // SSG → папка out/
    images: { unoptimized: true }, // нет сервера для оптимизации
    trailingSlash: true, // /about/ → /about/index.html (фикс GH Pages)
    basePath: isProd ? repoName : '', // префикс только в проде
    assetPrefix: isProd ? `${repoName}/` : '',
};
export default nextConfig;
```

**Дополнительно**: Файл `public/.nojekyll` — отключает Jekyll на GitHub Pages (иначе игнорируется папка `_next`).

**CodeRabbit замечание**: Запинить `devDependencies` до точных версий (x.y.z) в `package.json` для предотвращения version drift. Выполнено через `npm list --depth=0` → обновление `package.json` → `npm install` → коммит в тот же PR.

---

## 3. `chore(ui): initialize shadcn/ui CLI and configure base styles`

**Цель**: Добавить shadcn/ui — систему копируемых компонентов (не npm-пакет).

**Инициализация**: `npx shadcn@latest init`

- Style: **Default** (чистый, гибкий)
- Base color: **Zinc** (нейтральный темно-серый, хорошо в light/dark)
- CSS variables: **Yes** (критично для dark mode)
- Global CSS: `src/app/globals.css` (автоопределено)
- Tailwind config: `tailwind.config.ts` (автоопределено, но Tailwind v4 его не использует)
- Import aliases: `@/components`, `@/lib/utils` (дефолт)
- React Server Components: **Yes**

**Созданные файлы**:

- `components.json` — конфиг shadcn (читается CLI при `add`)
- `src/lib/utils.ts` — функция `cn()` = `clsx` + `tailwind-merge` (склеивает классы, решает конфликты Tailwind)
- `src/app/globals.css` — добавлены CSS-переменные shadcn (`:root` / `.dark`) в HSL без скобок (для работы `bg-primary/50`)

**Важно про Tailwind v4**: Нет файла `tailwind.config.ts` — конфигурация в `globals.css` через `@theme` directive. Это CSS-first подход, быстрее и чище.

**Первый компонент**: `npx shadcn@latest add button` → `src/components/ui/button.tsx` (исходник в проекте, полный контроль).

---

## 4. `chore(tools): setup ESLint, Prettier, Husky, and lint-staged`

**Цель**: Плохой код физически не может попасть в коммит.

### Установка:

```bash
npm install --save-dev prettier eslint-config-prettier
npm install --save-dev husky lint-staged
npx husky init  # создаёт .husky/pre-commit + добавляет "prepare": "husky" в package.json
```

### Конфигурации:

**`.prettierrc`** (стандарт проекта):

```json
{
    "semi": true,
    "singleQuote": true,
    "tabWidth": 4, // ВАЖНО: 4 пробела (не 2!) — по AGENTS.md
    "trailingComma": "es5",
    "printWidth": 100, // ВАЖНО: 100 (не 80!) — по AGENTS.md
    "bracketSpacing": true,
    "arrowParens": "always"
}
```

**`.prettierignore`**: `.next`, `out`, `node_modules`, `package-lock.json`, `public`

**`eslint.config.mjs`** (Flat Config, Next.js 16):

```javascript
import { defineConfig, globalIgnores } from 'eslint/config';
import nextVitals from 'eslint-config-next/core-web-vitals';
import nextTs from 'eslint-config-next/typescript';
import eslintConfigPrettier from 'eslint-config-prettier';

export default defineConfig([
    ...nextVitals,
    ...nextTs,
    eslintConfigPrettier, // ВАЖНО: в конце, отключает конфликтующие правила форматирования
    globalIgnores(['.next/**', 'out/**', 'build/**', 'next-env.d.ts']),
]);
```

**`.husky/pre-commit`**:

```
npx lint-staged
```

**`.lintstagedrc.json`** (исправлено под Next.js 16):

```json
{
    "*.{js,jsx,ts,tsx}": ["eslint --fix", "prettier --write"],
    "*.{json,css,md}": ["prettier --write"]
}
```

_Примечание_: В Next.js 16 `next lint` устарел/удалён — используется нативный `eslint --fix`.

### Как работает защита:

```
git commit → Git hook pre-commit → .husky/pre-commit → npx lint-staged
  → читает .lintstagedrc.json → запускает eslint/prettier ТОЛЬКО для staged файлов
  → если ошибки — коммит блокируется
  → если prettier поправил — файл перезаписывается, коммит проходит
```

---

# Ключевые решения

| Решение                                          | Обоснование                                                                    |
| ------------------------------------------------ | ------------------------------------------------------------------------------ |
| Локальные зависимости через npx                  | Воспроизводимость, изоляция, CI/CD работает из коробки                         |
| `output: 'export'` + `trailingSlash: true`       | Работающий роутинг на GitHub Pages без сервера                                 |
| shadcn/ui через CLI (copy-paste)                 | Полный контроль над компонентами, 0 лишнего бандла, a11y из коробки (Radix UI) |
| Tailwind v4 без tailwind.config.ts               | CSS-first, быстрее сборка, нативные CSS-переменные                             |
| 4-space indent, printWidth 100                   | Соответствует Prettier конфигу в AGENTS.md                                     |
| Husky + lint-staged (не pre-commit на все файлы) | Скорость: проверяются только staged файлы (2-3 сек)                            |
| `eslint --fix` вместо `next lint`                | Next.js 16 убрал встроенный lint wrapper                                       |

---

# CodeRabbit инциденты и уроки

1. **Pin devDependencies**: Бот заставил запинить версии в `package.json` — профессиональная практика.
2. **Radix UI unified package**: Бот предложил старый импорт `@radix-ui/react-slot` — ошибочно. shadcn/ui v4 использует единый пакет `radix-ui` с namespace `Slot.Root`. Ответ в PR: "CodeRabbit, this is incorrect. Project uses modern unified `radix-ui` package...". Разговор закрыт как resolved.
3. **Next.js 16 lint**: Бот/конфиг предложил `next lint --fix` — не работает в v16. Исправлено на `eslint --fix`.

---

# Связанные концепты

- [Portfolio Architecture](/architecture/portfolio-architecture.md) — итоговая архитектура после Phase 1
- [Phase 2 Design System](/phases/phase-2-design-system.md) — следующая фаза

[^phase1-init]: Инициализация Next.js приложения (raw notes)

[^phase1-config]: Настройка next.config.ts для static export (raw notes)

[^phase1-shadcn]: Инициализация shadcn/ui (raw notes)

[^phase1-tools]: Настройка инструментов качества кода (raw notes)
