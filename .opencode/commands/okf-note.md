---
description: Create an OKF work note about completed work from session context and git diff
---

Load the `okf` skill now via the skill tool and follow its produce/maintain
modes for everything below.

Important: this environment does not define `${CLAUDE_SKILL_DIR}`. Wherever the
skill text references it, substitute the real in-project path instead: the
skills live under `.opencode/skills/<skill-name>/`.

## Session context

Changed files:

!`git status --short`

Diff stat against HEAD:

!`git diff HEAD --stat`

Recent commits:

!`git log --oneline -10`

Additional user notes for this entry (may be empty): $ARGUMENTS

## Task

Record the work done in this session as a note in the OKF bundle at
`docs/knowledge/`, keeping the bundle conformant with the OKF v0.2 spec.

Language rule: write all visible content — note bodies, headings, tables,
lists, footnote definitions, `log.md` entries, descriptions in `index.md` —
in Russian. Keep YAML frontmatter keys and values (`type`, `title`,
`description`, `tags`, `sources[].title`, actor ids) in English.

1. If `docs/knowledge/index.md` does not exist yet, scaffold the bundle first:
    ```
    uv run .opencode/skills/okf/scripts/okf_init.py docs/knowledge --title "Portfolio"
    ```
2. Read the full working-tree diff (`git diff HEAD`) and skim touched files as
   needed. Combine the diff with the conversation context above into a clear
   picture of what was done, why, and which decisions were made.
3. Write exactly one concept file for this session:
   `docs/knowledge/work/YYYY-MM-DD-<short-slug>.md` (today's date, kebab-case
   slug), modeled on the skill's `templates/concept.md`. Frontmatter must have:
    - a non-empty `type` (use `Work Note`)
    - `title` and a one-line `description`
    - `generated: { by: opencode/okf-note, at: <current UTC ISO-8601 timestamp> }`
    - `sources`: one entry per meaningful input (key commits, main touched
      files), each with `id` and `resource`; attribute specific claims in the
      body with `[^id]` footnotes matching those source ids
4. If it does not exist yet, create `docs/knowledge/work/index.md` — a
   reserved file: no frontmatter, just a markdown list linking the work notes.
5. Append a dated (ISO) entry to `docs/knowledge/log.md`, newest first,
   summarizing what was added or updated.
6. Update the body of `docs/knowledge/index.md` so its `work/` section lists
   the new note.
7. Validate:
    ```
    uv run .opencode/skills/validate/scripts/okf_validate.py docs/knowledge --strict
    ```
    Resolve every ERROR. Fix warnings too when cheap; if some remain, re-run
    without `--strict` to confirm zero errors, then report the leftovers. If
    `uv` is unavailable, fall back to
    `python3 -m pip install --quiet pyyaml && python3 <script>`.
8. Report which files were created or updated.
