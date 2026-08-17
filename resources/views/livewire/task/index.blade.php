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

    @php
        $priorityLabels = [0 => 'Low', 1 => 'Medium', 2 => 'High'];
        $priorityColors = [0 => 'bg-green-100 text-green-800 dark:bg-green-400/10 dark:text-green-300', 1 => 'bg-amber-100 text-amber-800 dark:bg-amber-400/10 dark:text-amber-200', 2 => 'bg-red-100 text-red-800 dark:bg-red-400/10 dark:text-red-200'];
        $activeTasks = $tasks->filter(fn($task) => !$task->completed_at && !$task->trashed());
        $completedTasks = $tasks->filter(fn($task) => (bool) $task->completed_at && !$task->trashed());
        $archivedTasks = $tasks->filter(fn($task) => $task->trashed());
    @endphp

    @if($activeTasks->isEmpty() && $completedTasks->isEmpty())
        <div class="flex flex-1 flex-col items-center justify-center gap-2 rounded-2xl border border-dashed border-neutral-300 dark:border-neutral-700 p-12 text-center">
            <p class="text-neutral-500 dark:text-neutral-400">No tasks yet. Add one to get started.</p>
        </div>
    @else
        @foreach($activeTasks as $task)
            <flux:card
                class="group relative flex cursor-grab items-start gap-4 transition duration-150 border-t-4 border-b-4 border-transparent hover:shadow-md hover:bg-neutral-50 dark:hover:bg-neutral-800/70"
                wire:key="active-{{ $task->id }}"
                data-task-id="{{ $task->id }}"
                draggable="true"
            >
                <div class="flex-shrink-0 pt-0.5 text-neutral-400 dark:text-neutral-600 cursor-grab select-none" x-on:click.stop>
                    <flux:icon name="bars-3" class="size-5" />
                </div>
                <div class="flex-1 min-w-0"
                     x-on:click="$dispatch('open-edit-task', { taskId: {{ $task->id }} })">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                            {{ $task->name }}
                        </span>
                        <flux:badge size="sm" class="{{ $priorityColors[$task->priority] ?? '' }}">
                            {{ $priorityLabels[$task->priority] ?? 'Medium' }}
                        </flux:badge>
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
                    <flux:button size="sm" variant="primary" wire:click="complete({{ $task->id }})" icon="check">
                        Complete
                    </flux:button>
                    <div class="flex-shrink-0" x-on:click.stop>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                            <flux:menu>
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
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </div>
            </flux:card>
        @endforeach
    @endif

    @if(!$completedTasks->isEmpty() || !$archivedTasks->isEmpty())
        <div class="mt-2 space-y-3">
            @if(!$completedTasks->isEmpty())
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Completed</h2>
                    <div class="mt-2 flex flex-col gap-2">
                        @foreach($completedTasks as $task)
                            <flux:card class="flex items-start gap-3 opacity-75" wire:key="completed-{{ $task->id }}">
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100 line-through decoration-neutral-400">
                                        {{ $task->name }}
                                    </span>
                                </div>
                                <flux:button size="sm" variant="ghost" wire:click="reopen({{ $task->id }})">
                                    Reopen
                                </flux:button>
                                <div x-on:click.stop>
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                        <flux:menu>
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
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </flux:card>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!$archivedTasks->isEmpty())
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Archived</h2>
                    <div class="mt-2 flex flex-col gap-2">
                        @foreach($archivedTasks as $task)
                            <flux:card class="flex items-start gap-3 opacity-60" wire:key="archived-{{ $task->id }}">
                                <div class="flex-1 min-w-0">
                                    <span class="text-sm text-neutral-800 dark:text-neutral-200 line-through decoration-neutral-500">
                                        {{ $task->name }}
                                    </span>
                                </div>
                                <flux:button size="sm" variant="ghost" wire:click="restore({{ $task->id }})">
                                    Restore
                                </flux:button>
                                <flux:button size="sm" variant="danger" wire:click="forceDelete({{ $task->id }})" wire:confirm="Permanently delete this archived task?">
                                    Delete forever
                                </flux:button>
                            </flux:card>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <livewire:task.create :taskList="$taskList" />
    <livewire:task.edit />
</div>