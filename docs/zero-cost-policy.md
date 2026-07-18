# Zero-Cost Policy / 0円運用ポリシー

> **Non-negotiable:** this project may not create a subscription, usage charge, payment authorization, or billable cloud resource.

> [!IMPORTANT]
> 本プロジェクトは課金を一切許可しない。月額、従量課金、決済手数料、有料アドオン、無料期間終了後の自動課金を含め、支払い方法の登録を必要とする機能は使用しない。

## Approved zero-cost stack

| Capability | Approved implementation | Cost guardrail |
| --- | --- | --- |
| Local WordPress | `wp-env`, Docker-compatible local engine, MySQL, WP-CLI | Local machine only; no hosted database |
| Store platform | WordPress, WooCommerce, project-authored GPL code | No premium themes or plugins |
| Public demo | WordPress Playground in the reviewer's browser | No persistent hosted WordPress instance |
| Source control | Public GitHub repository | Repository must remain public for the CI policy |
| CI | Standard `ubuntu-latest` GitHub-hosted runner | No larger runners; no private-repository conversion |
| Payments | Renamed WooCommerce cash-on-delivery test method | No gateway account, token, webhook, or charge |
| Email | Disabled/short-circuited in seeded demos | No SMTP or transactional email provider |
| Assets | Project-authored or explicitly compatible free assets | No stock subscription or remote paid font |

## Prohibited services and actions

- Paid hosting, managed WordPress, hosted databases, object storage, CDNs, or custom domains.
- Stripe, PayPal, Square, Braintree, or any real payment gateway.
- SendGrid, Mailgun, Postmark, AWS SES, or other metered email delivery.
- Metered AI, verification, address, analytics, monitoring, or fraud APIs.
- GitHub larger runners, paid Code Quality, paid secret-protection products, Git LFS purchases, Packages overages, or long-lived Actions artifacts.
- Premium WooCommerce extensions, premium themes/plugins, and assets requiring an ongoing subscription.
- Trials that require a card or become paid automatically.

If a feature cannot be implemented with the approved stack, it is moved out of scope. It is never silently replaced with a paid service.

## GitHub safeguards

1. Keep the repository public.
2. Use only `ubuntu-latest`; `npm run check:zero-cost` rejects other runner labels.
3. Do not configure larger runners or attach a cloud billing account.
4. Do not upload CI artifacts; screenshots and reports remain local unless deliberately added to a release.
5. Keep spending limits at zero and do not enable metered products.

GitHub documents standard GitHub-hosted Actions runners as free for public repositories, while larger runners are always billable. If repository visibility or GitHub policy changes, Actions must be disabled until the zero-cost condition is re-verified.

## Docker licensing boundary

Docker Desktop is documented as free for personal use, education, non-commercial open source, and qualifying small businesses. This portfolio is developed as personal/open-source work. If the usage context no longer qualifies, do not purchase Docker Desktop for this project; use an eligible free local container engine or stop the Docker workflow.

## Release checklist

- [ ] No payment method or paid subscription was created for the project.
- [ ] Repository is public and all runners are standard `ubuntu-latest`.
- [ ] No CI artifacts, packages, LFS data, or caches can create an overage.
- [ ] Playground runs in the browser and no hosted WordPress instance exists.
- [ ] Checkout exposes only the no-payment test method.
- [ ] Email delivery is disabled.
- [ ] `npm run check:zero-cost` passes.

