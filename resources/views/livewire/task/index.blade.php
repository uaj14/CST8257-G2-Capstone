<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('dashboard')">Dashboard</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $taskList->name }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>
        <flux:button wire:click="$dispatch('open-create-task')" variant="primary" size="sm">
            Add Task
        </flux:button>
    </div>

    @php
        $priorityLabels = [0 => 'Low', 1 => 'Medium', 2 => 'High'];
        $priorityColors = [0 => 'bg-green-100 text-green-800', 1 => 'bg-yellow-100 text-yellow-800', 2 => 'bg-red-100 text-red-800'];
    @endphp

    @if($tasks->isEmpty())
        <div class="flex flex-1 items-center justify-center rounded-xl border border-dashed border-neutral-300 dark:border-neutral-700 p-12">
            <p class="text-neutral-500 dark:text-neutral-400">No tasks yet. Add one to get started.</p>
        </div>
    @else
        <div class="flex flex-col gap-3">
            @foreach($tasks as $task)
                <flux:card
                    class="group flex cursor-pointer items-start gap-4 transition duration-150 hover:border-neutral-400 hover:shadow-md hover:bg-neutral-50 dark:hover:bg-neutral-800 dark:hover:border-neutral-500"
                    wire:key="{{ $task->id }}"
                    wire:click="$dispatch('open-edit-task', { taskId: {{ $task->id }} })"
                >
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-neutral-900 dark:text-neutral-100">
                                {{ $task->name }}
                            </span>
                            <flux:badge size="sm" class="{{ $priorityColors[$task->priority] ?? '' }}">
                                {{ $priorityLabels[$task->priority] ?? 'Medium' }}
                            </flux:badge>
                        </div>
                        @if($task->description)
                            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400 truncate">
                                {{ $task->description }}
                            </p>
                        @endif
                        @if($task->deadline)
                            <p class="mt-1 text-xs text-neutral-400">
                                Due {{ $task->deadline->format('M j, Y') }}
                            </p>
                        @endif
                    </div>
                    <div class="flex-shrink-0" x-on:click.stop>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                            <flux:menu>
                                <flux:menu.item wire:click="$dispatch('open-edit-task', { taskId: {{ $task->id }} })">
                                    Edit
                                </flux:menu.item>
                                <flux:menu.item
                                    variant="danger"
                                    wire:click="delete({{ $task->id }})"
                                    wire:confirm="Delete this task?"
                                >
                                    Delete
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif

    <livewire:task.create :taskList="$taskList" />
    <livewire:task.edit />
</div>
