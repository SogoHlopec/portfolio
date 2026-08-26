---
type: Phase Notes
title: Phase 2 — Design System & Theming
description: next-themes провайдер, ThemeToggle кнопка, CSS переменные для light/dark, шрифты next/font
tags:
    - phase-2
    - design-system
    - theming
    - next-themes
    - tailwind
    - shadcn-ui
    - css-variables
status: stable
generated:
    by: opencode/okf-note
    at: '2026-08-27T10:00:00Z'
verified:
    by: human:sogohlopec
    at: '2026-08-27T10:00:00Z'
sources:
    - id: phase2-theme-provider
      resource: /docs/notes/Реализация Phase 2 Design System & Theming feat(theme) add next-themes and create ThemeProvider wrapper.md
      title: ThemeProvider с next-themes (raw notes)
      author: human:sogohlopec
      last_modified: '2026-06-23'
    - id: phase2-toggle
      resource: /home/projects/portfolio/docs/notes/Реализация Phase 2 Design System & Theming feat(ui) implement ThemeToggle component (кнопка переключения)..md
      title: ThemeToggle компонент (raw notes)
      author: human:sogohlopec
      last_modified: '2026-06-24'
    - id: phase2-css
      resource: /home/projects/portfolio/docs/notes/Реализация Phase 2 - Design System & Theming chore(styles) - define CSS variables in globals.css for light-dark modes based on shadcn tokens..md
      title: CSS переменные для тем (raw notes)
      author: human:sogohlopec
      last_modified: '2026-07-15'
    - id: phase2-fonts
      resource: /home/projects/portfolio/docs/notes/Реализация Phase 2 Design System & Theming feat(layout) create base RootLayout with custom fonts (next/font).md
      title: Кастомные шрифты next/font (raw notes)
      author: human:sogohlopec
      last_modified: '2026-06-23'
---

# Обзор

Phase 2 реализовала полноценную дизайн-систему с поддержкой светлой/тёмной темы, красивой анимацией переключения, кастомными шрифтами и семантическими CSS-токенами на базе shadcn/ui.

---

# Задачи и выполнение

## 1. `feat(theme): add next-themes and create ThemeProvider wrapper`

**Цель**: Настроить переключение тем без FOUC (Flash of Unstyled Content) и ошибок гидратации.

### Проблемы, которые решает next-themes:

1. **FOUC**: На SSG сервер отдаёт светлый HTML. Если у пользователя тёмная тема в ОС — браузер покажет белую вспышку до загрузки JS. `next-themes` вставляет инлайн-скрипт в `<head>`, который **до отрисовки первого пикселя** проверяет `localStorage` и `matchMedia` и вешает нужный класс на `<html>`.
2. **Server vs Client Components**: Next.js App Router по умолчанию рендерит Server Components. Контекст темы — клиентская фича (хуки, `localStorage`). Нельзя импортировать `ThemeProvider` прямо в серверный `layout.tsx`.

### Реализация:

**Установка**: `npm install next-themes`

**`src/components/providers/theme-provider.tsx`** — клиентская обёртка:

```tsx
'use client';
import * as React from 'react';
import { ThemeProvider as NextThemesProvider } from 'next-themes';

export function ThemeProvider({
    children,
    ...props
}: React.ComponentProps<typeof NextThemesProvider>) {
    return <NextThemesProvider {...props}>{children}</NextThemesProvider>;
}
```

**Интеграция в `src/app/layout.tsx`**:

```tsx
import { ThemeProvider } from '@/components/providers/theme-provider';

export default function RootLayout({ children }) {
    return (
        <html lang="en" suppressHydrationWarning>
            <body className={`${geistSans.variable} ${geistMono.variable} antialiased`}>
                <ThemeProvider
                    attribute="class" // класс .dark на <html>
                    defaultTheme="system" // по умолчанию — тема ОС
                    enableSystem // реагировать на смену темы в ОС
                    disableTransitionOnChange // без CSS-аяний при загрузке
                >
                    {children}
                </ThemeProvider>
            </body>
        </html>
    );
}
```

**Ключевые пропсы**:

- `suppressHydrationWarning` на `<html>` — отключает предупреждение React о несовпадении серверного/клиентского HTML (next-themes меняет класс динамически).
- `disableTransitionOnChange` — предотвращает дерганье элементов при первой смене темы.

---

## 2. `feat(ui): implement ThemeToggle component`

**Цель**: Кнопка переключения темы (Light/Dark) с плавной анимацией и без ошибок гидратации.

### Эволюция компонента:

**Версия 1 (Dropdown Menu)**: `npx shadcn@latest add dropdown-menu` → меню с тремя пунктами (Light, Dark, System). Работало, но 2 клика для переключения.

**Версия 2 (Simple Toggle — выбранная)**: Простая кнопка — 1 клик переключает Light ↔ Dark. Использует `resolvedTheme` (фактическая тема на экране), а не `theme` (которое может быть "system").

### Проблема гидратации и решение:

**Паттерн Two-Pass Rendering (устаревший)**:

```tsx
const [mounted, setMounted] = useState(false);
useEffect(() => setMounted(true), []);
if (!mounted) return <Button disabled />; // скелетон
```

Проблема: `setMounted(true)` в `useEffect` вызывает **каскадный рендер** (второй проход после paint) → предупреждение React 19: "Calling setState synchronously within an effect can trigger cascading renders".

**Современное решение (React 19)**: `useSyncExternalStore`

```tsx
const emptySubscribe = () => () => {};
function useIsMounted() {
    return React.useSyncExternalStore(
        emptySubscribe,
        () => true, // клиент
        () => false // сервер (SSG)
    );
}
const isMounted = useIsMounted();
if (!isMounted) return <Button disabled />;
```

Результат: **ноль каскадных рендеров**, сервер рендерит скелетон, клиент бесшовно подставляет реальную кнопку в один проход.

### Финальный `ThemeToggle`:

```tsx
'use client';
import * as React from 'react';
import { Moon, Sun } from 'lucide-react';
import { useTheme } from 'next-themes';
import { Button } from '@/components/ui/button';

const emptySubscribe = () => () => {};
function useIsMounted() {
    return React.useSyncExternalStore(
        emptySubscribe,
        () => true,
        () => false
    );
}

export function ThemeToggle() {
    const { setTheme, resolvedTheme } = useTheme();
    const isMounted = useIsMounted();
    if (!isMounted) return <Button variant="outline" size="icon" className="w-9 h-9" disabled />;

    const toggleTheme = () => setTheme(resolvedTheme === 'dark' ? 'light' : 'dark');

    return (
        <Button variant="outline" size="icon" onClick={toggleTheme} className="relative">
            <Sun className="h-[1.2rem] w-[1.2rem] rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
            <Moon className="absolute h-[1.2rem] w-[1.2rem] rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
            <span className="sr-only">Toggle theme</span>
        </Button>
    );
}
```

**Анимация**: Иконки наложены (`absolute`), Tailwind `dark:` варианты плавно вращают/масштабируют через `transition-all`. 0 KB JS, чистый CSS.

**Очистка**: Удалён неиспользуемый `src/components/ui/dropdown-menu.tsx` (мертвый код).

---

## 3. `chore(styles): define CSS variables in globals.css for light/dark modes based on shadcn tokens`

**Цель**: Кастомизировать дефолтные shadcn токены под дизайн портфолио (мягкая палитра Zinc/Slate, без чистого чёрного).

### Принцип семантических токенов:

Вместо `--blue` → `--primary` (роль цвета). В light/dark теме одно и то же имя — разные значения.

### Ключевые пары:

- `--background` / `--foreground` — базовые фон/текст
- `--card` / `--card-foreground` — карточки проектов
- `--popover` / `--popover-foreground` — dropdown меню
- `--primary` / `--primary-foreground` — CTA кнопки (foreground = текст внутри)
- `--muted` / `--muted-foreground` — второстепенный текст (даты, описания)
- `--border` — разделители
- `--radius` — скругления (0.5rem)

### HSL без скобок — трюк для Tailwind opacity:

`--primary: 240 5.9% 10%;` (не `hsl(...)`). Позволяет `bg-primary/50` → `hsl(var(--primary) / 0.5)`.

### Финальный `globals.css` (фрагмент):

```css
@import 'tailwindcss';
@plugin "tailwindcss-animate";
@custom-variant dark (&:is(.dark *));

:root {
    --background: 0 0% 100%;
    --foreground: 240 10% 3.9%;
    --card: 0 0% 100%;
    --card-foreground: 240 10% 3.9%;
    --popover: 0 0% 100%;
    --popover-foreground: 240 10% 3.9%;
    --primary: 240 5.9% 10%;
    --primary-foreground: 0 0% 98%;
    --secondary: 240 4.8% 95.9%;
    --secondary-foreground: 240 5.9% 10%;
    --muted: 240 4.8% 95.9%;
    --muted-foreground: 240 3.8% 46.1%;
    --accent: 240 4.8% 95.9%;
    --accent-foreground: 240 5.9% 10%;
    --destructive: 0 84.2% 60.2%;
    --destructive-foreground: 0 0% 98%;
    --border: 240 5.9% 90%;
    --input: 240 5.9% 90%;
    --ring: 240 5.9% 10%;
    --radius: 0.5rem;
}

.dark {
    --background: 240 10% 3.9%; /* Глубокий графит, не #000 — нет смазывания на OLED */
    --foreground: 0 0% 98%;
    --card: 240 10% 3.9%;
    --card-foreground: 0 0% 98%;
    --popover: 240 10% 3.9%;
    --popover-foreground: 0 0% 98%;
    --primary: 0 0% 98%; /* Светлые кнопки в тёмной теме */
    --primary-foreground: 240 5.9% 10%;
    --secondary: 240 3.7% 15.9%;
    --secondary-foreground: 0 0% 98%;
    --muted: 240 3.7% 15.9%;
    --muted-foreground: 240 5% 64.9%;
    --accent: 240 3.7% 15.9%;
    --accent-foreground: 0 0% 98%;
    --destructive: 0 62.8% 30.6%;
    --destructive-foreground: 0 0% 98%;
    --border: 240 3.7% 15.9%;
    --input: 240 3.7% 15.9%;
    --ring: 240 4.9% 83.9%;
}

@theme {
    --color-background: hsl(var(--background));
    --color-foreground: hsl(var(--foreground));
    /* ... все остальные --color-* маппинги ... */
}

body {
    background-color: var(--color-background);
    color: var(--color-foreground);
    font-feature-settings:
        'rlig' 1,
        'calt' 1; /* лигатуры */
}
```

---

## 4. `feat(layout): create base RootLayout with custom fonts (next/font)`

**Цель**: Подключить Inter (sans) и JetBrains Mono (mono) с поддержкой кириллицы.

**`src/app/layout.tsx`**:

```tsx
import { Geist, Geist_Mono } from "next/font/google"

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin", "cyrillic"],  // ВАЖНО: cyrillic для RU/BE контента
})
const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin", "cyrillic"],
})

export default function RootLayout({ children }) {
  return (
    <html lang="en" suppressHydrationWarning>
      <body className={`${geistSans.variable} ${geistMono.variable} antialiased`}>
        <ThemeProvider ...>{children}</ThemeProvider>
      </body>
    </html>
  )
}
```

**В `globals.css` (в `@theme`)**:

```css
@theme {
    --font-sans: var(--font-geist-sans);
    --font-mono: var(--font-geist-mono);
}
```

Теперь `font-sans` и `font-mono` классы Tailwind используют эти шрифты. `next/font` сам оптимизирует загрузку (self-host, preload, zero layout shift).

---

# Ключевые решения

| Решение                                               | Обоснование                                                     |
| ----------------------------------------------------- | --------------------------------------------------------------- |
| `next-themes` + инлайн скрипт в `<head>`              | Нет FOUC, тема применяется до первого paint                     |
| `ThemeProvider` как Client Component                  | App Router требует `"use client"` для Context/hukов             |
| `useSyncExternalStore` вместо `useEffect+useState`    | React 19 стандарт, нет каскадных рендеров, идеальная гидратация |
| `resolvedTheme` а не `theme`                          | Правильно работает при `defaultTheme="system"`                  |
| Простая кнопка Toggle вместо Dropdown                 | UX: 1 клик вместо 2, меньше кода, меньше бандла                 |
| Глубокий графит (`240 10% 3.9%`) вместо `#000` в dark | Нет смазывания текста на OLED, мягче для глаз                   |
| HSL без скобок в CSS переменных                       | Работает `bg-primary/50` и другие opacity утилиты Tailwind      |
| `subsets: ["cyrillic"]` в next/font                   | Контент частично на русском — глифы должны загружаться          |
| `font-feature-settings: "rlig" 1, "calt" 1`           | Включает лигатуры и контекстные альтернативы шрифта             |

---

# Связанные концепты

- [Portfolio Architecture](/architecture/portfolio-architecture.md) — где это используется
- [Phase 3 i18n](/phases/phase-3-i18n-core.md) — следующая фаза (планируется)

[^phase2-theme-provider]: ThemeProvider с next-themes (raw notes)

[^phase2-toggle]: ThemeToggle компонент (raw notes)

[^phase2-css]: CSS переменные для тем (raw notes)

[^phase2-fonts]: Кастомные шрифты next/font (raw notes)
