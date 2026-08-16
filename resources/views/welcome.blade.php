<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Plan clearly. Work calmly.')])
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white text-slate-950 antialiased dark:bg-[#08090a] dark:text-slate-50">
        <div class="relative isolate overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[42rem] bg-[radial-gradient(circle_at_20%_10%,rgba(99,102,241,.18),transparent_34%),radial-gradient(circle_at_80%_0%,rgba(139,92,246,.14),transparent_30%)] dark:bg-[radial-gradient(circle_at_20%_10%,rgba(99,102,241,.20),transparent_34%),radial-gradient(circle_at_80%_0%,rgba(139,92,246,.16),transparent_30%)]"></div>

            <header class="mx-auto flex w-full max-w-7xl items-center justify-between px-5 py-5 sm:px-8 lg:px-10">
                <a href="{{ route('home') }}" class="inline-flex min-h-11 items-center gap-3 rounded-xl font-semibold tracking-tight focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-indigo-500" aria-label="Multitasker home">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-linear-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-600/20">
                        <x-app-logo-icon class="size-7 text-white" />
                    </span>
                    <span class="text-lg">Multitasker</span>
                </a>

                <nav class="flex items-center gap-2 sm:gap-3" aria-label="Primary navigation">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                            Open dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-900/5 hover:text-slate-950 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white sm:px-4">
                            Log in
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                            Get started
                        </a>
                    @endauth
                </nav>
            </header>

            <main>
                <section class="mx-auto grid w-full max-w-7xl items-center gap-14 px-5 pb-20 pt-16 sm:px-8 sm:pt-24 lg:grid-cols-[1.02fr_.98fr] lg:gap-20 lg:px-10 lg:pb-28 lg:pt-28">
                    <div class="max-w-3xl">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50/80 px-3 py-1.5 text-xs font-semibold text-indigo-700 shadow-sm dark:border-indigo-400/20 dark:bg-indigo-400/10 dark:text-indigo-200">
                            <span class="size-1.5 rounded-full bg-indigo-500"></span>
                            A focused workspace for real plans
                        </div>
                        <h1 class="max-w-3xl text-balance text-5xl font-semibold leading-[1.04] tracking-[-0.045em] text-slate-950 dark:text-white sm:text-6xl lg:text-7xl">
                            Turn scattered tasks into a clear plan.
                        </h1>
                        <p class="mt-7 max-w-2xl text-pretty text-lg leading-8 text-slate-600 dark:text-slate-300 sm:text-xl">
                            Multitasker keeps projects, priorities, due dates, and next steps in one calm workspace—so you can spend less time organizing and more time finishing.
                        </p>
                        <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 text-base font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:-translate-y-0.5 hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 motion-reduce:hover:translate-y-0">
                                    Open dashboard
                                    <span aria-hidden="true">→</span>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-6 text-base font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:-translate-y-0.5 hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 motion-reduce:hover:translate-y-0">
                                    Get started
                                    <span aria-hidden="true">→</span>
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-200 bg-white/80 px-6 text-base font-semibold text-slate-800 shadow-sm backdrop-blur transition hover:border-slate-300 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500 dark:border-white/10 dark:bg-white/5 dark:text-slate-100 dark:hover:bg-white/10">
                                    Log in
                                </a>
                            @endauth
                        </div>
                        <div class="mt-8 flex flex-wrap gap-x-6 gap-y-3 text-sm text-slate-500 dark:text-slate-400">
                            <span class="inline-flex items-center gap-2"><span class="text-emerald-500">✓</span> Private to your account</span>
                            <span class="inline-flex items-center gap-2"><span class="text-emerald-500">✓</span> Priorities and due dates</span>
                            <span class="inline-flex items-center gap-2"><span class="text-emerald-500">✓</span> Flexible drag ordering</span>
                        </div>
                    </div>

                    <div class="relative mx-auto w-full max-w-xl lg:max-w-none" aria-label="Preview of a Multitasker task list">
                        <div class="absolute -inset-8 -z-10 rounded-[2.5rem] bg-linear-to-br from-indigo-500/20 via-violet-400/10 to-transparent blur-2xl"></div>
                        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white/90 shadow-[0_32px_80px_-36px_rgba(49,46,129,.45),0_16px_32px_-20px_rgba(15,23,42,.22)] backdrop-blur dark:border-white/10 dark:bg-[#111216]/95 dark:shadow-[0_32px_90px_-30px_rgba(0,0,0,.75)]">
                            <div class="flex items-center justify-between border-b border-slate-200/80 px-5 py-4 dark:border-white/10">
                                <div class="flex items-center gap-3">
                                    <span class="size-3 rounded-full bg-indigo-500"></span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900 dark:text-white">Launch plan</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">4 tasks · ordered by next action</p>
                                    </div>
                                </div>
                                <span class="rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-200">This week</span>
                            </div>
                            <div class="space-y-3 p-4 sm:p-5">
                                <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/[.035]">
                                    <span class="mt-1 text-slate-300 dark:text-slate-600" aria-hidden="true">☷</span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-semibold text-slate-900 dark:text-white">Review project requirements</p>
                                            <span class="rounded-md bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-700 dark:bg-rose-400/10 dark:text-rose-200">High</span>
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Confirm the final scope before implementation.</p>
                                        <p class="mt-2 text-xs font-medium text-slate-400">Due today</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/[.035]">
                                    <span class="mt-1 text-slate-300 dark:text-slate-600" aria-hidden="true">☷</span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="font-semibold text-slate-900 dark:text-white">Polish the responsive layout</p>
                                            <span class="rounded-md bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-400/10 dark:text-amber-200">Medium</span>
                                        </div>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Check phone, tablet, and desktop spacing.</p>
                                    </div>
                                </div>
                                <div class="relative flex items-start gap-3 rounded-2xl border border-indigo-200 bg-indigo-50/60 p-4 dark:border-indigo-400/20 dark:bg-indigo-400/[.06]">
                                    <span class="absolute inset-x-4 -top-2 h-1 rounded-full bg-indigo-500 shadow-[0_0_0_2px_rgba(99,102,241,.18)]"></span>
                                    <span class="mt-1 text-indigo-400" aria-hidden="true">☷</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-slate-900 dark:text-white">Prepare the group review</p>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Drag priorities into the order that matches the plan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="border-y border-slate-200/80 bg-slate-50/80 dark:border-white/10 dark:bg-white/[.025]" aria-labelledby="benefits-heading">
                    <div class="mx-auto w-full max-w-7xl px-5 py-20 sm:px-8 lg:px-10 lg:py-24">
                        <div class="max-w-2xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-300">Built for clarity</p>
                            <h2 id="benefits-heading" class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">A flexible system without the clutter.</h2>
                            <p class="mt-4 text-lg leading-8 text-slate-600 dark:text-slate-300">Simple enough for everyday tasks, structured enough for work that needs a real plan.</p>
                        </div>
                        <div class="mt-12 grid gap-5 md:grid-cols-3">
                            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/[.035]">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-indigo-50 text-xl text-indigo-600 dark:bg-indigo-400/10 dark:text-indigo-300">▤</span>
                                <h3 class="mt-5 text-lg font-semibold text-slate-950 dark:text-white">Separate what matters</h3>
                                <p class="mt-2 leading-7 text-slate-600 dark:text-slate-400">Use focused, color-coded lists to keep school, work, and personal plans easy to scan.</p>
                            </article>
                            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/[.035]">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-violet-50 text-xl text-violet-600 dark:bg-violet-400/10 dark:text-violet-300">◆</span>
                                <h3 class="mt-5 text-lg font-semibold text-slate-950 dark:text-white">Decide the next move</h3>
                                <p class="mt-2 leading-7 text-slate-600 dark:text-slate-400">Pair priorities with due dates and descriptions so every task carries the context you need.</p>
                            </article>
                            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/[.035]">
                                <span class="flex size-11 items-center justify-center rounded-xl bg-sky-50 text-xl text-sky-600 dark:bg-sky-400/10 dark:text-sky-300">↕</span>
                                <h3 class="mt-5 text-lg font-semibold text-slate-950 dark:text-white">Adapt without rebuilding</h3>
                                <p class="mt-2 leading-7 text-slate-600 dark:text-slate-400">Reorder tasks directly as priorities change. Your plan stays flexible and the new order stays saved.</p>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="mx-auto w-full max-w-7xl px-5 py-20 sm:px-8 lg:px-10 lg:py-28" aria-labelledby="workflow-heading">
                    <div class="grid items-start gap-14 lg:grid-cols-[.8fr_1.2fr] lg:gap-20">
                        <div class="lg:sticky lg:top-8">
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-indigo-600 dark:text-indigo-300">How it works</p>
                            <h2 id="workflow-heading" class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">From idea to action in three clear steps.</h2>
                            <p class="mt-4 text-lg leading-8 text-slate-600 dark:text-slate-300">Multitasker keeps the workflow direct: capture the work, give it context, then arrange it around what comes next.</p>
                        </div>
                        <ol class="space-y-5">
                            <li class="grid grid-cols-[auto_1fr] gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[.035] sm:p-6">
                                <span class="flex size-10 items-center justify-center rounded-full bg-indigo-600 font-semibold text-white">1</span>
                                <div><h3 class="text-lg font-semibold">Create focused lists</h3><p class="mt-1 leading-7 text-slate-600 dark:text-slate-400">Give each area of work a clear home and a color that makes it easy to recognize.</p></div>
                            </li>
                            <li class="grid grid-cols-[auto_1fr] gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[.035] sm:p-6">
                                <span class="flex size-10 items-center justify-center rounded-full bg-indigo-600 font-semibold text-white">2</span>
                                <div><h3 class="text-lg font-semibold">Add the context you need</h3><p class="mt-1 leading-7 text-slate-600 dark:text-slate-400">Capture the task, choose its priority, add details, and set a due date when timing matters.</p></div>
                            </li>
                            <li class="grid grid-cols-[auto_1fr] gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-white/[.035] sm:p-6">
                                <span class="flex size-10 items-center justify-center rounded-full bg-indigo-600 font-semibold text-white">3</span>
                                <div><h3 class="text-lg font-semibold">Keep the plan current</h3><p class="mt-1 leading-7 text-slate-600 dark:text-slate-400">Drag tasks into the order that matches your day. Multitasker saves the arrangement automatically.</p></div>
                            </li>
                        </ol>
                    </div>
                </section>

                <section class="px-5 pb-20 sm:px-8 lg:px-10 lg:pb-28">
                    <div class="mx-auto flex w-full max-w-7xl flex-col items-start justify-between gap-8 overflow-hidden rounded-3xl bg-[#17162f] px-6 py-10 text-white shadow-2xl shadow-indigo-950/20 sm:px-10 sm:py-12 lg:flex-row lg:items-center lg:px-14">
                        <div class="max-w-2xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-indigo-200">Start with the next task</p>
                            <h2 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">A clearer plan is one decision away.</h2>
                            <p class="mt-3 text-lg leading-8 text-indigo-100/80">Create an account and build a workspace that stays useful when plans change.</p>
                        </div>
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-xl bg-white px-6 font-semibold text-indigo-950 transition hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Open dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex min-h-12 shrink-0 items-center justify-center rounded-xl bg-white px-6 font-semibold text-indigo-950 transition hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Create your account</a>
                        @endauth
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-200/80 dark:border-white/10">
                <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-slate-500 dark:text-slate-400 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
                    <span class="inline-flex items-center gap-2 font-medium text-slate-700 dark:text-slate-300"><x-app-logo-icon class="size-5 text-indigo-500" /> Multitasker</span>
                    <span>Focused task management by CST8257 Group 2.</span>
                </div>
            </footer>
        </div>

        @fluxScripts
    </body>
</html>
