---
type: Decision
title: Multilingual Strategy (i18n)
description: 'Подход к интернационализации в Next.js Static Export: динамические роуты [lang], словари, generateStaticParams'
tags:
    - i18n
    - nextjs
    - static-export
    - multilingual
    - dictionaries
status: stable
generated:
    by: opencode/okf-note
    at: '2026-08-27T10:00:00Z'
verified:
    by: human:sogohlopec
    at: '2026-08-27T10:00:00Z'
sources:
    - id: multilingual-note
      resource: /docs/notes/Мультиязычность.md
      title: Мультиязычность (raw notes)
      author: human:sogohlopec
      last_modified: '2026-03-25'
    - id: arch-note
      resource: /docs/notes/Архитектура - PORTFOLIO.md
      title: Архитектура - PORTFOLIO (раздел i18n)
      author: human:sogohlopec
      last_modified: '2026-03-17'
    - id: plan-note
      resource: /docs/notes/Составляем план задач.md
      title: Составляем план задач (раздел i18n Strategy)
      author: human:sogohlopec
      last_modified: '2026-03-31'
---

# Обзор

Стратегия мультиязычности для Next.js в режиме `output: 'export'` (GitHub Pages). Встроенный i18n Next.js **не работает** в статическом экспорте. Используется паттерн с динамическим сегментом `[lang]` + словари JSON + `generateStaticParams`.

---

# Архитектура маршрутизации

```
src/app/[lang]/
├── layout.tsx       # generateStaticParams → ['en','ru','be']
├── page.tsx         # главная
├── projects/
│   ├── page.tsx
│   └── [slug]/page.tsx
└── roadmap/page.tsx
```

При `npm run build` Next.js вызывает `generateStaticParams` и создаёт **физические папки**:

```
out/
├── en/
│   ├── index.html
│   ├── projects/
│   └── roadmap/
├── ru/
│   ├── index.html
│   ...
└── be/
    ...
```

GitHub Pages отдаёт статику по URL: `https://user.github.io/portfolio/en/`, `/ru/`, `/be/`.

---

# generateStaticParams

В `src/app/[lang]/layout.tsx`:

```typescript
export async function generateStaticParams() {
    return [{ lang: 'en' }, { lang: 'ru' }, { lang: 'be' }];
}
```

Это единственное место, где перечисляются поддерживаемые языки. Добавление языка = добавление записи в массив + перевод словарей/контента.

---

# Система контента: три слоя

| Слой              | Путь                                    | Что хранит                                             | Перевод нужен?                                        |
| ----------------- | --------------------------------------- | ------------------------------------------------------ | ----------------------------------------------------- |
| **Dictionaries**  | `src/dictionaries/{en,ru,be}.json`      | UI текст: кнопки, навигация, заголовки, aria-labels    | **Да**, 1-к-1                                         |
| **Data (TS)**     | `src/data/*.ts`                         | Плоские структуры: skills, experience, socials, config | **Нет** (названия технологий, даты, ссылки одинаковы) |
| **Content (MDX)** | `src/content/{en,ru,be}/projects/*.mdx` | Полноценные статьи-кейсы проектов                      | **Да**, полный перевод                                |

**Почему разделение**:

- Дублировать `React`, `Node.js`, `TypeScript` в трёх словарях — бред. Хранятся в `data/skills.ts` один раз.
- Кнопки "Связаться", "Мои проекты" — переводится 1-к-1 → словари JSON.
- Кейсы проектов — сложный markdown с компонентами внутри → MDX по папкам языков.

---

# Утилита загрузки словаря

`src/lib/i18n.ts`:

```typescript
const dictionaries = {
    en: () => import('@/dictionaries/en.json').then((m) => m.default),
    ru: () => import('@/dictionaries/ru.json').then((m) => m.default),
    be: () => import('@/dictionaries/be.json').then((m) => m.default),
};

export const getDictionary = async (lang: string) => {
    const load = dictionaries[lang as keyof typeof dictionaries] || dictionaries.en;
    return load();
};
```

В Server Component (layout/page): `const dict = await getDictionary(lang);` — типизированный доступ `dict.hero.title`.

---

# Переключатель языка (LanguageSwitcher)

Компонент `src/components/layout/lang-switcher.tsx`:

- Клиентский (`"use client"`)
- Использует `usePathname`, `useRouter` из `next/navigation`
- Меню: флаги/названия языков → клик → `router.push(newPath)` + `router.refresh()`
- Сохраняет выбор в `localStorage` (опционально) для восстановления при следующем визите.

---

# Решение о белорусском языке (BE)

**Прагматичный анализ**:

- HR и техлиды (даже в РБ) смотрят резюме на EN или RU.
- BE не принесёт дополнительных офферов.

**Инженерный анализ**:

- Архитектура `[lang]` + `generateStaticParams` **уже поддерживает N языков** без изменений.
- Добавление 3-го языка = 0 архитектурной сложности.

**Решение**:

- **Release 1.0**: только EN + RU (не тормозить запуск переводом).
- **Phase 2 (отдельный PR)**: добавить BE — просто создать `be.json`, перевести MDX, добавить `'be'` в `generateStaticParams`. Это красивая "пасхалка" и демонстрация уважения к родному языку.

---

# Библиотеки: почему НЕ next-intl

`next-intl` — отличная либа, но для данного проекта избыточна:

- Добавляет килобайты в бандл (наш сайт — статический, каждый KB на счету).
- Наш паттерн с `generateStaticParams` + словари JSON + динамический импорт — **нативный, 0 депенденси, полный контроль**.
- TypeScript автодополнение работает "из коробки" с JSON импортами.

Если проект вырастет до десятков языков и сложных plural rules — тогда `next-intl` или `lingui`. Пока — KISS.

---

# SEO и метаданные

В `[lang]/layout.tsx` и страницах:

```typescript
export async function generateMetadata({ params }: { params: { lang: string } }) {
    const dict = await getDictionary(params.lang);
    return {
        title: dict.metadata.title,
        description: dict.metadata.description,
        openGraph: { locale: params.lang, alternateLocales: ['en', 'ru', 'be'] },
    };
}
```

`alternateLocales` в OpenGraph — подсказка поисковикам о переводах.

---

# Чек-лист добавления нового языка

1. [ ] Создать `src/dictionaries/{lang}.json` (копия en/ru + перевод).
2. [ ] Перевести MDX контент в `src/content/{lang}/projects/`.
3. [ ] Добавить `{ lang: 'xx' }` в `generateStaticParams`.
4. [ ] Добавить язык в `LanguageSwitcher`.
5. [ ] Добавить `alternateLocales` в metadata.
6. [ ] Проверить билд: `npm run build` → `out/xx/` существует.
7. [ ] PR → CodeRabbit → Merge.

---

# Связанные концепты

- [Portfolio Architecture](/architecture/portfolio-architecture.md) — общая архитектура с i18n
- [Phase 3 i18n Core](/phases/phase-3-i18n-core.md) — план реализации (планируется)

[^multilingual-note]: Мультиязычность (raw notes)

[^arch-note]: Архитектура - PORTFOLIO (раздел i18n)

[^plan-note]: Составляем план задач (раздел i18n Strategy)
