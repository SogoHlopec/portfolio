---
type: Work Note
title: 'Remove CodeRabbit, adopt ponytail agent review'
description: CodeRabbit AI полностью удалён (конфиг, GitHub App, документация); ревью PR теперь выполняет агент — ponytail (/ponytail-review) как плагин OpenCode; добавлен проектный скилл create-pr; всё собрано в PR #12
tags:
    - coderabbit
    - ponytail
    - code-review
    - opencode
    - ci
    - workflow
status: stable
generated:
    by: opencode/okf-note
    at: '2026-11-08T15:24:24Z'
sources:
    - id: pr-12
      resource: https://github.com/SogoHlopec/portfolio/pull/12
      title: 'PR #12 — chore(ci): remove CodeRabbit AI, adopt ponytail agent review'
    - id: commit-remove-config
      resource: https://github.com/SogoHlopec/portfolio/commit/6c09131de239e15799df749b378db2441b08df72
      title: 'chore(ci): remove CodeRabbit AI review config'
    - id: commit-ponytail-plugin
      resource: https://github.com/SogoHlopec/portfolio/commit/c72786dba06e85d79bb3dc2f787cceb66e20bf04
      title: 'chore(opencode): add ponytail agent plugin (pinned 5.3.3)'
    - id: commit-uninstall-fix
      resource: https://github.com/SogoHlopec/portfolio/commit/3a92b061b50c3de154ec335b2eaa2b6d839e73b6
      title: 'docs: correct CodeRabbit App uninstall status'
    - id: commit-create-pr
      resource: https://github.com/SogoHlopec/portfolio/commit/087afc3f2db7d7e1b6eb77289b995d1d052ac96b
      title: 'chore(opencode): add create-pr skill'
    - id: opencode-config
      resource: /.opencode/opencode.json
      title: 'opencode project config (plugins)'
    - id: create-pr-skill
      resource: /.opencode/skills/create-pr/SKILL.md
      title: 'create-pr skill (project skill)'
    - id: git-flow
      resource: /docs/knowledge/process/git-flow.md
      title: 'Git Flow & Conventional Commits (обновлённая секция Agent review)'
---

# Обзор

В сессии 2026-11-06…08 из проекта убран CodeRabbit AI и заменён на ревью агентом — ponytail. Работа шла в ветке `chore/remove-coderabbit`, результат — [PR #12](https://github.com/SogoHlopec/portfolio/pull/12) (`chore(ci): remove CodeRabbit AI, adopt ponytail agent review`); мерж выполняется владельцем вручную.[^pr-12]

Причина перехода: CodeRabbit как внешний бот отвечал с шумом и ошибался (разбор инцидентов — [Phase 1](/phases/phase-1-project-setup.md)), при этом требовал отдельного GitHub App и ручного разбора каждого комментария. ponytail ([repository](https://github.com/DietrichGebert/ponytail), MIT) — тот же класс ревью локально, прозрачно и под контролем владельца.

# Что сделано

## 1. Удаление CodeRabbit из репозитория

Удалён `.coderabbit.yaml` (единственный tracked-конфиг; `.github/coderabbit.yaml` из старых доков никогда не существовал). Branch protection на `main` проверена — CodeRabbit там не фигурировал, правки protection не потребовались.[^commit-remove-config]

Со стороны GitHub: ветка `coderabbitai/docstrings/f23bb86` на GitHub отсутствует (проверено `git ls-remote`), GitHub App CodeRabbit uninstalled владельцем вручную (CLI-токен не имеет прав на uninstall: `/user/installations` → 403); протухшие локальные remote-tracking ref'ы вычищены пользователем (`git remote prune origin`).[^commit-uninstall-fix]

## 2. ponytail как ревью-агент

В `.opencode/opencode.json` добавлен `"plugins": ["@dietrichgebert/ponytail@5.3.3"]` — точная версия запинана по конвенции проекта. Плагин даёт все команды (`/ponytail-review`, `/ponytail-audit`, `/ponytail-debt`, `/ponytail-gain`, `/ponytail-help`, `/ponytail`) и always-on ruleset («ленивый сеньор»), который инжектируется каждый ход и в субагентов; уровень по умолчанию — `full`, переключается `/ponytail lite|full|ultra|off`, машинный дефолт — `PONYTAIL_DEFAULT_MODE` / `~/.config/ponytail/config.json`. Плагин коммитится в репо и подтягивается автоматически при старте opencode.[^commit-ponytail-plugin][^opencode-config]

Dogfood: PR #12 прошёл через сам `/ponytail-review`. Нашли одно «Should fix» — доки утверждали, что uninstall App ещё предстоит (владелец уже выполнил); исправлено в коммите `docs: correct CodeRabbit App uninstall status`.[^commit-uninstall-fix]

## 3. Скилл `create-pr`

Новый проектный скилл `/create-pr` (`.opencode/skills/create-pr/SKILL.md`): PR открывается только из коммитов текущей ветки (base `main`), title и body — **на английском**, секции `Summary / Changes / Verification / Notes`, обязательные гейты (`npm run build`, OKF validate для `docs/knowledge`), создание только через `gh pr create`, затем `/ponytail-review` по полному диффу `main...HEAD`. Ключевое требование владельца зашито явно: **merge всегда делает только пользователь** — агент не выполняет `gh pr merge`, не включает auto-merge и не трогает branch protection.[^commit-create-pr][^create-pr-skill]

## 4. Документация и OKF-бандл

- `git-flow.md` — секция «Agent review (ponytail)», сценарий PR: `/ponytail-review` как гейт перед мержем; поправлены мёртвые ссылки (несуществовавшие `.github/coderabbit.yaml` и `.github/PULL_REQUEST_TEMPLATE.md`); указатель на `/create-pr`.[^git-flow]
- `AGENTS.md`, `architecture/portfolio-architecture.md`, `process/*`, `phases/index.md` — актуальный процесс ревью.
- `phases/phase-0-planning-devops.md` — статус `deprecated`, врезка «superseded 2026-11-06» (историю не переписывали).
- `log.md`, `TODO.md` — записи о переходе (задача закрыта; старые записи сохранены как история).
- OKF `validate` — conformant; `npm run build` — зелёный.[^commit-uninstall-fix]

# Решения

| Решение                              | Обоснование                                                                          |
| ------------------------------------ | ------------------------------------------------------------------------------------ |
| Полный плагин, не только скилл       | ponytail сам по себе system-prompt + команды; отдельное копирование скилла избыточно |
| Пин `@dietrichgebert/ponytail@5.3.3` | Точная версия = нет молчаливых апдейтов промпта в каждую сессию                      |
| Body PR на английском                | Решение владельца (коммиты всегда на английском)                                     |
| Merge только владельцем              | Явное требование; агент не имеет права завершать чужой мерж                          |
| PR из коммитов текущей ветки         | Явное требование; чужие ветки и staged-куски в PR не попадают                        |

# Проверка

- `npm run build` ✓ (не тронут продуктовый код, но гейт прогнан)
- OKF validate: conformant (4 битых cross-link на ещё не написанные фазы — терпимы по §6.1; 5-е предупреждение про footnote в заметке 2026-08-27 исправлено в этой сессии)
- `/ponytail-review` по ветке — 1 находка, исправлена[^commit-uninstall-fix]

# Дальше

1. Владелец: self-review PR #12 → _Squash and merge_.
2. После мержа: удаление ветки `chore/remove-coderabbit` (локально и на GitHub) — по команде владельца.
3. ponytail always-on: при желании смягчить — `/ponytail lite` в сессии или `PONYTAIL_DEFAULT_MODE=lite` в окружении.

[^pr-12]: PR #12 — chore(ci): remove CodeRabbit AI, adopt ponytail agent review

[^commit-remove-config]: chore(ci): remove CodeRabbit AI review config

[^commit-ponytail-plugin]: chore(opencode): add ponytail agent plugin (pinned 5.3.3)

[^commit-uninstall-fix]: docs: correct CodeRabbit App uninstall status (коммит-фикса находок ревью)

[^commit-create-pr]: chore(opencode): add create-pr skill

[^opencode-config]: .opencode/opencode.json — конфигурация opencode (plugins)

[^create-pr-skill]: .opencode/skills/create-pr/SKILL.md — скилл создания PR

[^git-flow]: Git Flow & Conventional Commits — секция Agent review (ponytail)
