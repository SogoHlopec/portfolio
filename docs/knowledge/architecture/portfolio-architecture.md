---
type: Architecture
title: Portfolio Architecture
description: Static export Next.js portfolio architecture with SSG, i18n routing, and design system
tags:
    - architecture
    - nextjs
    - static-export
    - shadcn-ui
    - i18n
status: stable
generated:
    by: opencode/okf-note
    at: '2026-08-27T10:00:00Z'
verified:
    by: human:sogohlopec
    at: '2026-08-27T10:00:00Z'
sources:
    - id: arch-note
      resource: /docs/notes/Архитектура - PORTFOLIO.md
      title: Архитектура - PORTFOLIO (raw notes)
      author: human:sogohlopec
      last_modified: '2026-03-17'
---

# Обзор

Архитектура портфолио v2 построена на **Next.js 16 (App Router)** в режиме **Static Export** для деплоя на GitHub Pages. Приложение полностью статическое — никаких API-роутов, SSR или динамического рендеринга на сервере. Все страницы генерируются на этапе сборки (`npm run build`) в папку `out/`.

---

# Стек технологий

| Слой        | Технология             | Версия / Детали                                      |
| ----------- | ---------------------- | ---------------------------------------------------- |
| Фреймворк   | Next.js                | 16.x (App Router, React Server Components)           |
| Язык        | TypeScript             | 5.9                                                  |
| Стили       | Tailwind CSS           | v4 (CSS-first, нет `tailwind.config.ts`)             |
| UI-кит      | shadcn/ui              | style `radix-nova`, компоненты в `src/components/ui` |
| Темизация   | next-themes            | класс `.dark` на `<html>`                            |
| Шрифты      | next/font/google       | Inter + JetBrains Mono (подмножество `cyrillic`)     |
| Анимации    | View Transitions API   | через `next-view-transitions`                        |
| Деплой      | GitHub Pages + Actions | `output: 'export'`, `basePath: '/portfolio'` в проде |
| CI/CD       | GitHub Actions         | билд + деплой артефакта `out/`                       |
| Code Review | CodeRabbit AI          | настраивается через `.github/coderabbit.yaml`        |

---

# Стратегия статического экспорта

В `next.config.ts` включены ключевые настройки для GitHub Pages:

```typescript
output: 'export'; // чистый HTML/CSS/JS
images: {
    unoptimized: true;
} // нет Node-сервера для оптимизации
trailingSlash: true; // /about/ → /about/index.html (фикс роутинга GH Pages)
basePath: isProd ? '/portfolio' : ''; // префикс только в проде
assetPrefix: isProd ? '/portfolio/' : '';
```

Файл `.nojekyll` в `public/` отключает обработку Jekyll (иначе папка `_next` игнорируется).

---

# i18n — маршрутизация через `[lang]`

Встроенный i18n Next.js **не работает** в `output: 'export'`. Используется динамический сегмент:

```
src/app/[lang]/
├── layout.tsx       // generateStaticParams → ['en','ru','be']
├── page.tsx         // главная
├── projects/
│   ├── page.tsx     // список
│   └── [slug]/page.tsx  // детальная (MDX)
└── roadmap/page.tsx
```

`generateStaticParams` в `[lang]/layout.tsx` возвращает массив языков — Next.js при билде создаёт физические папки `out/en/`, `out/ru/`, `out/be/`.

Словари интерфейса: `src/dictionaries/{en,ru,be}.json` (кнопки, навигация, заголовки).
Сложный контент (кейсы): `src/content/{en,ru,be}/projects/*.mdx`.
Плоские данные: `src/data/*.ts` (skills, experience, socials — не требуют перевода).

---

# Дизайн-система (Tailwind v4 + shadcn/ui)

- **Токены**: CSS-переменные в `src/app/globals.css` (`:root` / `.dark` + `@theme inline`).
- Ключевые кастомные токены: `--radius`, `--card-shadow*`, `--hero-max-width`, `--section-gap`.
- shadcn/ui компоненты добавляются через `npx shadcn@latest add <name>` → попадают в `src/components/ui/`.
- Иконки: `lucide-react`.
- Путь алиас: `@/*` → `src/*`.

---

# Темная тема

- Библиотека `next-themes` + клиентский `ThemeProvider` (`src/components/providers/theme-provider.tsx`).
- В `RootLayout` (`src/app/layout.tsx`): `suppressHydrationWarning` на `<html>`, `attribute="class"`, `defaultTheme="system"`, `disableTransitionOnChange`.
- Переключатель: `ThemeToggle` (`src/components/theme-toggle.tsx`) — использует `useSyncExternalStore` (React 19) для избежания каскадных рендеров.

---

# Анимации страниц

Пакет `next-view-transitions` оборачивает приложение в `<ViewTransitions>` в `[lang]/layout.tsx`. Навигация использует `<Link>` из того же пакета — даёт нативные плавные переходы (0 KB JS, на уровне браузера).

---

# Структура папок (актуальная)

```text
portfolio/
├── .github/
│   ├── workflows/deploy.yml
│   ├── PULL_REQUEST_TEMPLATE.md
│   └── coderabbit.yaml
├── public/
│   ├── images/, icons/, favicon.ico, .nojekyll
├── src/
│   ├── app/
│   │   ├── [lang]/           # i18n routing
│   │   │   ├── layout.tsx    # ThemeProvider, ViewTransitions, Header, Footer
│   │   │   ├── page.tsx      # Hero, Experience, Skills, Top Projects
│   │   │   ├── projects/
│   │   │   │   ├── page.tsx
│   │   │   │   └── [slug]/page.tsx
│   │   │   └── roadmap/page.tsx
│   │   ├── globals.css       # CSS variables, @theme
│   │   ├── not-found.tsx
│   │   └── layout.tsx        # базовый root layout
│   ├── components/
│   │   ├── ui/               # shadcn components (Button, Card, Badge, etc.)
│   │   ├── layout/           # Header, Footer, ThemeToggle, LangSwitcher
│   │   ├── sections/         # HeroSection, ExperienceTimeline, TopProjects
│   │   ├── mdx/              # CustomLink, CodeBlock для MDX
│   │   └── providers/        # ThemeProvider
│   ├── content/              # MDX по языкам
│   │   ├── en/projects/*.mdx
│   │   ├── ru/projects/*.mdx
│   │   └── be/projects/*.mdx
│   ├── data/                 # TypeScript arrays
│   │   ├── config.ts, socials.ts, skills.ts, experience.ts
│   ├── dictionaries/         # JSON словари UI
│   │   ├── en.json, ru.json, be.json
│   └── lib/                  # utils.ts, i18n.ts, mdx.ts
├── .eslintrc.json
├── components.json           # shadcn config
├── next.config.ts
├── package.json
└── tsconfig.json
```

---

# Git Flow

Ветки: `main` (production-ready), `feature/*`, `fix/*`, `chore/*`, `content/*`, `refactor/*`.
Коммиты: Conventional Commits (`feat(scope):`, `fix(scope):`, `chore(scope):`, `docs:`).
Все изменения — только через PR с CodeRabbit AI review.

---

# Решение о белорусском языке

Архитектура заложена под 3 языка (`generateStaticParams` возвращает 3 записи), но для релиза 1.0 делаются только EN и RU. BE добавится отдельным PR в Phase 2. Это не усложняет архитектуру, но экономит время на переводе.

[^arch-note]: Архитектура - PORTFOLIO (raw notes)
