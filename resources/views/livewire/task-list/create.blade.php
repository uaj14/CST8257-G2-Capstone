<flux:modal wire:model="showCreateModal" variant="flyout">
    <flux:heading size="lg">New Task List</flux:heading>

    <form wire:submit="create" class="mt-4 space-y-4">
        <flux:field>
            <flux:label>Name</flux:label>
            <flux:input wire:model="name" placeholder="e.g. Work, Home, Personal" autofocus />
            <flux:error name="name" />
        </flux:field>

        <div class="flex justify-end gap-3 pt-2">
            <flux:button type="button" wire:click="$set('showCreateModal', false)" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Create List
            </flux:button>
        </div>
    </form>
</flux:modal>
