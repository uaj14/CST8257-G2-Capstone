<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-100">My Task Lists</h1>
            <p class="text-sm text-neutral-500 dark:text-neutral-400">Select a list or create a new one.</p>
        </div>
        <flux:button wire:click="$dispatch('open-create-task-list')" variant="primary">
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
                @php $borderColor = \App\Models\TaskList::COLORS[$taskList->color ?? \App\Models\TaskList::defaultColor()] ?? '#3b82f6'; @endphp
                <flux:card
                    class="group relative flex flex-col gap-3 transition duration-150 border-l-4 hover:shadow-md hover:bg-neutral-50 dark:hover:bg-neutral-800"
                    wire:key="{{ $taskList->id }}"
                    style="border-left-color: {{ $borderColor }}"
                >
                    <div class="flex items-start justify-between">
                        <a
                            href="{{ route('tasks.index', $taskList) }}"
                            class="text-lg font-medium text-neutral-900 dark:text-neutral-100 after:absolute after:inset-0 after:content-['']"
                        >
                            {{ $taskList->name }}
                        </a>
                        <div class="relative z-10" x-on:click.stop>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-vertical" />
                                <flux:menu>
                                    <flux:menu.item wire:click="$dispatch('open-edit-task-list', { taskListId: {{ $taskList->id }} })">
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
                    </div>
                    <div class="flex items-center gap-3 text-sm text-neutral-500 dark:text-neutral-400">
                        <span>
                            {{ $taskList->active_tasks_count }} active
                            <span class="text-neutral-400 dark:text-neutral-600">/ {{ $taskList->tasks_count }}</span>
                        </span>
                        @if($taskList->next_due)
                            <span aria-hidden="true">·</span>
                            <span>Next due {{ \Illuminate\Support\Carbon::parse($taskList->next_due)->format('M j') }}</span>
                        @endif
                    </div>
                </flux:card>
            @endforeach
        </div>
    @endif

    <livewire:task-list.create />
    <livewire:task-list.edit />
</div>
