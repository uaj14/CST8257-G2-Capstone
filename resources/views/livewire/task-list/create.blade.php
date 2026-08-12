@php
    use App\Models\TaskList;
@endphp
<flux:modal wire:model="showModal" variant="flyout">
    <flux:heading size="lg">New Task List</flux:heading>

    <form wire:submit="create" class="mt-4 space-y-4">
        <flux:field>
            <flux:label>Name</flux:label>
            <flux:input wire:model="name" placeholder="e.g. Work, Home, Personal" autofocus />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>Color</flux:label>
            <div class="flex flex-wrap gap-2 mt-1">
                @foreach(TaskList::COLORS as $key => $hex)
                    <button
                        type="button"
                        wire:click="$set('color', '{{ $key }}')"
                        class="size-8 rounded-full border-2 transition {{ $color === $key ? 'border-neutral-900 dark:border-neutral-100 scale-110' : 'border-transparent hover:scale-105' }}"
                        style="background-color: {{ $hex }}"
                        aria-label="Color {{ $key }}"
                    ></button>
                @endforeach
            </div>
            <flux:error name="color" />
        </flux:field>

        <div class="flex justify-end gap-3 pt-2">
            <flux:button type="button" wire:click="$set('showModal', false)" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Create List
            </flux:button>
        </div>
    </form>
</flux:modal>