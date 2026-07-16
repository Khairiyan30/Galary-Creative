# Design System Inspired by Pinterest

## 1. Visual Theme & Atmosphere

The Pinterest design system embodies a modern, approachable aesthetic centered around inspiration and creativity. The visual language combines bold, confident primary colors with clean neutral foundations, creating an environment that feels both energetic and trustworthy. The design prioritizes content discovery through generous use of whitespace, rounded corners for friendliness, and a carefully curated color palette that balances vibrant accents with calm, neutral tones. Typography is bold and commanding at larger scales, drawing users into curated collections and personalized inspiration. The overall mood is optimistic, inclusive, and user-centric—designed to make creative exploration feel effortless and joyful.

**Key Characteristics**
- Bold, vibrant primary red paired with sophisticated neutral foundations
- Clean, modern aesthetic with generous whitespace and breathing room
- Rounded, friendly button treatments and card corners
- High contrast between interactive elements and neutral backgrounds
- Content-forward design that emphasizes imagery and user-generated inspiration
- Accessible and inclusive visual hierarchy
- Confident typography with strong weight differentiation

## 2. Color Palette & Roles

### Primary
- **Brand Red** (`#D72323`): Primary call-to-action buttons, hero elements, and brand identity
- **Dark Gray** (`#3E3636`): Primary text, headings, and dominant UI component backgrounds

### Accent Colors
- **Purple Gradient** (`#583B91`, `#6845AB`, `#774FC4`): Secondary accent elements, category highlights, and decorative accents
- **Sky Blue** (`#0866FF`): Secondary interactive elements and information states
- **Vibrant Orange** (`#DB5B06`): Alert highlights and secondary attention-drawing elements
- **Forest Green** (`#068440`): Premium or exclusive content markers
- **Cobalt** (`#376DF6`): Link highlights and interactive accent states

### Interactive
- **Error Red** (`#DD0E0E`): Primary error state indicator
- **Danger Red** (`#D72323`): Destructive actions and prominent warnings
- **Success Green** (`#02B70E`): Confirmation and success state feedback
- **Bright Success** (`#05D50B`): Secondary success indicators

### Neutral Scale
- **Pure Black** (`#000000`): Critical text, primary body copy, and strong contrast overlays
- **True White** (`#FFFFFF`): Primary background, card backgrounds, and content containers
- **Warm Gray** (`#62625B`): Secondary text, helper text, and muted information
- **Light Gray** (`#E5E5E0`): Subtle dividers, input borders, and background accents
- **Off-White** (`#F6F6F3`): Soft background sections and subtle container fills
- **Soft Gray** (`#EFEFEF`): Secondary background sections and alternate row treatments
- **Near Black** (`#181816`): Text emphasis and strong contrast requirements
- **Charcoal** (`#242421`): Deep background and high-contrast typography

### Surface & Borders
- **Input Border** (`#919190`): Form field borders and focus states
- **Form Border** (`#C1C1C1`): Secondary input borders and dividing lines

## 3. Typography Rules

### Font Family
**Primary:** Pin Sans, Helvetica Neue, Helvetica, Arial, sans-serif
**Secondary:** Pin Sans (system fallback stack as above)

### Hierarchy

| Role | Font | Size | Weight | Line Height | Letter Spacing | Notes |
|------|------|------|--------|-------------|-----------------|-------|
| Display/Hero | Pin Sans | 24px | 700 | 26.4px | 0px | Large headings, page titles, hero sections |
| Heading (H1) | Pin Sans | 24px | 700 | 26.4px | 0px | Primary page headings |
| Heading (H2) | Pin Sans | 16px | 700 | 22.4px | 0px | Section headings and card titles |
| Body/Paragraph | Pin Sans | 14px | 700 | normal | 0px | Primary body text and descriptive content |
| Button/Label | Pin Sans | 12px | 400 | normal | 0px | Button text, labels, and UI microcopy |
| Caption/Helper | Pin Sans | 12px | 400 | 18px | 0px | Helper text, captions, and secondary information |
| Input Placeholder | Pin Sans | 16px | 400 | normal | 0px | Form input fields and search boxes |
| Code/Monospace | Pin Sans | 12px | 400 | 18px | 0px | Technical content and code snippets |

### Principles
- Weight differentiation creates clear hierarchy—700 weight for headings and emphasis, 400 weight for body and supplementary content
- Line height increases for longer-form text (captions 18px) while body copy uses tight, efficient spacing
- All typography is set in Pin Sans for brand consistency and distinctive visual identity
- Button text uses lighter weight (400) to balance the compact `48px` height container
- Inputs maintain `16px` size for comfortable reading and interaction on touch devices
- Letter spacing remains uniform at 0px for modern, clean appearance

## 4. Component Stylings

### Buttons

#### Primary Button
- **Background:** `#D72323`
- **Text Color:** `#000000`
- **Font Size:** `12px`
- **Font Weight:** `400`
- **Padding:** `6px 14px`
- **Border Radius:** `16px`
- **Border:** `2px solid transparent`
- **Height:** `48px`
- **Line Height:** `normal`
- **Hover State:** Darken background to `#C70020`, maintain text color
- **Active State:** Apply `box-shadow: inset 0px 2px 4px rgba(0, 0, 0, 0.2)`
- **Focus State:** Add `box-shadow: 0px 0px 0px 3px rgba(230, 0, 35, 0.2)`

#### Secondary Button
- **Background:** `#E5E5E0`
- **Text Color:** `#000000`
- **Font Size:** `12px`
- **Font Weight:** `400`
- **Padding:** `6px 14px`
- **Border Radius:** `16px`
- **Border:** `2px solid transparent`
- **Height:** `48px`
- **Line Height:** `normal`
- **Hover State:** Darken background to `#D4D4CF`, maintain text color
- **Active State:** Apply `box-shadow: inset 0px 2px 4px rgba(0, 0, 0, 0.15)`
- **Focus State:** Add `box-shadow: 0px 0px 0px 3px rgba(229, 229, 224, 0.3)`

#### Ghost Button (Icon/Text Link)
- **Background:** `transparent`
- **Text Color:** `#3E3636`
- **Font Size:** `12px`
- **Font Weight:** `400`
- **Padding:** `0px`
- **Border Radius:** `0px`
- **Border:** `none`
- **Height:** `48px`
- **Width:** `48px` (square for icon buttons)
- **Line Height:** `normal`
- **Hover State:** Apply light background `#F6F6F3`, darken text to `#000000`
- **Active State:** Darken background to `#EFEFEF`
- **Focus State:** Add `box-shadow: 0px 0px 0px 2px rgba(33, 25, 34, 0.15)`

### Cards & Containers

#### Primary Card
- **Background:** `#FFFFFF`
- **Border:** `1px solid #E5E5E0`
- **Border Radius:** `12px`
- **Padding:** `16px`
- **Box Shadow:** `0px 1px 3px rgba(0, 0, 0, 0.08)`
- **Hover State:** Elevate with `box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.12)`

#### Compact Card (Pin Grid Item)
- **Background:** `#FFFFFF`
- **Border Radius:** `16px`
- **Overflow:** `hidden`
- **Box Shadow:** `0px 2px 4px rgba(0, 0, 0, 0.1)`
- **Hover State:** Apply `box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.15)` and slight scale transform

### Inputs & Forms

#### Text Input (Default)
- **Background:** `#FFFFFF`
- **Text Color:** `#000000`
- **Placeholder Color:** `#62625B`
- **Font Size:** `16px`
- **Font Weight:** `400`
- **Padding:** `11px 15px`
- **Border Radius:** `16px`
- **Border:** `1px solid #919190`
- **Height:** `48px`
- **Line Height:** `22.4px`
- **Focus State:** Border color changes to `#3E3636`, apply `box-shadow: 0px 0px 0px 2px rgba(33, 25, 34, 0.1)`
- **Error State:** Border color `#DD0E0E`, background tint `rgba(221, 14, 14, 0.05)`

#### Search Input
- **Background:** `rgba(0, 0, 0, 0)` (transparent/overlay)
- **Text Color:** `#000000`
- **Font Size:** `16px`
- **Font Weight:** `400`
- **Padding:** `0px`
- **Border Radius:** `0px`
- **Border:** `none`
- **Height:** `48px`
- **Width:** `554px` (responsive container)
- **Line Height:** `normal`

#### Form Input (Compact)
- **Background:** `#FFFFFF`
- **Text Color:** `#000000`
- **Font Size:** `12px`
- **Font Weight:** `400`
- **Padding:** `0px`
- **Border Radius:** `0px`
- **Border:** `1px solid #C1C1C1`
- **Height:** `40px`
- **Width:** `250px` (standard form width)
- **Line Height:** `normal`
- **Focus State:** Border `1px solid #3E3636`

### Navigation

#### Header Navigation
- **Background:** `#FFFFFF`
- **Text Color:** `#3E3636`
- **Font Size:** `12px`
- **Font Weight:** `400`
- **Padding:** `16px`
- **Border Radius:** `0px`
- **Border:** `none`
- **Height:** `80px`
- **Box Shadow:** `0px 1px 0px rgba(0, 0, 0, 0.08)`
- **Active Item:** Text color `#D72323`, apply bottom border `2px solid #D72323`
- **Hover Item:** Background `#F6F6F3`, text `#000000`

### Links

#### Text Link (Inline)
- **Background:** `transparent`
- **Text Color:** `#3E3636`
- **Font Size:** `12px`
- **Font Weight:** `400`
- **Padding:** `0px`
- **Border Radius:** `0px`
- **Border:** `none`
- **Decoration:** `underline`
- **Hover State:** Text color `#D72323`, underline thickness increases
- **Focus State:** Apply `outline: 2px solid rgba(33, 25, 34, 0.2)`
- **Visited State:** Text color `#62625B`

#### Pill Link (Rounded Container)
- **Background:** `transparent`
- **Text Color:** `#3E3636`
- **Font Size:** `12px`
- **Font Weight:** `400`
- **Padding:** `6px 14px`
- **Border Radius:** `999px`
- **Border:** `1px solid #E5E5E0`
- **Height:** `21px`
- **Hover State:** Background `#F6F6F3`, border `#C1C1C1`
- **Active State:** Background `#E5E5E0`, border `#919190`

## 5. Layout Principles

### Spacing System
**Base Unit:** `4px`

**Scale:**
- `4px`: Micro spacing, tight component gaps
- `8px`: Small padding, icon spacing
- `12px`: Component internal spacing
- `16px`: Primary padding for containers and sections
- `24px`: Section margin, vertical rhythm
- `32px`: Card internal spacing, larger containers
- `36px`: Section spacing
- `40px`: Large section margins
- `64px`: Major content section spacing
- `92px`: Extended content padding
- `100px`: Hero section padding
- `120px`: Full page section margins

**Usage Context:**
- `4px` and `8px` for button padding and tight controls
- `12px` and `16px` for standard form inputs and card padding
- `24px` and `32px` for section-to-section transitions
- `40px` and `64px` for major content blocks
- `100px` and `120px` for hero sections and full-width spacing

### Grid & Container
- **Max Width:** `1440px` (navigation and full-width sections)
- **Column Strategy:** 12-column grid system with `16px` gutters
- **Container Padding:** `16px` on mobile, `32px` on tablet, `64px` on desktop
- **Section Pattern:** Full-width background containers with centered content max-width, allowing asymmetrical content layouts

### Whitespace Philosophy
Generous whitespace creates visual breathing room and prioritizes content over decoration. Margins between sections follow the `4px` base unit scale. Internal padding within components (buttons, inputs, cards) uses tighter spacing (`6px`–`16px`) to maintain compactness. Between major content sections, spacing jumps to `64px`–`120px` to create clear visual separation and guide user attention through hierarchy.

### Border Radius Scale
- `0px`: Navigation bars, full-width sections, and traditional form inputs
- `2px`: Badge elements and subtle component corners
- `12px`: Secondary cards and container corners
- `16px`: Primary buttons, input fields, and rounded card containers
- `999px`: Pill-shaped buttons, toggle switches, and fully rounded elements

## 6. Depth & Elevation

| Level | Treatment | Use |
|-------|-----------|-----|
| Flat (None) | No shadow, `box-shadow: none` | Form inputs, text content, neutral backgrounds |
| Raised (1) | `box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.08)` | Card bases, subtle elevation, default card state |
| Elevated (2) | `box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.12)` | Hovered cards, secondary elevation, modal backgrounds |
| Prominent (3) | `box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.15)` | Modals, expanded cards, primary interactive hover state |
| Maximum (4) | `box-shadow: 0px 12px 24px rgba(0, 0, 0, 0.2)` | Dropdowns, tooltips, overlay elements, full-screen modals |

**Shadow Philosophy:** Shadows are subtle and soft, using low-opacity black (`rgba(0, 0, 0, 0.08–0.2)`) to create minimal but perceptible depth. Most UI elements remain relatively flat with single-pixel borders preferred over shadows. Elevation increases only on interactive states (hover) or distinct container types (modals). This restraint maintains the clean, modern aesthetic while providing visual feedback for interactivity.

## 7. Do's and Don'ts

### Do
- Use **Pin Sans** exclusively across all UI text for consistent brand voice
- Maintain minimum `48px` height for all interactive elements to ensure touch accessibility
- Apply `16px` border radius to buttons and standard input fields for friendly, modern appearance
- Use the primary red (`#D72323`) sparingly for high-priority CTAs only—not for generic actions
- Combine bold `700` weight typography with generous whitespace to create visual hierarchy
- Test color combinations for sufficient contrast ratio (WCAG AA minimum 4.5:1 for text)
- Layer shadows subtly using `rgba(0, 0, 0, 0.08–0.15)` for depth without heaviness
- Ensure secondary buttons and neutral elements use `#E5E5E0` to maintain visual distinction from primary actions
- Apply focus states with `box-shadow` rings rather than outline borders for modern appearance
- Scale spacing using the `4px` base unit to maintain rhythm and consistency

### Don't
- Mix multiple font families—Pin Sans is the only typeface in this system
- Use light text on light backgrounds or dark text on dark backgrounds without testing contrast
- Apply the red primary color (`#D72323`) to non-interactive elements or disabled states
- Create buttons smaller than `48px` height—this violates mobile touch target minimums
- Use harsh, high-opacity shadows (`rgba(0, 0, 0, 0.3+)`) that create visual clutter
- Overlap the purple accent palette excessively—reserve these for category highlights only
- Forget to include focus states and keyboard navigation indicators on interactive elements
- Set font weight to 400 on headline elements—headings must use `700` weight
- Create buttons with borders thicker than `2px` or highly saturated outline colors
- Deviate from the established spacing scale—custom spacing creates visual chaos

## 8. Responsive Behavior

### Breakpoints

| Breakpoint Name | Width | Primary Changes |
|-----------------|-------|-----------------|
| Mobile | `< 600px` | Single column layout, `16px` container padding, `12px` section margins, full-width cards, stacked navigation |
| Tablet | `600px–1024px` | Two-column grid, `32px` container padding, `24px` section margins, 6-column grid system, collapsible navigation |
| Desktop | `1024px–1440px` | Three-column grid, `48px` container padding, `40px` section margins, 12-column grid system, full horizontal navigation |
| Large Desktop | `> 1440px` | Wider gutters, max content width `1440px` maintained, expanded whitespace, hero sections reach full screen |

### Touch Targets
- Minimum interactive element size: `48px × 48px` (buttons, icon buttons, link areas)
- Minimum button text size: `12px` (Pin Sans, 400 weight)
- Minimum spacing between touch targets: `8px` (to prevent accidental activation)
- Form inputs: `48px` height, `16px` font size for comfortable tap interaction
- Icon buttons: Square `48px` container with centered icon

### Collapsing Strategy
- **Mobile (< 600px):** Navigation collapses to hamburger menu, content stacks vertically, button widths expand to fill container, section margins reduce to `12px`, border radius on cards tightens to `12px`, hero sections reduce to single column with stacked imagery
- **Tablet (600px–1024px):** Navigation expands to horizontal tabs, two-column grid activates, card widths adjust to container, spacing increases proportionally to screen size
- **Desktop (1024px+):** Full multi-column layout activates, spacing reaches maximum scale, containers maintain max-width `1440px`, navigation displays fully expanded with all options visible, section margins expand to `64px`

## 9. Agent Prompt Guide

### Quick Color Reference
- **Primary CTA Button:** Brand Red (`#D72323`)
- **Primary Text/Headings:** Dark Gray (`#3E3636`)
- **Secondary Button:** Light Gray (`#E5E5E0`)
- **Body Text:** True Black (`#000000`)
- **Secondary Text:** Warm Gray (`#62625B`)
- **Background:** True White (`#FFFFFF`)
- **Subtle Background:** Off-White (`#F6F6F3`)
- **Error State:** Error Red (`#DD0E0E`)
- **Success State:** Success Green (`#02B70E`)
- **Input Border:** Input Border (`#919190`)
- **Focus Ring:** Dark Gray with 0.1–0.2 opacity
- **Accent/Purple Highlights:** Purple Gradient (`#583B91` to `#774FC4`)

### Iteration Guide

1. **Font consistency:** All text uses Pin Sans exclusively; headings require `700` weight, body/buttons use `400` weight.

2. **Color priority:** Primary red (`#D72323`) reserved for single high-priority CTA per section; secondary actions use light gray (`#E5E5E0`); text defaults to dark brand (`#3E3636`) or pure black (`#000000`).

3. **Interactive sizing:** Every interactive element must be minimum `48px` height/width; buttons and inputs use standard `48px` height; add internal padding `6px 14px` for text buttons.

4. **Spacing scale:** All margins and padding align to `4px` base unit; common values are `8px`, `12px`, `16px`, `24px`, `32px`, `40px`, `64px`, `100px`, `120px`; never use arbitrary pixel values.

5. **Border radius:** Buttons and inputs use `16px`; cards use `12px`; pills/toggles use `999px`; flat elements use `0px`.

6. **Shadow/Elevation:** Use soft shadows only on hover/active states; apply `box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.08)` as default card shadow, increase to `0px 4px 12px rgba(0, 0, 0, 0.12)` on hover; avoid harsh shadows.

7. **Focus states:** Every interactive element requires focus ring using `box-shadow: 0px 0px 0px 2px–3px rgba(33, 25, 34, 0.15–0.2)`; never rely on default browser outline.

8. **Form inputs:** Use `16px` font size, `11px 15px` padding, `#FFFFFF` background, `1px solid #919190` border, `16px` border radius; maintain consistent height across input types.

9. **Typography hierarchy:** Use `24px 700` for hero/H1, `16px 700` for H2, `14px 700` for body, `12px 400` for labels/buttons; line heights are `26.4px` (H1), `22.4px` (H2), `normal` (buttons), `18px` (captions).

10. **Responsive behavior:** Maintain `16px` minimum container padding on mobile, scale to `32px–64px` on desktop; use `12`-column grid at desktop with responsive collapsing; all breakpoint changes follow the breakpoint table (< 600px mobile, 600–1024px tablet, > 1024px desktop).