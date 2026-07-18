# Contributing / 開発フロー

Northline Commerce Lab uses GitHub Flow and preserves honest implementation history. There are no fictional reviewers or fabricated approvals.

## Workflow

1. Open an Issue with user/operator value and acceptance criteria.
2. Create `feat/*`, `fix/*`, or `docs/*` from an up-to-date `main` branch.
3. Use English Conventional Commit messages, for example `feat: validate restricted checkout regions`.
4. Open a Pull Request with implementation decisions, verification evidence, accessibility impact, security impact, and rollback notes.
5. Complete a named self-review when no independent reviewer is available.
6. Merge only after required checks pass; use squash merge.

The Codex desktop workspace may use the required `codex/` branch prefix while developing locally. The PR title remains Conventional Commits compatible.

## Required local verification

```bash
npm run lint
npm run test:php
npm run test:e2e
npm run playground:build
git diff --check
```

`npm run check:zero-cost` is mandatory. A change that introduces a paid or metered service is rejected rather than deferred to runtime configuration.

## Pull request evidence

- Link the Issue and any ADR.
- State what changed for customers and store operators.
- Include desktop/mobile screenshots when presentation changes.
- List tested success and failure states.
- Identify every new capability, nonce, input, output, order field, and personal-data path.
- Explain rollback and whether reseeding is required.

## 日本語要約

Issueからブランチを作り、Conventional Commits、PR、CI、squash mergeで進める。独立レビューがない場合は架空の承認を作らず、セルフレビューと明記する。0円ポリシー、アクセシビリティ、セキュリティ、復旧方法はすべてのPRで確認する。

