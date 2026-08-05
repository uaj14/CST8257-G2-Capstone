<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">My Task Lists</h1>
        <flux:button wire:click="openCreate" variant="primary">
            New List
        </flux:button>
    </div>

    @if($taskLists->isEmpty())
        <div class="flex flex-1 items-center justify-center rounded-xl border border-dashed border-neutral-300 dark:border-neutral-700 p-12">
            <p class="text-neutral-500 dark:text-neutral-400">No task lists yet. Create one to get started.</p>
        </div>
    @else
        <div class="grid auto-rows-min gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($taskLists as $taskList)
                <flux:card class="relative flex flex-col gap-3" wire:key="{{ $taskList->id }}">
                    <div class="flex items-start justify-between">
                        <a href="{{ route('tasks.index', $taskList) }}" class="text-lg font-medium text-neutral-900 hover:underline dark:text-neutral-100">
                            {{ $taskList->name }}
                        </a>
                        <flux:dropdown>
                            <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                            <flux:menu>
                                <flux:menu.item wire:click="openEdit({{ $taskList->id }})">
                                    Rename
                                </flux:menu.item>
                                <flux:menu.item
                                    variant="danger"
                                    wire:click="delete({{ $taskList->id }})"
                                    wire:confirm="Delete this list and all its tasks?"
                                >
                                    Delete
                                </flux:menu.item>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $taskList->tasks_count }} {{ Str::plural('task', $taskList->tasks_count) }}
                    </p>
                </flux:card>
            @endforeach
        </div>
    @endif

    <!-- Create Modal -->
    @if($showCreateModal)
        <livewire:task-list.create />
    @endif

    <!-- Edit Modal -->
    @if($showEditModal && $editingTaskList)
        <livewire:task-list.edit :taskList="$editingTaskList" wire:key="edit-{{ $editingTaskList->id }}" />
    @endif
</div>
