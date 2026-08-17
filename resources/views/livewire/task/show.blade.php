<div class="mx-auto w-full max-w-3xl flex-1">
    <div class="mb-6 flex items-center gap-3">
        <a
            href="{{ route('tasks.index', $taskList) }}"
            wire:navigate
            class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
        >
            ← Back to {{ $taskList->name }}
        </a>
    </div>

    @php
        $borderColor = \App\Models\TaskList::COLORS[$taskList->color ?? \App\Models\TaskList::defaultColor()] ?? '#6366f1';
        $dueClasses = $task->is_overdue
            ? 'text-rose-600 dark:text-rose-400 font-medium'
            : ($task->is_upcoming
                ? 'text-amber-600 dark:text-amber-400 font-medium'
                : 'text-slate-400 dark:text-slate-500');
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/[.035]">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl font-semibold tracking-tight text-slate-900 dark:text-white {{ $task->completed_at ? 'line-through decoration-slate-400' : '' }}">
                        {{ $task->name }}
                    </h1>
                    <span class="rounded-md px-2 py-0.5 text-xs font-semibold {{ \App\Models\Task::PRIORITY_BADGE_CLASSES[$task->priority] ?? \App\Models\Task::PRIORITY_BADGE_CLASSES[1] }}">
                        {{ \App\Models\Task::PRIORITY_LABELS[$task->priority] ?? 'Medium' }}
                    </span>
                    @if($task->trashed())
                        <span class="rounded-md bg-zinc-50 px-2 py-0.5 text-xs font-semibold text-zinc-700 dark:bg-zinc-400/10 dark:text-zinc-200">In Trash</span>
                    @elseif($task->completed_at)
                        <span class="rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-200">Completed</span>
                    @endif
                </div>

                <p class="mt-1 flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                    <span class="size-2.5 rounded-full" style="background: {{ $borderColor }}"></span>
                    {{ $taskList->name }}
                </p>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @if(!$task->trashed())
                    @if(!$task->completed_at)
                        <flux:button size="sm" variant="primary" wire:click="complete" icon="check">
                            Complete
                        </flux:button>
                    @else
                        <flux:button size="sm" variant="ghost" wire:click="reopen">
                            Reopen
                        </flux:button>
                    @endif
                    <flux:button size="sm" variant="ghost" wire:click="$dispatch('open-edit-task', { taskId: {{ $task->id }} })" icon="pencil-square">
                        Edit
                    </flux:button>
                @else
                    <flux:button size="sm" variant="ghost" wire:click="restore" icon="arrow-uturn-left">
                        Restore
                    </flux:button>
                    <flux:button size="sm" variant="danger" wire:click="forceDelete" wire:confirm="Permanently delete this task? This cannot be undone." icon="trash">
                        Delete forever
                    </flux:button>
                @endif
            </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200 p-4 dark:border-white/10">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Priority</p>
                <p class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ \App\Models\Task::PRIORITY_LABELS[$task->priority] ?? 'Medium' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 dark:border-white/10">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Deadline</p>
                <p class="mt-1 text-sm font-medium {{ $task->deadline ? $dueClasses : 'text-slate-500 dark:text-slate-400' }}">
                    {{ $task->deadline ? ($task->is_overdue ? 'Overdue '.$task->deadline->format('M j, Y') : 'Due '.$task->deadline->format('M j, Y')) : 'No deadline' }}
                </p>
            </div>
            <div class="rounded-xl border border-slate-200 p-4 dark:border-white/10">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Created</p>
                <p class="mt-1 text-sm font-medium text-slate-900 dark:text-white">{{ $task->created_at->format('M j, Y') }}</p>
            </div>
        </div>

        @if($task->description)
            <div class="mt-6">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Description</p>
                <p class="mt-2 whitespace-pre-wrap text-sm leading-7 text-slate-700 dark:text-slate-300">{{ $task->description }}</p>
            </div>
        @endif
    </div>

    @if(!$task->trashed())
        <div class="mt-4 flex justify-end">
            <flux:button
                size="sm"
                variant="danger"
                wire:click="delete"
                wire:confirm="Move this task to Trash? You can restore it later."
                icon="trash"
            >
                Delete
            </flux:button>
        </div>
    @endif

    <livewire:task.edit />
</div>