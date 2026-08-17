<div class="flex h-full w-full flex-1 flex-col gap-5 rounded-xl"
     x-data="taskReorder(@js($tasks->reject(fn ($t) => $t->trashed())->pluck('id')->all()))"
     @task-reordered.window="reorder($event.detail.ids)"
     x-on:dragstart="onDragStart($event)"
     x-on:dragover.prevent="onDragOver($event)"
     x-on:drop="onDrop($event)"
     x-on:dragend="onDragEnd($event)">
    @php
        $borderColor = \App\Models\TaskList::COLORS[$taskList->color ?? \App\Models\TaskList::defaultColor()] ?? '#6366f1';
        $orderLabel = match ($this->sort) {
            'priority' => 'priority',
            'deadline' => 'deadline',
            'name' => 'name',
            default => 'your order',
        };
    @endphp
    <div class="flex items-start justify-between gap-4">
        <div class="flex min-w-0 items-start gap-3">
            <span class="mt-1.5 size-3 shrink-0 rounded-full" style="background: {{ $borderColor }}"></span>
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Workspace</p>
                <h1 class="truncate text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ $taskList->name }}</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $tasks->count() }} {{ Str::plural('task', $tasks->count()) }} · ordered by {{ $orderLabel }}</p>
            </div>
        </div>
        <flux:button wire:click="$dispatch('open-create-task')" variant="primary" size="sm">
            Add Task
        </flux:button>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-white/[.035]">
        <div class="p-4">
            <form wire:submit="quickAdd" class="flex gap-2">
                <flux:input
                    wire:model="quickName"
                    placeholder="Quick add: e.g. Buy groceries"
                    class="flex-1"
                />
                <flux:button type="submit" variant="primary" size="sm" icon="plus">
                    Add
                </flux:button>
            </form>
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex gap-2">
            <flux:button size="sm" variant="filled" :class="$filter === 'all' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-200' : 'bg-white text-slate-700 dark:bg-white/5 dark:text-slate-200'" wire:click="$set('filter', 'all')">All</flux:button>
            <flux:button size="sm" variant="filled" :class="$filter === 'active' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-200' : 'bg-white text-slate-700 dark:bg-white/5 dark:text-slate-200'" wire:click="$set('filter', 'active')">Active</flux:button>
            <flux:button size="sm" variant="filled" :class="$filter === 'completed' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-200' : 'bg-white text-slate-700 dark:bg-white/5 dark:text-slate-200'" wire:click="$set('filter', 'completed')">Completed</flux:button>
            <flux:button size="sm" variant="filled" :class="$filter === 'archived' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-200' : 'bg-white text-slate-700 dark:bg-white/5 dark:text-slate-200'" wire:click="$set('filter', 'archived')">Archived</flux:button>
        </div>
        <div class="flex w-full gap-2 sm:w-auto">
            <flux:input wire:model.live="search" placeholder="Search tasks..." icon="magnifying-glass" class="w-full sm:w-56" />
            <flux:select wire:model.live="sort" size="sm" class="w-full sm:w-40">
                <option value="position">Sort: Default</option>
                <option value="priority">Sort: Priority</option>
                <option value="deadline">Sort: Deadline</option>
                <option value="name">Sort: Name</option>
            </flux:select>
        </div>
    </div>

    @if($tasks->isEmpty())
            @if($filter === 'archived')
                <div class="flex flex-1 flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-neutral-300 dark:border-neutral-700 p-12 text-center">
                    <p class="text-neutral-500 dark:text-neutral-400">No archived tasks.</p>
                </div>
            @else
                <div class="flex flex-1 flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-neutral-300 dark:border-neutral-700 p-12 text-center">
                    <p class="text-neutral-500 dark:text-neutral-400">No tasks match this view.</p>
                </div>
            @endif
        @else
            @if($filter === 'archived')
                <div class="rounded-xl border border-neutral-200 bg-neutral-50 px-4 py-3 dark:border-neutral-700 dark:bg-neutral-800/40">
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">
                        Archived tasks are hidden from your active lists. <strong>Restore</strong> to bring one back.
                    </p>
                </div>
            @endif
            <div class="flex flex-col gap-3" x-ref="list">
            @foreach($tasks as $task)
                @php
                    $isArchivedRow = $task->trashed() ? 'true' : 'false';
                @endphp
                <div
                    class="group relative flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition duration-150 hover:border-slate-300 hover:shadow-md dark:border-white/10 dark:bg-white/[.035] dark:hover:border-white/20 dark:hover:bg-white/[.06]"
                    wire:key="{{ $task->id }}"
                    data-task-id="{{ $task->id }}"
                    data-not-draggable="{{ $isArchivedRow }}"
                    draggable="true"
                >
                    <span class="mt-1 select-none text-slate-300 dark:text-slate-600 cursor-grab" aria-hidden="true" x-on:click.stop>☷</span>
                    <div class="min-w-0 flex-1 @if(!$task->trashed()) cursor-pointer @endif"
                         @if(!$task->trashed()) x-on:click="$dispatch('open-edit-task', { taskId: {{ $task->id }} })" @endif>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-slate-900 dark:text-white {{ (bool) $task->completed_at ? 'line-through decoration-slate-400' : '' }}">
                                {{ $task->name }}
                            </p>
                            <span class="rounded-md px-2 py-0.5 text-xs font-semibold {{ \App\Models\Task::PRIORITY_BADGE_CLASSES[$task->priority] ?? \App\Models\Task::PRIORITY_BADGE_CLASSES[1] }}">
                                {{ \App\Models\Task::PRIORITY_LABELS[$task->priority] ?? 'Medium' }}
                            </span>
                            @if($task->trashed())
                                <span class="rounded-md bg-zinc-50 px-2 py-0.5 text-xs font-semibold text-zinc-700 dark:bg-zinc-400/10 dark:text-zinc-200">Archived</span>
                            @endif
                        </div>
                        @if($task->description)
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 line-clamp-1">{{ $task->description }}</p>
                        @endif
                        @if($task->deadline)
                            @php
                                $dueClasses = $task->is_overdue
                                    ? 'text-rose-600 dark:text-rose-400 font-medium'
                                    : ($task->is_upcoming
                                        ? 'text-amber-600 dark:text-amber-400 font-medium'
                                        : 'text-slate-400 dark:text-slate-500');
                            @endphp
                            <p class="mt-2 text-xs font-medium {{ $dueClasses }}">
                                {{ $task->is_overdue ? 'Overdue' : 'Due' }} {{ $task->deadline->format('M j, Y') }}
                            </p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @if(!$task->trashed())
                            @if(!$task->completed_at)
                                <flux:button size="sm" variant="primary" wire:click="complete({{ $task->id }})" icon="check">
                                    Complete
                                </flux:button>
                            @else
                                <flux:button size="sm" variant="ghost" wire:click="reopen({{ $task->id }})">
                                    Reopen
                                </flux:button>
                            @endif
                        @endif
                        <div class="flex-shrink-0" x-on:click.stop>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                <flux:menu>
                                    @if(!$task->trashed())
                                        <flux:menu.item wire:click="$dispatch('open-edit-task', { taskId: {{ $task->id }} })">
                                            Edit
                                        </flux:menu.item>
                                        <flux:menu.item wire:click="archive({{ $task->id }})" wire:confirm="Archive this task?">
                                            Archive
                                        </flux:menu.item>
                                        <flux:menu.item
                                            variant="danger"
                                            wire:click="delete({{ $task->id }})"
                                            wire:confirm="Permanently delete this task?"
                                        >
                                            Delete
                                        </flux:menu.item>
                                    @else
                                        <flux:menu.item wire:click="restore({{ $task->id }})">
                                            Restore
                                        </flux:menu.item>
                                        <flux:menu.item
                                            variant="danger"
                                            wire:click="forceDelete({{ $task->id }})"
                                            wire:confirm="Permanently delete this archived task?"
                                        >
                                            Delete forever
                                        </flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <livewire:task.create :taskList="$taskList" />
    <livewire:task.edit />
</div>
