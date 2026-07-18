# Visual Design System / ビジュアル設計

## Approved direction

The approved storefront combines an editorial magazine composition with an industrial product catalogue. The implementation should feel designed around the catalog rather than assembled from interchangeable ecommerce cards.

承認デザインは、雑誌的な編集レイアウトと工業製品カタログの精密さを組み合わせる。均等なカードを並べたテンプレート感を避け、商品情報を起点に画面を構成する。

![Approved homepage V2](design/approved-homepage-v2.png)

## Principles

1. **Asymmetry with order** — use a 12-column grid, unequal spans, deliberate offsets, and a visible reading order.
2. **Commerce remains obvious** — name, price, stock, variation, and add-to-cart actions are never decorative or hidden.
3. **Industrial restraint** — square edges, one-pixel rules, mineral surfaces, monochrome product imagery, and minimal shadow.
4. **Orange means action** — copper orange is reserved for primary actions, active state, warnings, and short status accents.
5. **Accessible tension** — unusual composition must preserve contrast, document order, keyboard focus, and generous targets.

## Tokens

| Role | Token | Value |
| --- | --- | --- |
| Ink | `--nl-ink` | `#171a1b` |
| Mineral | `--nl-mineral` | `#f2efe8` |
| Paper | `--nl-paper` | `#fffdf8` |
| Concrete | `--nl-concrete` | `#d5d0c7` |
| Muted text | `--nl-muted` | `#696863` |
| Action | `--nl-copper` | `#c65d32` |
| Rule | `--nl-rule` | `#aca79e` |
| Focus | `--nl-focus` | `#4f9dff` |

Spacing follows a fluid 4/8 base. Content uses a maximum width of 1600px. Display type is condensed and uppercase; body copy remains a neutral system sans serif so the theme does not depend on remote fonts.

## Responsive behavior

- Desktop: 12 columns; hero, mosaic, and featured modules may overlap grid zones.
- Tablet: 8 columns; overlaps reduce and product controls remain next to their product.
- Mobile: 4 columns; the visual order becomes the DOM order, product cards become horizontal modules where space permits, and sticky purchase controls never cover form errors.
- Motion respects `prefers-reduced-motion`.

## Design-to-code acceptance

- No section may default to a repeated row of three or four equal cards unless the content genuinely has equal priority.
- Every visual overlap must retain a readable fallback at 200% zoom.
- Decorative index numbers and vertical labels are hidden from assistive technology.
- Focus is always visible against both dark and light surfaces.

