<div class="flex h-full w-full flex-1 flex-col gap-5 rounded-xl"
     x-data="taskReorder(@js($tasks->pluck('id')->all()))"
     @task-reordered.window="reorder($event.detail.ids)"
     x-on:dragstart="onDragStart($event)"
     x-on:dragover.prevent="onDragOver($event)"
     x-on:drop="onDrop($event)"
     x-on:dragend="onDragEnd($event)">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-300">Workspace</p>
            <h1 class="truncate text-2xl font-semibold tracking-tight text-neutral-900 dark:text-neutral-100">{{ $taskList->name }}</h1>
        </div>
        <flux:button wire:click="$dispatch('open-create-task')" variant="primary" size="sm">
            Add Task
        </flux:button>
    </div>

    <div class="rounded-xl border border-neutral-200/80 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
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
            <flux:button size="sm" variant="filled" :class="$filter === 'all' ? 'bg-indigo-600 text-white dark:bg-indigo-500' : 'bg-white text-neutral-700 dark:bg-neutral-800 dark:text-neutral-200'" wire:click="$set('filter', 'all')">All</flux:button>
            <flux:button size="sm" variant="filled" :class="$filter === 'active' ? 'bg-indigo-600 text-white dark:bg-indigo-500' : 'bg-white text-neutral-700 dark:bg-neutral-800 dark:text-neutral-200'" wire:click="$set('filter', 'active')">Active</flux:button>
            <flux:button size="sm" variant="filled" :class="$filter === 'completed' ? 'bg-indigo-600 text-white dark:bg-indigo-500' : 'bg-white text-neutral-700 dark:bg-neutral-800 dark:text-neutral-200'" wire:click="$set('filter', 'completed')">Completed</flux:button>
            <flux:button size="sm" variant="filled" :class="$filter === 'archived' ? 'bg-indigo-600 text-white dark:bg-indigo-500' : 'bg-white text-neutral-700 dark:bg-neutral-800 dark:text-neutral-200'" wire:click="$set('filter', 'archived')">Archived</flux:button>
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
        <div class="flex flex-1 flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-neutral-300 dark:border-neutral-700 p-12 text-center">
            <p class="text-neutral-500 dark:text-neutral-400">No tasks match this view.</p>
        </div>
    @else
        <div class="flex flex-col gap-3" x-ref="list">
            @foreach($tasks as $task)
                <flux:card
                    class="group relative flex items-start gap-4 transition duration-150 border-t-4 border-b-4 border-transparent hover:shadow-md hover:bg-neutral-50 dark:hover:bg-neutral-800/70"
                    wire:key="{{ $task->id }}"
                    data-task-id="{{ $task->id }}"
                    draggable="true"
                >
                    <div class="flex-shrink-0 pt-0.5 text-neutral-400 dark:text-neutral-600 cursor-grab select-none" x-on:click.stop>
                        <flux:icon name="bars-3" class="size-5" />
                    </div>
                    <div class="flex-1 min-w-0"
                         x-on:click="$dispatch('open-edit-task', { taskId: {{ $task->id }} })">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100 {{ (bool) $task->completed_at ? 'line-through decoration-neutral-400' : '' }}">
                                {{ $task->name }}
                            </span>
                            <flux:badge size="sm" class="{{ \App\Models\Task::PRIORITY_COLORS[$task->priority] ?? '' }}">
                                {{ \App\Models\Task::PRIORITY_LABELS[$task->priority] ?? 'Medium' }}
                            </flux:badge>
                            @if($task->trashed())
                                <flux:badge size="sm" class="bg-neutral-100 text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300">Archived</flux:badge>
                            @endif
                        </div>
                        @if($task->description)
                            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400 line-clamp-1">
                                {{ $task->description }}
                            </p>
                        @endif
                        @if($task->deadline)
                            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">
                                Due {{ $task->deadline->format('M j, Y') }}
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
                                        @if(!$task->completed_at)
                                            <flux:menu.item wire:click="archive({{ $task->id }})" wire:confirm="Archive this task?">
                                                Archive
                                            </flux:menu.item>
                                        @endif
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
                </flux:card>
            @endforeach
        </div>
    @endif

    <livewire:task.create :taskList="$taskList" />
    <livewire:task.edit />
</div>
