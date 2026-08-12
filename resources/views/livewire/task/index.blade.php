<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl"
     x-data="taskReorder(@js($tasks->pluck('id')->all()))"
     @task-reordered.window="reorder($event.detail.ids)">
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
        <div class="flex flex-col gap-3" x-ref="list">
            @foreach($tasks as $task)
                <flux:card
                    class="group flex cursor-grab items-start gap-4 transition duration-150 hover:border-neutral-400 hover:shadow-md hover:bg-neutral-50 dark:hover:bg-neutral-800 dark:hover:border-neutral-500"
                    wire:key="{{ $task->id }}"
                    data-task-id="{{ $task->id }}"
                    draggable="true"
                    x-bind:draggable="true"
                    x-on:dragstart="onDragStart($event, {{ $task->id }})"
                    x-on:dragover.prevent="onDragOver($event, {{ $task->id }})"
                    x-on:drop="onDrop($event, {{ $task->id }})"
                    x-on:dragend="onDragEnd($event)"
                    x-bind:class="{ 'opacity-50 scale-95': draggingId === {{ $task->id }} }"
                >
                    <div class="flex-shrink-0 text-neutral-400 dark:text-neutral-600 cursor-grab select-none" x-on:click.stop>
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
                                <flux:menu.item wire:click="$dispatch('open-edit-task', { taskId: {{ $task->id }} }))">
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

@script
<script>
    function taskReorder(initialIds) {
        return {
            draggingId: null,
            order: initialIds,

            onDragStart(event, id) {
                this.draggingId = id;
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', String(id));
            },

            onDragOver(event, overId) {
                if (this.draggingId === null || this.draggingId === overId) return;
                event.dataTransfer.dropEffect = 'move';
            },

            onDrop(event, dropOnId) {
                if (this.draggingId === null || this.draggingId === dropOnId) return;
                event.preventDefault();

                const from = this.order.indexOf(this.draggingId);
                const to   = this.order.indexOf(dropOnId);
                if (from === -1 || to === -1) return;

                const moved = this.order.splice(from, 1)[0];
                this.order.splice(to, 0, moved);

                $wire.call('reorder', this.order);
                this.draggingId = null;
            },

            onDragEnd() {
                this.draggingId = null;
            },
        };
    }
</script>
@endscript