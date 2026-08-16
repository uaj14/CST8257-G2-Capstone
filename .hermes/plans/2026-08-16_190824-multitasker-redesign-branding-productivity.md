# Multitasker Redesign, Branding, and Productivity Implementation Plan

> **For Hermes:** Execute this plan sequentially. Keep each phase on `feature/frontend-ui`, test before every commit, and do not add optional features until the preceding quality gate passes.

**Goal:** Turn the current Laravel starter-kit UI into a branded, responsive, deployment-ready Multitasker product; strengthen the existing task workflow; then add completion/archive, quick-add, Pomodoro, and optional sound feedback without destabilizing authentication or drag-to-reorder.

**Architecture:** Keep Laravel/Livewire as the source of truth for user data and task actions. Use Flux UI for accessible server-driven controls and Alpine/vanilla JavaScript only for temporary client interactions such as drag feedback, Pomodoro timing, and opt-in sounds. Add features in release gates so the submission-critical branding and design work cannot be blocked by optional productivity extras.

**Tech Stack:** Laravel 13, Livewire 4, Flux UI Free 2, Alpine.js, Tailwind CSS 4, Vite 8, Pest 4, PHPStan/Larastan, SQLite locally.

---

## Confirmed baseline

- Drag-and-drop and the drop indicator are fixed, committed, and pushed through commit `1d93d86` on `feature/frontend-ui`.
- Working tree was clean when this plan was written.
- The application is still visibly based on the Laravel starter kit:
  - app name defaults to `Laravel`;
  - the Laravel logo remains in `resources/views/components/app-logo-icon.blade.php`;
  - `resources/views/welcome.blade.php` is the stock Laravel landing page;
  - sidebar links still point to the starter-kit repository and Laravel documentation.
- Current task capabilities: create, edit, permanent delete, priority, due date, description, and drag reorder.
- Missing task states: completed, archived, restored.
- There is no quick-add workflow, Pomodoro timer, or feedback-sound preference.
- Existing automated coverage focuses on authentication, dashboard access, and policies; workflow coverage is minimal.

## Scope and priority

### Release Gate A — submission critical (must finish first)

1. Brand decisions and design tokens.
2. Multitasker logo/name/favicon/metadata.
3. Branded public landing page.
4. Responsive app-shell, dashboard, task-list, forms, empty states, and accessibility pass.
5. Regression testing of all existing CRUD and drag features.

### Release Gate B — high-value workflow improvements

6. Complete/reopen tasks.
7. Archive/restore tasks while preserving permanent delete as a separate destructive action.
8. Active/completed/archived filters.
9. Quick-add tasks to the current list.

### Release Gate C — optional productivity enhancements

10. Pomodoro timer.
11. Optional sound feedback.

Do not begin Gate C unless Gate A and Gate B are passing Pint, PHPStan, Pest, Vite build, and responsive manual checks. Pomodoro and sounds are not worth risking the design and reliability requirements.

## Product decisions required before visual implementation

The user owns these choices; do not silently pick them:

1. **Logo direction:** task/checkmark mark, stacked cards/lists mark, or wordmark-led identity.
2. **Primary brand family:** blue/indigo, violet/indigo, or another user-selected palette.
3. **Brand voice:** recommended default is calm, focused, direct, and professional.
4. **Archive semantics:** recommended behavior is reversible archive; permanent deletion remains separately confirmed.
5. **Pomodoro scope:** recommended first version is a local 25/5 timer with start/pause/reset and optional task label—no analytics or database history.
6. **Sound behavior:** recommended default is off, user-enabled, subtle, and used only for successful add/complete/timer-finish events. Never autoplay sound without user interaction.

---

## Task 1: Establish a visual baseline and approve brand direction

**Objective:** Capture the current product at phone, tablet, and desktop sizes and lock the brand direction before changing UI.

**Files:** None.

**Steps:**

1. Run the app with `composer run dev`.
2. Capture these screens in light and dark mode:
   - `/` landing page;
   - `/login`;
   - `/dashboard` with seeded lists;
   - one populated `/task-lists/{id}/tasks` page;
   - create/edit flyouts.
3. Use viewport widths 375px, 768px, and 1440px.
4. Record visible starter-kit elements, overflow, weak hierarchy, low contrast, inconsistent spacing, and dead links.
5. Present 2–3 logo/palette directions to the user; do not implement one without selection.
6. Define acceptance screenshots for the final comparison.

**Quality gate:** User approves one logo direction, one primary palette, and the landing-page tone.

---

## Task 2: Add shared brand tokens and app identity

**Objective:** Make all pages use one documented visual system and the Multitasker name.

**Files:**
- Modify: `resources/css/app.css`
- Modify: `config/app.php`
- Modify: `.env.example`
- Local-only modify: `.env` (`APP_NAME=Multitasker`)
- Modify: `composer.json` metadata only; do not change dependencies
- Test: `tests/Feature/LandingPageTest.php`

**Implementation:**

1. Add CSS-first Tailwind tokens in `@theme` for the approved primary, accent, surface, muted, success, warning, and danger colors.
2. Map Flux variables `--color-accent`, `--color-accent-content`, and `--color-accent-foreground` to the approved primary in both light and dark themes.
3. Define consistent radii, shadows, focus rings, page widths, and spacing utilities without creating a second CSS framework.
4. Change the fallback application name in `config/app.php` from `Laravel` to `Multitasker`.
5. Set `APP_NAME=Multitasker` in `.env.example` and local `.env`; never commit secrets from `.env`.
6. Update `composer.json` package name/description from starter-kit metadata to project metadata without adding packages.
7. Add a Pest feature test asserting page titles and public brand text contain `Multitasker`, not `Laravel`.

**Verification:**

```bash
php artisan test --compact tests/Feature/LandingPageTest.php
npm run build
```

Expected: test passes; Vite emits production assets; no Laravel name remains in user-facing titles.

**Commit:** `Brand Multitasker application identity and theme tokens`

---

## Task 3: Create a reusable Multitasker logo system

**Objective:** Replace every Laravel mark with an original, scalable Multitasker identity.

**Files:**
- Modify: `resources/views/components/app-logo-icon.blade.php`
- Modify: `resources/views/components/app-logo.blade.php`
- Modify: `resources/views/layouts/auth/simple.blade.php`
- Modify: `resources/views/layouts/auth/card.blade.php`
- Modify: `resources/views/layouts/auth/split.blade.php`
- Replace: `public/favicon.svg`
- Replace or generate: `public/favicon.ico`
- Replace or generate: `public/apple-touch-icon.png`
- Modify: `resources/views/partials/head.blade.php`

**Implementation requirements:**

1. Build the approved SVG mark with `currentColor` where appropriate so it works in light/dark contexts.
2. Verify legibility at 16px, 24px, 32px, and hero size.
3. Keep icon-only and icon+wordmark uses consistent.
4. Add `theme-color`, description, and Open Graph basics to the head partial; avoid fabricated social images unless one is created.
5. Use accessible text labels; decorative SVGs should be hidden from screen readers when the adjacent wordmark already names the app.

**Verification:** Inspect landing, auth, sidebar, mobile header, browser tab, and saved-home-screen icon. Run `npm run build`.

**Commit:** `Add Multitasker logo and application icons`

---

## Task 4: Replace the stock landing page

**Objective:** Explain what Multitasker is, why it is useful, and lead visitors clearly to registration or login.

**Files:**
- Rewrite: `resources/views/welcome.blade.php`
- Modify: `routes/web.php` only if a named anchor/route is genuinely needed
- Test: `tests/Feature/LandingPageTest.php`

**Proposed landing structure:**

1. Accessible top navigation with logo, Log in, and Get started.
2. Hero:
   - short outcome-led headline (provisional: “Turn scattered tasks into a clear plan.”);
   - one-sentence explanation of lists, priorities, due dates, and flexible ordering;
   - primary registration CTA and secondary login CTA;
   - branded product-preview composition built from real UI patterns, not fake claims.
3. Three benefit sections:
   - organize work into colored lists;
   - decide what matters with priorities and dates;
   - adjust plans quickly through direct manipulation and quick-add.
4. “How it works” in three steps.
5. Trust statement: private account-owned data; users only access their own lists/tasks.
6. Final CTA and simple footer with project name/year; remove all Laravel ecosystem marketing.

**Responsive/accessibility requirements:**

- Mobile-first layout; no horizontal overflow at 320–375px.
- Semantic landmarks (`header`, `main`, `section`, `footer`).
- One H1, logical H2 sequence, keyboard-visible CTAs, WCAG AA contrast.
- Respect `prefers-reduced-motion`; no mandatory animation.
- Do not show app features that are not implemented by that release gate.

**Tests:**

- Guest sees the Multitasker headline and login/register links.
- Authenticated visitor gets a dashboard CTA rather than redundant registration copy.
- Page contains no visible “Laravel” starter text.

**Commit:** `Build branded responsive Multitasker landing page`

---

## Task 5: Redesign the authenticated app shell

**Objective:** Deliver a clean, professional, responsive shell that is clearly Multitasker rather than a starter kit.

**Files:**
- Modify: `resources/views/layouts/app/sidebar.blade.php`
- Modify or remove unused variant: `resources/views/layouts/app/header.blade.php`
- Modify: `resources/views/layouts/app.blade.php`
- Modify: `resources/views/components/desktop-user-menu.blade.php`
- Modify: `resources/views/partials/settings-heading.blade.php`
- Modify: `resources/css/app.css`

**Implementation:**

1. Remove starter-kit Repository and Documentation links.
2. Rename the generic “Platform” group to product navigation or remove the heading when Dashboard is the only item.
3. Give the main content a consistent max width, page padding, responsive gutters, and top hierarchy.
4. Add a clear desktop/mobile page-title pattern.
5. Keep settings and user-menu placement consistent.
6. Preserve Flux mobile-sidebar behavior and keyboard navigation.
7. Verify touch targets are at least approximately 44px where practical.
8. Keep dark mode fully supported; do not force `<html class="dark">` as the only theme.

**Verification:** 375px, 768px, 1024px, 1440px; light/dark; sidebar open/closed; keyboard tab order.

**Commit:** `Redesign responsive authenticated application shell`

---

## Task 6: Redesign dashboard and task-list presentation

**Objective:** Improve hierarchy and comprehension while preserving all working CRUD and drag behavior.

**Files:**
- Modify: `resources/views/livewire/task-list/index.blade.php`
- Modify: `resources/views/livewire/task/index.blade.php`
- Modify: `resources/views/livewire/task-list/create.blade.php`
- Modify: `resources/views/livewire/task-list/edit.blade.php`
- Modify: `resources/views/livewire/task/create.blade.php`
- Modify: `resources/views/livewire/task/edit.blade.php`
- Modify: `resources/css/app.css`
- Preserve carefully: `resources/js/app.js` drag logic

**Dashboard improvements:**

1. Add a concise greeting/summary and one dominant “New list” action.
2. Use responsive equal-height list cards with clear color identity, task count, and large card-wide navigation target.
3. Improve empty state with icon, explanation, and inline primary action.
4. Keep overflow menu above the stretched link and prevent click propagation.

**Task-list improvements:**

1. Show list name as the page H1 rather than only a breadcrumb.
2. Add summary counts (active/completed later), clear Add Task/Quick Add actions, and concise drag guidance.
3. Preserve the 4px drop indicator and event-delegated drag behavior.
4. Improve task cards: title, priority, due-date state (normal/due soon/overdue), description truncation, explicit drag handle, and predictable action menu.
5. Stack title/actions at narrow widths; badges and dates must wrap without overflow.
6. Make flyouts/full-screen mobile behavior easy to operate at 320–375px.

**Regression checklist:** create, edit, color, reorder repeatedly in both directions, reload persistence, menu actions, mobile layout, dark mode.

**Commit:** `Polish dashboard and task-list experience`

---

## Task 7: Add completion and archive data states

**Objective:** Let users finish and organize tasks without forcing permanent deletion.

**Files:**
- Create via Artisan: migration adding `completed_at` and `archived_at` nullable timestamps to `tasks`
- Modify: `app/Models/Task.php`
- Modify: `database/factories/TaskFactory.php`
- Modify: `app/Livewire/Task/Index.php`
- Modify: `resources/views/livewire/task/index.blade.php`
- Test: `tests/Feature/TaskWorkflowTest.php`

**Commands:**

```bash
php artisan make:migration add_completion_and_archive_state_to_tasks_table --table=tasks --no-interaction
php artisan make:test --pest TaskWorkflowTest --no-interaction
```

**Behavior:**

1. `completed_at` controls complete/reopen status.
2. `archived_at` controls active/archived visibility and is reversible.
3. Permanent delete remains separately named, confirmed, policy-authorized, and destructive.
4. Add Index actions: `toggleComplete(Task $task)`, `archive(Task $task)`, `restore(Task $task)`, `delete(Task $task)`.
5. Every action checks ownership and verifies the task belongs to the mounted list.
6. Add `active`, `completed`, and `archived` filter tabs with counts.
7. Reorder only the currently active, non-archived tasks; archived tasks must not corrupt active positions.
8. Completed tasks should remain readable, visually de-emphasized, and reopenable.

**Tests:**

- Owner can complete/reopen/archive/restore/delete.
- Another user cannot perform any state action.
- Archived tasks are excluded from active query and shown in archived filter.
- Reordering active tasks does not expose or renumber another user’s tasks.
- State persists after component refresh.

**Verification:**

```bash
php artisan test --compact tests/Feature/TaskWorkflowTest.php
```

**Commit:** `Add task completion and archive workflow`

---

## Task 8: Add quick task capture

**Objective:** Allow fast name-only task entry without opening the full flyout.

**Files:**
- Modify: `app/Livewire/Task/Index.php`
- Modify: `resources/views/livewire/task/index.blade.php`
- Test: `tests/Feature/TaskWorkflowTest.php`

**Behavior:**

1. Add `public string $quickTaskName = ''` and `quickAdd()` to the current list component.
2. Validate required string, maximum 255 characters.
3. Create through the authenticated user relationship.
4. Assign current list, next sequential position, medium/default priority, null description/deadline.
5. Clear the input, toast success, and keep keyboard focus ready for another entry.
6. Provide “Add details” through the existing full create flyout.
7. Enter submits; Escape clears; do not add global shortcuts that interfere with typing.

**Tests:** valid add, empty/oversize rejection, correct owner/list/position/default priority, cross-user isolation.

**Commit:** `Add quick task capture workflow`

---

## Task 9: Strengthen existing behavior and feedback

**Objective:** Ensure every current action is reliable and clearly communicated.

**Files:**
- Modify as needed: Livewire task/list components and views
- Test: extend `tests/Feature/TaskWorkflowTest.php` and add `tests/Feature/TaskListWorkflowTest.php`

**Checklist:**

1. Create/edit/delete/list-color/reorder state persists after hard refresh.
2. Every successful action provides a toast; errors remain field-specific.
3. Permanent deletion wording explicitly says it cannot be undone.
4. Empty, loading, disabled, and `wire:loading` states prevent duplicate submissions.
5. Overdue styling is derived from dates and is not shown on completed tasks.
6. Direct URLs cannot expose another user’s list/task.
7. Fix the malformed extra parenthesis currently visible in the Edit menu dispatch in `resources/views/livewire/task/index.blade.php`.
8. Add regression coverage for reorder authorization and valid ID ownership filtering.

**Commit:** `Harden task and list interactions`

---

## Task 10: Add a minimal Pomodoro timer (optional Gate C)

**Objective:** Add a useful, low-risk focus timer without creating a new server-side subsystem.

**Files:**
- Create: `resources/js/pomodoro.js`
- Modify: `resources/js/app.js`
- Create: `resources/views/components/pomodoro-timer.blade.php`
- Modify: `resources/views/layouts/app/sidebar.blade.php` or task-list toolbar after user approves placement
- Modify: `resources/css/app.css`

**Recommended v1 behavior:**

1. 25-minute focus and 5-minute break presets.
2. Start, pause, resume, reset, and skip.
3. Timer state stored in `localStorage` using an absolute end timestamp so refresh does not reset elapsed time.
4. Optional plain-text current-focus label; do not add task-session analytics yet.
5. Accessible remaining-time text; avoid updating an aggressive live region every second.
6. Respect reduced motion and visibility changes.
7. Timer continues accurately after tab sleep by recomputing from timestamps.

**No new dependency:** implement with Alpine/vanilla JS. Do not add a timer package.

**Manual tests:** start/pause/resume, refresh persistence, background tab, timer completion, mobile layout, reduced motion.

**Commit:** `Add lightweight Pomodoro focus timer`

---

## Task 11: Add optional sound feedback (optional Gate C)

**Objective:** Provide subtle feedback without harming accessibility or surprising users.

**Files:**
- Create: `resources/js/sound-feedback.js`
- Modify: `resources/js/app.js`
- Modify: `resources/views/livewire/settings/appearance.blade.php` or an approved preference location
- Modify: relevant Livewire views/events only after the setting exists

**Rules:**

1. Sound is off by default.
2. User must explicitly enable it; store preference in `localStorage` for v1.
3. Use Web Audio API synthesized tones to avoid asset licensing and downloads.
4. Use only for successful add, complete, and Pomodoro finish. Avoid sounds for destructive delete.
5. Never play on initial page load.
6. Handle browsers that suspend AudioContext until user interaction.
7. Add an immediately testable preview control next to the setting.
8. Provide visual toasts independently; sound never becomes the only feedback channel.

**Commit:** `Add opt-in interaction sounds`

---

## Task 12: Deployment-readiness and final quality gate

**Objective:** Produce a submission-ready, reproducible application with no starter-kit artifacts.

**Files likely to change:**
- `README.md` only if the team wants deployment/setup documentation updated
- `.env.example`
- `composer.json`
- `database/seeders/DatabaseSeeder.php`
- tests and any files identified during QA

**Automated verification:**

```bash
vendor/bin/pint --format agent
vendor/bin/phpstan analyse --memory-limit=512M
php artisan test --compact
npm run build
composer audit
npm audit
php artisan route:list --except-vendor
```

**Manual/CDP verification:**

1. Guest landing/auth flow.
2. Registration/login/logout/password reset UI.
3. Two-user isolation test by direct list URL.
4. Dashboard/list CRUD and color changes.
5. Quick add and full create/edit forms.
6. Complete/reopen/archive/restore/permanent delete.
7. At least five consecutive drag operations in both directions, including previously moved cards and visual indicator checks.
8. Pomodoro/sound only if included.
9. Light/dark at 375px, 768px, 1024px, and 1440px.
10. Keyboard-only navigation, focus visibility, touch targets, contrast, reduced motion.
11. Production configuration checklist: `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`, secure app key, writable storage/cache, production DB, mail config for password reset, and deployment cache commands.

**Acceptance criteria:**

- No visible Laravel logo/name/starter links.
- Original Multitasker logo and consistent color system across public, auth, and app surfaces.
- Landing page clearly communicates what the app is and why someone should use it.
- No horizontal overflow at 320–375px.
- Existing features plus completion/archive/quick-add work and persist.
- No cross-user data access.
- No required sound or motion.
- Pint, PHPStan, Pest, and Vite build all pass.

**Final commits:** one focused fix commit per QA issue; do not squash or rewrite team history unless explicitly requested.

---

## Risks and mitigations

1. **Feature creep:** Pomodoro and sounds can consume time without improving the professor’s stated branding/design requirements. Mitigation: Gate C stays optional.
2. **Drag regression during redesign:** task card markup and event boundaries are fragile. Mitigation: preserve `data-task-id`, root event delegation, morph hook, and CDP multi-drag test after every task-card edit.
3. **Archive ambiguity:** archive, completion, trash, and delete can confuse users. Mitigation: use distinct labels and filters; keep permanent delete confirmed and separate.
4. **Accessibility regression:** custom visuals/sounds can lower quality. Mitigation: AA contrast, focus-visible, reduced-motion, sound opt-in, semantic headings, keyboard verification.
5. **Database migration risk:** adding task states affects queries and reorder. Mitigation: nullable timestamps, ownership-filtered queries, targeted Pest tests, fresh migration/seed test.
6. **CSS conflicts with Flux:** dynamic borders already caused problems. Mitigation: central tokens, avoid fighting component shorthands, and verify computed styles via CDP when custom indicators are involved.

## Explicitly out of scope for this release

- Collaboration/sharing.
- Cloud notifications or reminders.
- Pomodoro analytics/history.
- Calendar integrations.
- Native/mobile app packaging.
- New paid Flux components or third-party UI dependencies.
- New dependencies without user/team approval.

## Recommended execution order

1. Task 1 decision gate.
2. Tasks 2–6 (brand, landing, layout, responsive design).
3. Run the full Gate A quality check and get team feedback.
4. Tasks 7–9 (completion/archive/quick-add/reliability).
5. Run the full Gate B quality check.
6. Only then decide whether Task 10 and/or Task 11 fit the remaining schedule.
7. Task 12 final deployment/submission pass.
