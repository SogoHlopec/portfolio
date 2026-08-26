---
type: Decision
title: Project Plan & Roadmap
description: 'Полная дорожная карта портфолио v2: 10 фаз от планирования до релиза, с задачами, best practices и Git интеграцией'
tags:
    - planning
    - roadmap
    - project-management
    - phases
status: stable
generated:
    by: opencode/okf-note
    at: '2026-08-27T10:00:00Z'
verified:
    by: human:sogohlopec
    at: '2026-08-27T10:00:00Z'
sources:
    - id: plan-note
      resource: /docs/notes/Составляем план задач.md
      title: Составляем план задач (raw notes)
      author: human:sogohlopec
      last_modified: '2026-03-31'
---

# Обзор

Валидированный и детализированный план реализации портфолио v2. Разбит на 10 последовательных фаз с атомарными задачами (1-3 часа каждая), привязанными к Git workflow и CI/CD.

---

# Фазы и задачи

## Phase 0: Planning & DevOps Setup ✅ **DONE**

- `chore(repo): init github repository and setup branch protection rules`
- `chore(ci): install CodeRabbit AI app to the repository`
- `chore(project): setup GitHub Projects board (Kanban: Todo, In Progress, Review, Done)`

## Phase 1: Project Setup (Фундамент) ✅ **DONE**

- `chore(init): create Next.js app with App Router, TypeScript, and Tailwind`
- `chore(config): configure next.config.ts for static export and unoptimized images`
- `chore(ui): initialize shadcn/ui CLI and configure base styles`
- `chore(tools): setup ESLint, Prettier, Husky, and lint-staged`

## Phase 2: Design System & Theming ✅ **DONE**

- `feat(theme): add next-themes and create ThemeProvider wrapper`
- `feat(ui): implement ThemeToggle component`
- `chore(styles): define CSS variables in globals.css for light/dark modes based on shadcn tokens`
- `feat(layout): create base RootLayout with custom fonts (next/font)`

## Phase 3: Internationalization (i18n) Core 🔄 **PLANNED**

- `feat(i18n): restructure app directory to use [lang] dynamic route`
- `feat(i18n): implement generateStaticParams to pre-build en and ru routes`
- `feat(i18n): create dictionaries (en.json, ru.json) and fetcher utility`
- `feat(ui): create LanguageSwitcher component`

## Phase 4: UI Implementation (Компоненты) 🔄 **PLANNED**

- `feat(ui): add shadcn components (Button, Badge, Card, Sheet for mobile menu)`
- `feat(layout): build responsive Header with navigation and sticky scroll`
- `feat(layout): build Footer with social links`
- `feat(home): build Hero section with CTA buttons`
- `feat(home): build Experience timeline component`

## Phase 5: Content System (TS + MDX) 🔄 **PLANNED**

- `feat(content): create TypeScript data files for Skills, Social Links, and Experience`
- `feat(mdx): install next-mdx-remote and setup parsing logic for local .mdx files`
- `feat(projects): create ProjectCard component and map top projects on Home page`
- `feat(projects): implement individual project pages (/projects/[slug]) using MDX`

## Phase 6: Animations & Polish 🔄 **PLANNED**

- `feat(anim): setup View Transitions API for seamless page routing`
- `feat(anim): create Framer Motion <FadeIn> wrapper for scroll reveals`
- `refactor(ui): apply <FadeIn> to sections on the Home page`
- `refactor(ui): add Tailwind transitions to interactive elements (hover states)`

## Phase 7: SEO & Performance 🔄 **PLANNED**

- `feat(seo): implement dynamic metadata generation for Next.js App Router`
- `feat(seo): setup OpenGraph images and Twitter cards`
- `feat(seo): generate static sitemap.xml and robots.txt`
- `chore(perf): audit and optimize image sizes, ensure lazy loading is active`

## Phase 8: CI/CD & Deployment 🔄 **PLANNED**

- `chore(ci): create github-actions.yml for Next.js GitHub Pages deployment`
- `chore(config): add .nojekyll file generation step`
- `fix(deploy): test pipeline, fix any basePath or static asset path issues`

## Phase 9: Testing & Quality Assurance 🔄 **PLANNED** (Phase 4.5 в заметке)

- `chore(test): install Vitest and React Testing Library, configure setup files`
- `feat(test): write unit tests for date formatting and data fetching utilities`
- `feat(test): write integration tests for ThemeToggle and LanguageSwitcher components`
- `chore(test): install Playwright and write basic E2E navigation test`

## Phase 10: Final QA & Release 🔄 **PLANNED**

- Lighthouse audit (95-100 по всем 4 метрикам)
- Hydration errors check (Console clean)
- Mobile real-device testing
- Social cards validation (Telegram, Twitter)
- 404 page check
- Accessibility: Tab navigation, focus visible
- Release tag + announcement

---

# Git Integration в Workflow

**Ежедневный процесс разработчика**:

```bash
# 1. Взять задачу из GitHub Projects (Todo → In Progress)
# 2. Создать ветку
git checkout main && git pull origin main
git checkout -b feature/имя-задачи

# 3. Писать код, атомарные коммиты
git add .
git commit -m "feat(scope): описание изменения"  # Husky: lint + prettier

# 4. Пуш и PR
git push origin feature/имя-задачи
# GitHub: New PR → main, заполнить template

# 5. CodeRabbit AI ревью (авто)
# 6. Self-review (Files changed)
# 7. Исправить замечания → push (авто-обновление PR)

# 8. Squash and Merge
# 9. GitHub Actions: build → deploy to GitHub Pages
# 10. Локально: git checkout main && git pull && git branch -d feature/...
```

**Коммиты**: Conventional Commits (`feat`, `fix`, `chore`, `docs`, `refactor`, `style`, `test`) на английском.

---

# CI/CD & Deployment Plan (детально)

## next.config.ts (production-ready)

```typescript
import type { NextConfig } from 'next';

const isProd = process.env.NODE_ENV === 'production';
const repoName = '/portfolio';

const nextConfig: NextConfig = {
    output: 'export',
    trailingSlash: true,
    images: { unoptimized: true },
    basePath: isProd ? repoName : '',
    assetPrefix: isProd ? `${repoName}/` : '',
};

export default nextConfig;
```

## `.github/workflows/deploy.yml`

```yaml
name: Deploy to GitHub Pages

on:
  push:
    branches: ["main"]
  workflow_dispatch:

permissions:
  contents: read
  pages: write
  id-token: write

concurrency:
  group: "pages"
  cancel-in-progress: false

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with: { node-version: "20", cache: npm }
      - uses: actions/configure-pages@v5
        with: { static_site_generator: next }
      - run: npm ci
      - run: npm run build
      - uses: actions/upload-pages-artifact@v3
        with: { path: ./out }

  deploy:
    environment: { name: github-pages, url: ${{ steps.deployment.outputs.page_url }} }
    runs-on: ubuntu-latest
    needs: build
    steps:
      - uses: actions/deploy-pages@v4
        id: deployment
```

**Ключевые моменты**:

- `actions/configure-pages` с `static_site_generator: next` — сам создаёт `.nojekyll`.
- Артефакт `./out` загружается и деплоится.
- `concurrency` предотвращает параллельные деплои.

---

# Content & MDX Strategy

## Архитектура папок

```
src/
├── data/              # Плоские TS-данные (map'ятся в циклы)
│   ├── skills.ts      # [{ name: 'React', icon: 'react', category: 'frontend' }]
│   ├── experience.ts  # Места работы (даты, роли — не требуют перевода)
│   └── socials.ts     # Ссылки на соцсети
├── content/           # Объёмный контент (MDX)
│   └── projects/
│       ├── en/zennek-integration.mdx
│       ├── ru/zennek-integration.mdx
│       └── be/zennek-integration.mdx
├── dictionaries/      # UI текст 1-к-1
│   ├── en.json
│   ├── ru.json
│   └── be.json
└── lib/mdx.ts         # Парсинг .mdx файлов (next-mdx-remote)
```

**Когда TypeScript**: Данные для карточек, списков, циклов `map()`.
**Когда MDX**: Детальные страницы кейсов — позволяет вставлять React-компоненты внутрь текста (`<TechStack tags={['Next.js', 'Tailwind']} />`).

---

# Internationalization Strategy

**Языки**: EN, RU (Release 1.0), BE (Phase 2 отдельным PR).

**Паттерн для SSG (App Router)**:

```typescript
// src/app/[lang]/layout.tsx
export async function generateStaticParams() {
    return [{ lang: 'en' }, { lang: 'ru' }];
}
```

**Словари**: `src/dictionaries/{en,ru,be}.json` + `src/lib/i18n.ts` (динамический импорт).
**Переключатель**: `LanguageSwitcher` (клиентский, `next/navigation`).

**Почему не next-intl**: Нативный паттерн = 0 депенденси, полный контроль, TypeScript автодополнение работает. `next-intl` добавит KB в бандл без необходимости.

---

# Quality & Production Checklist (Pre-release)

| Проверка       | Инструмент                        | Цель                                                        |
| -------------- | --------------------------------- | ----------------------------------------------------------- |
| Performance    | Lighthouse (Incognito)            | 95-100 Performance, Accessibility, Best Practices, SEO      |
| Hydration      | DevTools Console                  | 0 ошибок `Text content does not match server-rendered HTML` |
| Mobile         | Реальный телефон (не DevTools)    | Удобные тап-таргеты, читаемый шрифт, работающее бургер-меню |
| Social Cards   | Telegram / Twitter Card Validator | Правильный og:image, title, description                     |
| 404 Page       | Ручной переход на `/404`          | Кастомная красивая страница `not-found.tsx`                 |
| A11y (Tab nav) | Только клавиатура (Tab)           | Виден фокус на всех ссылках/кнопках, порядок логичный       |

---

# Валидация архитектуры (из исходного промпта)

| Аспект                | Вердикт              | Детали                                         |
| --------------------- | -------------------- | ---------------------------------------------- |
| Next.js Static Export | ✅ Tier-1            | Идеален для GH Pages, мгновенная загрузка      |
| Vite                  | ❌ Не нужен          | Next.js имеет свой bundler (Turbopack/Webpack) |
| Tailwind + shadcn/ui  | ✅ Лучший выбор      | Enterprise design system, a11y из коробки      |
| CodeRabbit AI         | ✅ Отличная практика | Имитирует командный ревью для соло-разработки  |
| Риск: Images          | ⚠️ Решено            | `unoptimized: true` в next.config.ts           |
| Риск: `_next` folder  | ⚠️ Решено            | `public/.nojekyll`                             |
| Риск: i18n в export   | ⚠️ Решено            | Паттерн `[lang]` + `generateStaticParams`      |

---

# Связанные концепты

- [Phase 0](/phases/phase-0-planning-devops.md) — инфраструктура
- [Phase 1](/phases/phase-1-project-setup.md) — фундамент
- [Phase 2](/phases/phase-2-design-system.md) — дизайн-система
- [Git Flow](/process/git-flow.md) — процесс разработки
- [Multilingual Strategy](/process/multilingual-strategy.md) — i18n детали
- [Testing Strategy](/process/testing-strategy.md) — QA подход

[^plan-note]: Составляем план задач (raw notes)
