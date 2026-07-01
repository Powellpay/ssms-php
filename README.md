# SSMS Web — Frontend

**School Management System — React + TypeScript frontend**  
Aligned to Uganda's Competency-Based Curriculum (NCDC Lower Secondary)

## Tech Stack

React 19, TypeScript 6, Vite 8, Tailwind CSS v4, Redux Toolkit, TanStack React Query, React Router v7, Axios, Framer Motion, Recharts

## Quick Start

```bash
npm install
npm run dev
```

The dev server starts at `http://localhost:5173` and proxies `/api` requests to `http://127.0.0.1:8000` (Laravel backend).

## Project Structure

```
src/
├── main.tsx                  # Entry point (Redux + QueryClient + Router)
├── App.tsx                   # Root route definitions
├── index.css                 # Tailwind v4 theme tokens
├── app/
│   ├── api/axiosConfig.ts    # Axios instance (token interceptor, base URL)
│   ├── routes/constants.ts   # All route path constants
│   └── store/                # Redux Toolkit (authSlice, uiSlice)
├── modules/
│   ├── auth/                 # Login
│   ├── dashboard/            # Dashboard with module navigation grid
│   ├── academic/             # Years, Terms, Classes, Streams
│   ├── staff/                # Staff management
│   ├── students/             # Students, Guardians, Enrollments
│   ├── curriculum/           # Subjects, Themes, Outcomes, Skills
│   ├── assessment/           # Types, Grading, Records, Results
│   ├── reports/              # Report cards
│   ├── attendance/           # Daily register
│   ├── timetable/            # Weekly timetable
│   ├── finance/              # Fees, Invoices, Payments
│   ├── discipline/           # Conduct records
│   ├── library/              # Books, Loans
│   └── announcements/        # School notices
└── shared/
    ├── api/endpoints/        # API_ENDPOINTS constant map
    ├── components/           # Shared UI components
    ├── types/                # Shared TypeScript interfaces
    └── utils/                # cn() and other utilities
```

Each module has an `api/` directory for queries and a `ui/` directory for components.

## State Management

| Concern | Tool |
|---------|------|
| Server state & caching | TanStack React Query |
| Global UI state | Redux Toolkit (auth, ui slices) |
| Component state | useState / useReducer |

## Available Scripts

| Command | Description |
|---------|-------------|
| `npm run dev` | Start dev server with HMR |
| `npm run build` | Type-check + production build |
| `npm run preview` | Preview production build |
| `npm run lint` | Oxlint check |
| `npm run vera:fast` | Lint changed files only |
| `npm run vera:extended` | Full lint suite |

## Backend

The corresponding API is at `https://github.com/Powellpay/ssms-php` on the `opiyo-oscar/sms-api` branch.
