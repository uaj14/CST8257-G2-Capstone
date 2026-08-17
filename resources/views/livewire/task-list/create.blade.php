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
            <div class="flex flex-wrap gap-3 mt-1">
                @foreach(TaskList::COLORS as $key => $hex)
                    <button
                        type="button"
                        wire:click="$set('color', '{{ $key }}')"
                        title="{{ ucfirst($key) }}"
                        class="size-9 rounded-full transition shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-neutral-900 dark:focus-visible:ring-neutral-100"
                        style="background-color: {{ $hex }}; box-shadow: 0 0 0 2px {{ $color === $key ? '#fff' : 'transparent' }}, 0 0 0 4px {{ $color === $key ? $hex : 'transparent' }};"
                        aria-label="Color {{ $key }}"
                        aria-pressed="{{ $color === $key ? 'true' : 'false' }}"
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