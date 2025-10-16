Theming and UI Consistency

Overview
- We now have a single source of truth for colors and gradients in `src/assets/theme.css`.
- Global components to reuse everywhere: `BaseButton.vue`, `FormInput.vue`, `PageHeader.vue`.
- Account-specific duplicates now wrap the global components to keep old imports working.

Theme tokens
- Brand: `--brand`, `--brand-2`
- Accent: `--accent`, `--accent-2`
- Text/Ink: `--ink-1` (dark), `--ink-2`, `--ink-3`, `--ink-4`, `--ink-5` (borders)
- Surfaces: `--surface`, `--surface-2`

Utilities
- `.gradient-brand` for primary gradient backgrounds
- `.gradient-outline` for gradient borders around white cards
- `.bg-page-soft` for subtle radial accent backgrounds

Usage examples
- Page header: `<PageHeader title="Title" subtitle="Subtitle" icon="fas fa-tag" />`
- Button: `<BaseButton variant="primary" size="large">Comprar</BaseButton>`
- Input: `<FormInput id="email" v-model="email" label="Email" />`

View layout tips
- Add `under-fixed-header` and inline `--header-height` to avoid overlap with fixed Header.
- Wrap main content with `.bg-page-soft` when you want the soft background accents.

Notes
- Please avoid introducing new color literals; use CSS variables instead.
- If a view needs a new color, add a variable to `theme.css` rather than hardcoding.