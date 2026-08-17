<div class="flex h-full w-full flex-1 flex-col gap-5 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">Trash</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                {{ $count }} {{ Str::plural('deleted task', $count) }} — restore or permanently delete them.
            </p>
        </div>
    </div>

    @if($count === 0)
        <div class="flex flex-1 flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-slate-300 dark:border-white/15 p-12 text-center">
            <p class="text-slate-500 dark:text-slate-400">Trash is empty.</p>
        </div>
    @else
        <div class="flex flex-col gap-6">
            @foreach($groups as $group)
                @php
                    $list = $group['taskList'];
                    $tasks = $group['tasks'];
                    $borderColor = $list
                        ? (\App\Models\TaskList::COLORS[$list->color ?? \App\Models\TaskList::defaultColor()] ?? '#6366f1')
                        : '#6366f1';
                @endphp

                <div>
                    <div class="mb-3 flex items-center gap-2">
                        <span class="size-2.5 rounded-full" style="background: {{ $borderColor }}"></span>
                        <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                            @if($list)
                                {{ $list->name }}
                            @else
                                Uncategorized
                            @endif
                        </h2>
                    </div>

                    <div class="flex flex-col gap-2">
                        @foreach($tasks as $trashedTask)
                            @php
                                $dueClasses = $trashedTask->is_overdue
                                    ? 'text-rose-600 dark:text-rose-400 font-medium'
                                    : ($trashedTask->is_upcoming
                                        ? 'text-amber-600 dark:text-amber-400 font-medium'
                                        : 'text-slate-400 dark:text-slate-500');
                            @endphp
                            <div
                                class="group relative flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md dark:border-white/10 dark:bg-white/[.035] dark:hover:bg-white/[.06]"
                                wire:key="trash-{{ $trashedTask->id }}"
                            >
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="truncate font-semibold text-slate-900 dark:text-white">
                                            {{ $trashedTask->name }}
                                        </p>
                                        <span class="rounded-md px-2 py-0.5 text-xs font-semibold {{ \App\Models\Task::PRIORITY_BADGE_CLASSES[$trashedTask->priority] ?? \App\Models\Task::PRIORITY_BADGE_CLASSES[1] }}">
                                            {{ \App\Models\Task::PRIORITY_LABELS[$trashedTask->priority] ?? 'Medium' }}
                                        </span>
                                    </div>
                                    @if($trashedTask->description)
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 line-clamp-1">{{ $trashedTask->description }}</p>
                                    @endif
                                    @if($trashedTask->deadline)
                                        <p class="mt-1 text-xs font-medium {{ $dueClasses }}">
                                            {{ $trashedTask->is_overdue ? 'Overdue' : 'Due' }} {{ $trashedTask->deadline->format('M j, Y') }}
                                        </p>
                                    @endif
                                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">
                                        Deleted {{ $trashedTask->deleted_at->format('M j, Y g:i A') }}
                                    </p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <flux:button size="sm" variant="ghost" wire:click="restore({{ $trashedTask->id }})" icon="arrow-uturn-left">
                                        Restore
                                    </flux:button>
                                    <flux:button
                                        size="sm"
                                        variant="danger"
                                        wire:click="forceDelete({{ $trashedTask->id }})"
                                        wire:confirm="Permanently delete this task? This cannot be undone."
                                        icon="trash"
                                    >
                                        Delete forever
                                    </flux:button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>