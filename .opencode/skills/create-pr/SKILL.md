---
name: create-pr
description: >-
  Open a pull request to main for this project, from the commits of the
  current branch only. Encodes the project's PR requirements: English title
  and body (Conventional Commits), gh CLI, verification gates, a
  /ponytail-review pass — and the rule that the merge is always done by the
  user personally, never by the agent. Use when asked to open a PR
  ("create a PR", "open a PR", "создай PR", "открой PR", "PR в main").
user-invocable: true
argument-hint: ""
allowed-tools: Bash Read Grep
---

# Create a PR against `main`

Full workflow: `docs/knowledge/process/git-flow.md`. This skill only covers
opening the PR. Never merge.

## 1. Preconditions

- Not on `main` (otherwise stop and say why).
- Working tree is clean (`git status --porcelain` — no output).
- Branch name follows the git-flow types: `feature/*`, `fix/*`, `chore/*`,
  `content/*`, `refactor/*`.

## 2. Scope: commits of the current branch only

Base is always `main`, head is always the current branch. The PR contains
only this branch's commits — never another branch's, never staged-only pieces.

```bash
git log --oneline main..HEAD    # what this PR will contain
git diff --stat main...HEAD     # this branch's total diff
```

If the branch was cut from something other than an up-to-date `main`, or holds
commits that are not yours to ship (e.g. it includes another feature branch),
stop and tell the user. Do not silently rebase, cherry-pick, or re-cut the
branch.

## 3. Push the whole branch first

```bash
git push -u origin <branch>
```

## 4. Verification before opening

Run both, report results honestly:

```bash
npm run build
```

If the diff touches `docs/knowledge/**`, also run the OKF checker — it must
report conformant (pre-existing warnings are fine):

```bash
python3 .opencode/skills/validate/scripts/okf_validate.py docs/knowledge
```

There is no CI in this project yet (Phase 8), so do not write "checks passed" —
write exactly what you ran and what it said.

## 5. Title and body — English only

Title: English, Conventional Commits, describing the whole squashed change:

`chore(ci): remove CodeRabbit AI, adopt ponytail agent review`

Body: exactly these sections.

```markdown
## Summary

<1–3 sentences: what and why>

## Changes

- `<subject from git log>` — <one line: what/why>
- ...

## Verification

- `npm run build` ✓ / ✗ (what happened)
- OKF validate: conformant / not applicable

## Notes / manual steps

<anything the reviewer or owner must do/know: manual GitHub settings, risks,
migrations. Nothing? Write "None.">
```

No reference to a PR template — none exists in this repo.

## 6. Create the PR

```bash
gh pr create --base main --head <branch> --title "<title>" --body "<body>"
```

If `gh` fails, report the error verbatim. Do not improvise a workaround.

## 7. Review gate

Run `/ponytail-review` on the diff (`git diff main...HEAD`). Fix the Must fix
findings; discuss Should fix with the user; never apply findings blindly.
Push fix commits to the same branch, then re-run the review if fixes were
material.

## 8. The merge is the user's, always

The agent never merges: no `gh pr merge`, no `--auto`, no touching branch
protection, no clicking around. After the gate is clean, say the PR is ready
and wait. If the user later confirms the merge happened, offer — never do it
unasked — to delete the branch (`git push origin --delete <branch>` + local).

Always print the PR URL when done.
