---
type: Work Note
title: OKF tooling setup — skills and /okf-note command
description: Installed OKF agent skills project-locally, added the /okf-note session-note command, and chose Obsidian as the bundle reader.
tags:
    - okf
    - opencode
    - tooling
status: stable
generated:
    by: opencode/okf-note
    at: '2026-08-24T21:12:42Z'
sources:
    - id: okf-skills-repo
      resource: https://github.com/scaccogatto/okf-skills
      title: scaccogatto/okf-skills — the OKF toolkit for Claude Code
    - id: opencode-docs
      resource: https://opencode.ai/docs/skills/
      title: OpenCode documentation — Agent Skills and Commands
      last_modified: '2026-08-24'
    - id: git-worktree
      resource: portfolio working tree at HEAD 15043b4 (uncommitted changes)
      title: Local uncommitted diff and new files
---

# Обзор

В этой сессии в репозиторий встроен инструментарий OKF: агентные скиллы из апстрим-тулкита[^okf-skills-repo] установлены как обычные проектные скиллы (осознанно не как плагин Claude Code), кастомная команда `/okf-note` превращает контекст сессии и состояние git в рабочие заметки этого самого бандла, а Obsidian выбран для просмотра графа и повседневного чтения.

# Изменения

- Скиллы `okf` и `validate` установлены дословно из апстримного `skills/` в `.opencode/skills/<name>/` — OpenCode автоматически находит там любые `skills/*/SKILL.md`.[^opencode-docs]
- Третий скилл, `visualize`, сперва был установлен, но затем **удалён**: его HTML-рендер графа заменяет нативный Graph View Obsidian над этими связанными markdown-файлами.
- Создан `.opencode/commands/okf-note.md`: он инжектит в промпт `git status --short`, `git diff HEAD --stat` и `git log --oneline -10`, загружает скилл `okf`, пишет ровно одну рабочую заметку за запуск в `work/`, обновляет `log.md` и индексы, а затем прогоняет детерминированный валидатор с `--strict`.[^opencode-docs]
- Текст скилла ссылается на скрипты через `${CLAUDE_SKILL_DIR}` — переменную окружения, которой нет вне Claude Code, поэтому команда подставляет реальные пути внутри проекта (`.opencode/skills/<skill-name>/`), оставляя вендоренные файлы скиллов нетронутыми для лёгкого обновления из апстрима.
- Этот бандл намеренно живёт в `docs/knowledge/` вместо дефолтного `.okf/`, переиспользуя существующую пустую папку; кросс-ссылки сохраняют абсолютный от корня бандла формат.
- `docs` удалён из `.gitignore` (изменение не закоммичено), чтобы бандл можно было закоммитить рядом с кодом; также новое в дереве: `AGENTS.md`, `TODO.md`, `.opencode/`.[^git-worktree]

# Решения

| Решение                                      | Обоснование                                                                                                                           |
| -------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------- |
| Установка скиллами, а не плагином            | Файлы внутри репозитория, без зависимости от маркетплейса плагинов; совпадает с нативными путями обнаружения OpenCode.                |
| Оставить `validate`, удалить `visualize`     | Валидация — гейт качества данных (битый YAML-фронтматтер ломает Properties/Dataview в Obsidian); визуализацию закрывает сам Obsidian. |
| Бандл в `docs/knowledge/`                    | Предпочтение пользователя; папка уже существовала.                                                                                    |
| Инжект stat диффа, полный дифф по требованию | Промпт не разбухает, при этом агент может изучить полные изменения своими инструментами.                                              |

# Файлы

- `.opencode/skills/okf/**`, `.opencode/skills/validate/**` — установлены (дословно из апстрима[^okf-skills-repo])
- `.opencode/commands/okf-note.md` — создан
- `docs/knowledge/**` — этот бандл (развёрнут `okf_init.py`, затем расширен)

[^okf-skills-repo]: scaccogatto/okf-skills — GitHub-репозиторий, источник вендоренных скиллов.

[^opencode-docs]: Документация OpenCode: страницы Agent Skills и Commands.

[^git-worktree]: Наблюдалось напрямую через `git status` / `git diff HEAD` в ходе этой сессии.
