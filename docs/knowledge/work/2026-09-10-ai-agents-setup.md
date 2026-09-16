---
type: Work Note
title: Set up Next.js AI coding agents integration
description: Integrated Next.js bundled-docs guidance with the project by adding the nextjs-agent-rules block to AGENTS.md, enabling browserToTerminal logging, and wiring the next-devtools MCP server into the opencode-only config
tags:
    - nextjs
    - ai-agents
    - mcp
    - opencode
    - documentation
status: stable
generated:
    by: opencode/okf-note
    at: '2026-09-09T21:19:24Z'
sources:
    - id: ai-agents-guide
      resource: node_modules/next/dist/docs/01-app/02-guides/ai-agents.md
      title: Next.js guide "How to set up your Next.js project for AI coding agents"
    - id: mcp-guide
      resource: node_modules/next/dist/docs/01-app/02-guides/mcp.md
      title: Next.js guide "Enabling Next.js MCP Server for Coding Agents"
    - id: agents-md
      resource: /AGENTS.md
      title: AGENTS.md (managed block added)
    - id: next-config
      resource: /next.config.ts
      title: next.config.ts (logging.browserToTerminal enabled)
    - id: opencode-config
      resource: /.opencode/opencode.json
      title: opencode project config (next-devtools MCP added)
---

# Обзор

В сессии к проекту подключена интеграция Next.js для AI-агентов по официальному гайду. Проект стоит на Next.js **16.2.6**, поэтому применён путь «Existing projects» из бандлованной документации: блок `<!-- BEGIN:nextjs-agent-rules -->` добавлен вручную, так как авто-генерация `AGENTS.md` появляется только в 16.3.[^ai-agents-guide]

# Шаги

## 1. AGENTS.md — managed-блок `nextjs-agent-rules`

В начало `AGENTS.md` вставлен каноничный блок с директивой читать документацию из `node_modules/next/dist/docs/` перед любой работой с Next.js. Маркеры `BEGIN`/`END` сохранены по конвенции гайда, чтобы при апгрейде на 16.3+ блок обновлялся автоматически, а существующие проектные инструкции (шadbcn/ui, Tailwind v4, static export) остались нетронутыми под блоком.[^ai-agents-guide][^agents-md]

## 2. next.config.ts — logging.browserToTerminal

Включён `logging: { browserToTerminal: true }`: `next dev` теперь форвардит ошибки и предупреждения браузерной консоли в терминал, который читает агент. Ключ подтверждён в `config-shared.d.ts` для v16.2.6; на статический экспорт (output: 'export') не влияет.[^next-config]

## 3. .opencode/opencode.json — MCP-сервер next-devtools

Создан проектный конфиг opencode с локальным MCP-сервером `next-devtools` (`next-devtools-mcp@latest`). Пакет — рекомендованный способ подключения из бандлованного `mcp.md`: он сам обнаруживает запущенный `next dev` через встроенный эндпоинт `/_next/mcp` и открывает инструменты `get_errors`, `get_routes`, `get_logs` и др.[^mcp-guide]

> По требованию пользователя интеграция подключена **только для opencode**: конфиг размещён в `.opencode/opencode.json`, а не в корневом `.mcp.json`, который прочитали бы все агенты (Cursor, Copilot, Claude Code).[^opencode-config]

## 4. Проверка

- JSON `.opencode/opencode.json` валидирован.
- `npm run lint` — чисто.
- `npm run build` (typecheck-гейт) — успешно, страницы сгенерированы статически.

# Дальнейшие шаги (вручную)

1. Перезапустить opencode — конфиг MCP читается только при старте.
2. Запустить `npm run dev`, чтобы `next-devtools-mcp` обнаружил инстанс.

[^ai-agents-guide]: Next.js guide "How to set up your Next.js project for AI coding agents"

[^mcp-guide]: Next.js guide "Enabling Next.js MCP Server for Coding Agents"

[^agents-md]: AGENTS.md — фильтрация проекта (изменена)

[^next-config]: next.config.ts — конфигурация проекта (изменена)

[^opencode-config]: .opencode/opencode.json — конфигурация opencode (создан)
