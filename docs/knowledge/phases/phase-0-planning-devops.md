---
type: Phase Notes
title: Phase 0 — Planning & DevOps Setup
description: Инициализация GitHub репозитория, настройка branch protection, установка CodeRabbit AI
tags:
    - phase-0
    - planning
    - devops
    - github
    - coderabbit
status: stable
generated:
    by: opencode/okf-note
    at: '2026-08-27T10:00:00Z'
verified:
    by: human:sogohlopec
    at: '2026-08-27T10:00:00Z'
sources:
    - id: phase0-repo
      resource: /docs/notes/Реализация Phase 0 Planning & DevOps Setup `chore(repo) init github repository and setup branch protection rules` (запретить прямой пуш в `main`).md
      title: Настройка GitHub репозитория и branch protection (raw notes)
      author: human:sogohlopec
      last_modified: '2026-03-17'
    - id: phase0-coderabbit
      resource: /docs/notes/Реализация Phase 0 Planning & DevOps Setup `chore(ci) install CodeRabbit AI app to the repository.md
      title: Установка CodeRabbit AI (raw notes)
      author: human:sogohlopec
      last_modified: '2026-03-17'
---

# Обзор

Phase 0 подготовила инфраструктуру репозитория перед началом разработки. Основные шаги: создание GitHub репозитория, настройка защиты ветки `main`, подключение CodeRabbit AI для авторевью PR.

---

# Задачи и выполнение

## 1. `chore(repo): init github repository and setup branch protection rules`

**Цель**: Запретить прямой пуш в `main`, требовать PR для всех изменений.

**Шаги**:

1. Создан репозиторий `portfolio` на GitHub (public/private — по выбору).
2. В настройках репозитория: **Settings → Branches → Add branch protection rule**:
    - Branch name pattern: `main`
    - ✅ Require a pull request before merging
    - ✅ Require approvals (1 minimum)
    - ✅ Dismiss stale PR approvals when new commits are pushed
    - ✅ Require status checks to pass before merging (позже добавятся CI проверки)
    - ✅ Require branches to be up to date before merging
    - ✅ Do not allow bypassing the above settings
3. Локально: `git init`, `git remote add origin`, первый коммит с `README.md` и `.coderabbit.yaml`, `git push -u origin main`.

**Результат**: Прямой пуш в `main` запрещён. Любая работа — только через feature-ветки и PR.

---

## 2. `chore(ci): install CodeRabbit AI app to the repository`

**Цель**: Автоматические AI-ревью каждого Pull Request.

**Шаги**:

1. В GitHub Marketplace найден и установлен **CodeRabbit AI** (бесплатный план для public репозиториев, или trial для private).
2. Дан доступ к репозиторию `portfolio`.
3. В корне проекта создан файл `.github/coderabbit.yaml` с базовой конфигурацией:
    ```yaml
    # Пример минимальной конфигурации
    reviews:
        profile: chill
        high_level_summary: true
        poem: false
    ```
4. CodeRabbit теперь автоматически комментирует каждый новый PR: пишет саммари изменений, указывает на потенциальные баги, security issues, style nits.

**Результат**: Каждый PR получает мгновенный технический ревью от AI. Это имитирует процесс командной разработки и подстраховывает от очевидных ошибок.

---

# Ключевые решения

| Решение                                   | Обоснование                                                                              |
| ----------------------------------------- | ---------------------------------------------------------------------------------------- |
| Защита `main` с требуемым PR              | Гарантирует, что `main` всегда production-ready; история чистая; есть контекст изменений |
| CodeRabbit AI на бесплатном плане         | Даёт 80% пользы код-ревью за 0$; подходит для соло-проекта                               |
| Ранняя настройка (до первой строчки кода) | Избегает рефакторинга процесса позже; приучает к дисциплине с первого дня                |

---

# Связанные концепты

- [Git Flow](/process/git-flow.md) — детальное описание стратегии веток и коммитов
- [Project Plan](/decisions/project-plan.md) — полный дорожная карта всех фаз

[^phase0-repo]: Настройка GitHub репозитория и branch protection (raw notes)

[^phase0-coderabbit]: Установка CodeRabbit AI (raw notes)
