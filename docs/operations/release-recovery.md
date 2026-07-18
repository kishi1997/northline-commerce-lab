# Release and Recovery / リリース・復旧

## Release preparation

1. Confirm repository visibility is Public and the zero-cost policy still applies.
2. Start from a clean clone with Node 22, PHP 8.3 tooling, Composer, and a free eligible Docker-compatible engine.
3. Run:

```bash
npm ci
composer install
npm run env:start
npm run setup
npm run lint
npm test
npm run playground:build
git diff --check
```

4. Inspect `WP_CONTENT_DIR/debug.log`; release only when tested project paths produce no warning, notice, or deprecated message.
5. Capture screenshots and the 1–2 minute walkthrough; label self-review evidence honestly.
6. Commit the two custom ZIP files and Blueprint JSON from `playground/bundle` in the release PR.
7. Create a signed/annotated version tag after the PR is merged; Playground reads the bundle from that immutable raw tag URL.
8. Optionally attach the same four files to the GitHub Release for human inspection. The Playground URL must not use GitHub Release redirects because browsers can reject them under CORS.
8. Update README links to the immutable release tag, never an unpinned `main` artifact.

## Playground smoke test

- Storefront Blueprint opens `/` with twelve products.
- Admin Blueprint logs into the disposable administrator account.
- Variable selection, cart, `NORTHLINE10`, three shipping choices, denied region, allowed region, and order confirmation work.
- Changes stay inside the reviewer's browser.

## Recovery

### Local data corruption

```bash
npm run reset
```

This deletes only the project-managed wp-env databases/containers and regenerates them from source. Never point the command at a production or unrelated Docker project.

### Bad content change

- Use WordPress revisions for pages/templates where available.
- Restore product fields from the seed fixture by rerunning `npm run setup`.
- Do not commit/export the damaged database as a repair mechanism.

### Bad code release

1. Revert through a new PR; do not rewrite public history.
2. Retag only with a new patch version such as `v1.0.1`.
3. Keep the affected release and document the known issue when it helps the audit trail.
4. Rebuild immutable Playground assets and update README links.

### Playground failure

- Verify release URLs return ZIP/JSON with HTTP 200.
- Validate Blueprint JSON and pinned WooCommerce URL.
- Rebuild from the same tag; do not switch to an unversioned remote dependency.

## 日本語要約

リリースはクリーンclone、全検査、デバッグログ、Playgroundスモークテストを通してから固定タグで行う。障害時はDBダンプではなくシードから再生成し、公開履歴を書き換えずrevert PRとパッチ版で復旧する。
