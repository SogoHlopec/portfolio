<!-- BEGIN:nextjs-agent-rules -->

# Next.js: ALWAYS read docs before coding

Before any Next.js work, find and read the relevant doc in `node_modules/next/dist/docs/`. Your training data is outdated — the docs are the source of truth.

<!-- END:nextjs-agent-rules -->

# AGENTS.md

Next.js 16 (App Router) + React 19 + TypeScript 5.9 + Tailwind CSS v4 portfolio site (V2). Static export deployed to GitHub Pages. Old version lives on the `portfolio_old` branch. Site content mixes English and Russian.

## Commands

- `npm run dev` — dev server (runs at site root, no base path)
- `npm run build` — production build; **this is the typecheck gate** (no separate `tsc` script). Output goes to `out/`.
- `npm run lint` — ESLint (includes prettier rules)
- `npm run start` — serve production build
- No test suite.

## Formatting & hooks

- Prettier config deviates from defaults: **4-space indent, single quotes, semicolons, printWidth 100**. Match it manually.
- Husky pre-commit runs lint-staged (`eslint --fix` + `prettier --write`) on staged files, so push clean formatting or commits get rewritten.

## Toolchain quirks

- **Tailwind v4 has no config file.** All theme tokens are CSS variables in `src/app/globals.css` (`:root` / `.dark` plus `@theme inline`). Dark mode is the `.dark` class on `<html>` via next-themes. Custom tokens to know: `--radius`, `--card-shadow*`, `--hero-max-width`, `--section-gap`.
- **shadcn/ui** (style `radix-nova`): components live in `src/components/ui`; add new ones with `npx shadcn@latest add <name>`. Icons from `lucide-react`.
- Path alias `@/*` → `src/*`.
- React Compiler is enabled (`reactCompiler: true` in next.config.ts).
- `next/font/google`: Inter + JetBrains Mono, both with the **`cyrillic` subset** (content is partly Russian — keep it when touching fonts).
- Static export constraints in `next.config.ts`: `output: 'export'`, `basePath`/`assetPrefix` `/portfolio` only in production, `trailingSlash: true`, `images.unoptimized: true`. No API routes or dynamic SSR — every page must be statically exportable. Production links resolve under `/portfolio/`, dev links at root.

## Conventions

- Conventional Commits: `feat(scope):`, `fix(scope):`, `chore(scope):`, `docs:` (see git log).
- Feature work happens on `feat/*` or `chore/*` branches merged into `main` via PRs.
- No env files or secrets needed for this app.
