# Portfolio Project Roadmap & Task Board

> **Instructions for Agent**:
>
> - When starting a session, read this file to understand the current stage..
> - Don't delete completed tasks, they form the project history.

---

## Phase 0 Planning & DevOps Setup

- [x] chore(repo) init github repository and setup branch protection rules
- [x] chore(ci): install CodeRabbit AI app to the repository
- [x] create Next.js app with App Router, TypeScript, and Tailwind

---

## Phase 1: Project Setup

- [x] chore(config): configure next.config.ts for static export and unoptimized images
- [x] chore(tools): setup ESLint, Prettier, Husky, and lint-staged
- [x] chore(ui): initialize shadcn/ui CLI and configure base styles

---

## Phase 2: Design System & Theming

- [x] feat(theme): add next-themes and create ThemeProvider wrapper
- [x] feat(ui): implement ThemeToggle component
- [x] chore(styles): define CSS variables in globals.css for light/dark modes based on shadcn tokens
- [x] feat(layout): create base RootLayout with custom fonts (next/font)
- [ ] Creating AGENTS.md agent rules and TODO.md task structure. Creating a project documentation database.

---

## Phase 3: Internationalization (i18n) Core

- [ ] feat(i18n): restructure app directory to use [lang] dynamic route
- [ ] feat(i18n): implement generateStaticParams to pre-build en and ru routes
- [ ] feat(i18n): create dictionaries (en.json, ru.json) and fetcher utility
- [ ] feat(ui): create LanguageSwitcher component

---

## Phase 4: UI Implementation

- [ ] feat(ui): add shadcn components (Button, Badge, Card, Sheet for mobile menu)
- [ ] feat(layout): build responsive Header with navigation and sticky scroll
- [ ] feat(layout): build Footer with social links
- [ ] feat(home): build Hero section with CTA buttons
- [ ] feat(home): build Experience timeline component

---

## Phase 4.5: Quality Assurance Setup

- [ ] chore(test): install Vitest and React Testing Library, configure setup files
- [ ] feat(test): write unit tests for date formatting and data fetching utilities
- [ ] feat(test): write integration tests for ThemeToggle and LanguageSwitcher components
- [ ] chore(test): install Playwright and write basic E2E navigation test

---

## Phase 5: Content System (TS + MDX)

- [ ] feat(content): create TypeScript data files for Skills, Social Links, and Experience
- [ ] feat(mdx): install next-mdx-remote and setup parsing logic for local .mdx files
- [ ] feat(projects): create ProjectCard component and map top projects on Home page
- [ ] feat(projects): implement individual project pages (/projects/[slug]) using MDX

---

## Phase 6: Animations & Polish

- [ ] feat(anim): setup View Transitions API for seamless page routing
- [ ] feat(anim): create Framer Motion <FadeIn> wrapper for scroll reveals
- [ ] refactor(ui): apply <FadeIn> to sections on the Home page
- [ ] refactor(ui): add Tailwind transitions to interactive elements (hover states)

---

## Phase 7: SEO & Performance

- [ ] feat(seo): implement dynamic metadata generation for Next.js App Router
- [ ] feat(seo): setup OpenGraph images and Twitter cards
- [ ] feat(seo): generate static sitemap.xml and robots.txt
- [ ] chore(perf): audit and optimize image sizes, ensure lazy loading is active

---

## Phase 8: CI/CD & Deployment

- [ ] chore(ci): create github-actions.yml for Next.js GitHub Pages deployment
- [ ] chore(config): add .nojekyll file generation step
- [ ] fix(deploy): test pipeline, fix any basePath or static asset path issues

---
