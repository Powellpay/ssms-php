# SSMS Frontend — Design Guidelines

> Reference implementation: `php-starter/` (Powellpay/ssms-php)  
> React + TypeScript + Tailwind CSS v4  
> This document is the **single source of truth** for all UI decisions. Every component, page, and style must conform.

---

## 1. Color Palette

### Brand Colors
```
Primary Green:     #1f6f43  (green-700 equivalent)
Primary Dark:      #154d2e  (green-900 equivalent)  
Background:        #f4f6f5  (gray-50 with green tint)
Card Background:   #ffffff
Text:              #1c1c1c  (near-black)
Muted Text:        #6b7280  (gray-500)
Border:            #e2e5e3  (light gray-green)
```

### Grade Colors (A–E grading)
```
Grade A (Exceptional):  #1f6f43  — green, bold
Grade B (Outstanding):  #2f7d32  — medium green, bold
Grade C (Satisfactory): #b8860b  — dark yellow/gold, bold
Grade D (Basic):        #c2410c  — orange, bold
Grade E (Elementary):   #b3261e  — red, bold
```

### Semantic Colors
```
Error Alert Background:  #fdecea  /  Border: #f5c2bf  /  Text: #b3261e
Success Alert Background: #e8f5e9  /  Border: #bfe3c8  /  Text: #1f6f43
```

### Tailwind v4 Theme Setup
In `src/index.css`:
```css
@import "tailwindcss";

@theme {
  --color-primary: #1f6f43;
  --color-primary-dark: #154d2e;
  --color-primary-light: #e8f5e9;
  --color-bg: #f4f6f5;
  --color-muted: #6b7280;
  --color-border: #e2e5e3;

  --color-grade-a: #1f6f43;
  --color-grade-b: #2f7d32;
  --color-grade-c: #b8860b;
  --color-grade-d: #c2410c;
  --color-grade-e: #b3261e;

  --color-alert-error-bg: #fdecea;
  --color-alert-error-border: #f5c2bf;
  --color-alert-error-text: #b3261e;
  --color-alert-success-bg: #e8f5e9;
  --color-alert-success-border: #bfe3c8;
  --color-alert-success-text: #1f6f43;

  --font-sans: -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
}
```

---

## 2. Layout Structure

### Page Shell
```
┌──────────────────────────────────────────────┐
│  Top Bar (bg-primary-dark, text-white)       │
│  Brand left · Nav links right · User pill   │
├──────────────────────────────────────────────┤
│                                              │
│  Container (max-w-[1100px] mx-auto p-4)     │
│                                              │
│  ┌────────── Card ──────────────────────┐   │
│  │  Content here                        │   │
│  └──────────────────────────────────────┘   │
│                                              │
│  ┌────────── Card ──────────────────────┐   │
│  │  More content                        │   │
│  └──────────────────────────────────────┘   │
│                                              │
├──────────────────────────────────────────────┤
│  Footer (text-center, text-muted, text-sm)   │
└──────────────────────────────────────────────┘
```

### Top Bar Component
```tsx
// Shared/Navigation/TopBar.tsx
<header className="bg-primary-dark text-white flex items-center justify-between px-6 py-3 flex-wrap gap-2">
  <div className="font-bold">SSMS - School Management System</div>
  <nav className="flex items-center gap-4 text-sm">
    <a href="/dashboard">Dashboard</a>
    <a href="/students">Students</a>
    <a href="/assessment">Assessment</a>
    <a href="/reports">Reports</a>
    <span className="bg-primary px-2 py-0.5 rounded-full text-xs">
      username (role)
    </span>
    <a href="/logout">Logout</a>
  </nav>
</header>
```

### Container & Footer
```tsx
<main className="max-w-[1100px] mx-auto my-6 px-4">
  {/* page content */}
</main>
<footer className="text-center text-muted text-xs py-4">
  School Management System — Uganda New Lower Secondary Curriculum © {year}
</footer>
```

---

## 3. Component Library

### Card
```tsx
// Shared/components/ui/Card.tsx
<div className="bg-white border border-border rounded-lg p-5 mb-5">
  {children}
</div>
```

### Table
```tsx
<table className="w-full border-collapse bg-white">
  <thead>
    <tr className="bg-[#eef3f0]">
      <th className="border border-border p-2 text-left text-sm">Header</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td className="border border-border p-2 text-sm">Data</td>
    </tr>
    <tr>
      <td colSpan={6} className="text-center text-muted">No records found.</td>
    </tr>
  </tbody>
</table>
```

### Button
```tsx
// Variants: primary, secondary, ghost
<button className="inline-block bg-primary text-white border-none px-4 py-2 rounded-md cursor-pointer text-sm hover:bg-primary-dark">
  Click Me
</button>
```

### Form Inputs
```tsx
// All inputs share the same style:
<label className="block mb-1 font-semibold text-sm">Field Label</label>
<input className="w-full p-2 mb-3 border border-border rounded-md text-sm" />
<select className="w-full p-2 mb-3 border border-border rounded-md text-sm" />
<textarea className="w-full p-2 mb-3 border border-border rounded-md text-sm" />
```

### Alert
```tsx
// Error alert:
<div className="bg-alert-error-bg text-alert-error-text border border-alert-error-border rounded-md px-4 py-2 mb-4 text-sm">
  Error message here
</div>

// Success alert:
<div className="bg-alert-success-bg text-alert-success-text border border-alert-success-border rounded-md px-4 py-2 mb-4 text-sm">
  Success message here
</div>
```

### Grade Badge
```tsx
<span className="font-bold text-grade-a">A</span>  // Replace a with b/c/d/e
```

### User Pill (top bar)
```tsx
<span className="bg-primary text-white px-2 py-0.5 rounded-full text-xs">
  username (role)
</span>
```

---

## 4. Page Templates

### Dashboard Page
```
┌──────────────────────────────────────────────┐
│  Top Bar                                     │
├──────────────────────────────────────────────┤
│  Welcome, Username                           │
│  Current period: Term 1, 2026               │
│                                              │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐       │
│  │  45  │ │  12  │ │   8  │ │  13  │       │
│  │Students│ │ Staff │ │Streams│ │Subjects│   │
│  └──────┘ └──────┘ └──────┘ └──────┘       │
│                                              │
│  ┌── Quick links ────────────────────────┐   │
│  │ [Manage Students] [New Curriculum     │   │
│  │  Reports]                             │   │
│  └───────────────────────────────────────┘   │
├──────────────────────────────────────────────┤
│  Footer                                      │
└──────────────────────────────────────────────┘
```

### List Page (e.g., Students)
```
┌──────────────────────────────────────────────┐
│  Top Bar                                     │
├──────────────────────────────────────────────┤
│  Students                                    │
│  [+ Add Student]                             │
│                                              │
│  ┌── Card ──────────────────────────────┐   │
│  │  ┌────────── Table ───────────────┐  │   │
│  │  │ Adm No │ Name │ Gender │ Class │  │   │
│  │  ├────────┼──────┼────────┼───────┤  │   │
│  │  │ S26-001│ Faith│ Female │ S1 A  │  │   │
│  │  └────────┴──────┴────────┴───────┘  │   │
│  └──────────────────────────────────────┘   │
├──────────────────────────────────────────────┤
│  Footer                                      │
└──────────────────────────────────────────────┘
```

### Form Page (e.g., Add Student)
```
┌──────────────────────────────────────────────┐
│  Top Bar                                     │
├──────────────────────────────────────────────┤
│  Add Student                                 │
│                                              │
│  ┌── Card (max-w-[480px]) ──────────────┐   │
│  │  Admission No: [_______________]      │   │
│  │  First Name:   [_______________]      │   │
│  │  Last Name:    [_______________]      │   │
│  │  Gender:       [Female ▼       ]      │   │
│  │  Date of Birth:[_______________]      │   │
│  │  [Save Student]                       │   │
│  └───────────────────────────────────────┘   │
├──────────────────────────────────────────────┤
│  Footer                                      │
└──────────────────────────────────────────────┘
```

### View/Profile Page (e.g., Student Profile)
```
┌──────────────────────────────────────────────┐
│  Top Bar                                     │
├──────────────────────────────────────────────┤
│  Faith Achieng                               │
│                                              │
│  ┌── Card ──────────────────────────────┐   │
│  │  Admission No: S26-0001              │   │
│  │  Class: S1 A                         │   │
│  │  Gender: Female                      │   │
│  │  Date of Birth: 2012-03-14           │   │
│  │  Status: active                      │   │
│  └──────────────────────────────────────┘   │
│                                              │
│  ┌── Card ──────────────────────────────┐   │
│  │  Guardians                           │   │
│  │  • Moses Achieng (Father) - +256...  │   │
│  └──────────────────────────────────────┘   │
│                                              │
│  [View New Curriculum Report]               │
├──────────────────────────────────────────────┤
│  Footer                                      │
└──────────────────────────────────────────────┘
```

### Login Page
```
┌──────────────────────────────────────────────┐
│  Top Bar (simplified or hidden)              │
├──────────────────────────────────────────────┤
│                                              │
│       ┌── Card (max-w-[380px], centered) ┐   │
│       │  Sign in                          │   │
│       │  [error message if any]           │   │
│       │  Username: [_______________]       │   │
│       │  Password: [_______________]       │   │
│       │  [Login]                          │   │
│       │  Default: admin / ChangeMe123!    │   │
│       └──────────────────────────────────┘   │
│                                              │
├──────────────────────────────────────────────┤
│  Footer                                      │
└──────────────────────────────────────────────┘
```

---

## 5. Report Card Layout (Most Complex Page)

The report card is the signature deliverable. It must render a print-friendly layout.

```
┌──────────────────────────────────────────────┐
│  [← Back to report selector]  [Print/Save]  │
├──────────────────────────────────────────────┤
│  ┌── Report Card ────────────────────────┐   │
│  │                                        │   │
│  │  ┌── Report Header (centered) ────┐   │   │
│  │  │  Your School Name Here          │   │   │
│  │  │  P.O. Box ..., Kampala, Uganda │   │   │
│  │  │  Learner Progress Report        │   │   │
│  │  │  Term 1, 2026                  │   │   │
│  │  └────────────────────────────────┘   │   │
│  │                                        │   │
│  │  ┌── Report Meta (2-col grid) ────┐   │   │
│  │  │  Learner: Faith Achieng        │   │   │
│  │  │  Admission: S26-0001           │   │   │
│  │  │  Class: S1 A                   │   │   │
│  │  │  Term: Term 1, 2026           │   │   │
│  │  └────────────────────────────────┘   │   │
│  │                                        │   │
│  │  Subject Performance                   │   │
│  │  ┌── Table ───────────────────────┐   │   │
│  │  │ Subject│CA│EoT│Final│Grade│...│   │   │
│  │  ├────────┼──┼───┼─────┼─────┼───┤   │   │
│  │  │English │16│60 │ 76  │  B  │...│   │   │
│  │  │Maths   │18│68 │ 86  │  A  │...│   │   │
│  │  └────────┴──┴───┴─────┴─────┴───┘   │   │
│  │                                        │   │
│  │  Generic Skills Assessment             │   │
│  │  ┌── Skills Grid (auto-fit) ──────┐   │   │
│  │  │ ┌─Skill Card──┐ ┌─Skill Card─┐│   │   │
│  │  │ │Communicat'n │ │Cooperation ││   │   │
│  │  │ │Proficient   │ │Mastery     ││   │   │
│  │  │ └─────────────┘ └────────────┘│   │   │
│  │  └───────────────────────────────┘   │   │
│  │                                        │   │
│  │  Attendance                            │   │
│  │  Days Present: 58  Days Absent: 2     │   │
│  │                                        │   │
│  │  Comments                              │   │
│  │  ┌── Comment Box ─────────────────┐   │   │
│  │  │  Class Teacher: ...            │   │   │
│  │  └────────────────────────────────┘   │   │
│  │  ┌── Comment Box ─────────────────┐   │   │
│  │  │  Head Teacher: ...             │   │   │
│  │  └────────────────────────────────┘   │   │
│  │                                        │   │
│  │  Grading Key                           │   │
│  │  ┌── Table ───────────────────────┐   │   │
│  │  │ Grade │ Descriptor │ Range     │   │   │
│  │  ├───────┼────────────┼───────────┤   │   │
│  │  │ A     │ Exceptional│ 80-100   │   │   │
│  │  │ B     │ Outstanding│ 70-79.99 │   │   │
│  │  └───────┴────────────┴───────────┘   │   │
│  │  Skills scale: 1=Beginning, 2=...     │   │
│  └────────────────────────────────────────┘   │
└──────────────────────────────────────────────┘
```

### Report Card CSS (for print)
```css
@media print {
  .topbar, .footer, .no-print { display: none !important; }
  body { background: #fff; }
  .container { max-width: 100%; margin: 0; padding: 0; }
  .report-card { border: none; padding: 0; }
  table { font-size: 0.85rem; }
}
```

---

## 6. Responsive Behavior

| Breakpoint | Behavior |
|------------|----------|
| **≥1100px** | Container at max-width, centered |
| **768–1100px** | Full width with 1rem padding |
| **<768px** | Stack cards vertically, tables get horizontal scroll, skills grid goes single column, top bar nav wraps |
| **Print** | Top bar + footer hidden, report card borderless, full bleed |

---

## 7. Naming Conventions

| Item | Convention | Example |
|------|-----------|---------|
| **Components** | PascalCase, one per file | `StudentList.tsx`, `ReportCard.tsx` |
| **CSS classes** | Tailwind utility classes only | No custom CSS files |
| **Colors** | Use theme tokens | `bg-primary` not `bg-[#1f6f43]` |
| **Files** | kebab-case for directories | `student-list/`, `report-card/` |
| **Exports** | Named exports preferred | `export function StudentList()` |
| **Props interface** | Define above component | `interface StudentListProps` |

---

## 8. State Management Rules

| Concern | Tool |
|---------|------|
| Server state / API data | **TanStack React Query only** — never in Redux |
| Global UI state | Redux Toolkit slices (`authSlice`, `uiSlice`) |
| Component-local state | `useState` / `useReducer` |
| Form state | React Hook Form + Zod validation |
| API responses in Redux | **Never** — use React Query cache |

---

## 9. Empty States

Every list/table must handle the empty state:

```tsx
{items.length === 0 ? (
  <tr><td colSpan={6} className="text-center text-muted">No records found.</td></tr>
) : (
  items.map(item => <tr>...</tr>)
)}
```

Every report section must handle missing data gracefully — show `Not yet recorded.` or `No results yet.` rather than breaking.

---

## 10. File Organization

```
src/modules/{module}/
  api/
    {module}Queries.ts    — React Query hooks
    {module}Types.ts      — TypeScript interfaces
  ui/
    {Module}List.tsx      — List page
    {Module}Form.tsx      — Create/Edit form
    {Module}View.tsx      — Detail/Profile page
  index.ts                — Barrel exports

src/shared/
  api/endpoints/endpoints.ts  — API_ENDPOINTS object
  components/
    ui/                       — Card, Button, Table, Modal, etc.
    Navigation/               — TopBar, Sidebar, Footer
    layout/                   — PageLayout, Container
  types/                      — Shared interfaces
  utils/                      — cn(), formatters, validators
```

---

## 11. Print / PDF

The report card must have a **Print / Save as PDF** button that triggers `window.print()`. The print stylesheet hides navigation and footer, and makes the report card full-bleed with no borders.

---

## 12. Colour-Coded Grades

Wherever a grade (A–E) appears in the UI, it MUST use the corresponding grade color:

```tsx
function GradeBadge({ grade }: { grade: string }) {
  const colors: Record<string, string> = {
    A: 'text-grade-a', B: 'text-grade-b', C: 'text-grade-c',
    D: 'text-grade-d', E: 'text-grade-e',
  };
  return <span className={`font-bold ${colors[grade] || ''}`}>{grade}</span>;
}
```

---

> **Every component, every page, every style must follow this guide.**  
> If it's not in this document, don't invent it — ask.
