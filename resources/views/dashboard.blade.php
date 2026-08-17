<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-300">Workspace</p>
                <h1 class="truncate text-2xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">Dashboard</h1>
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Pick up where you left off, or create a new list.</p>
            </div>
            </div>

        <div class="rounded-2xl border border-neutral-200/80 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            <div class="px-5 pb-5 pt-5">
                <livewire:task-list.index />
            </div>
        </div>

        <livewire:task-list.create />
        <livewire:task-list.edit />
    </div>
</x-layouts::app>
