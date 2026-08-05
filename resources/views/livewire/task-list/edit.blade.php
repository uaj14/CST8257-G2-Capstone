<flux:modal wire:model="showEditModal" variant="flyout">
    <flux:heading size="lg">Rename List</flux:heading>

    <form wire:submit="save" class="mt-4 space-y-4">
        <flux:field>
            <flux:label>Name</flux:label>
            <flux:input wire:model="name" autofocus />
            <flux:error name="name" />
        </flux:field>

        <div class="flex justify-end gap-3 pt-2">
            <flux:button type="button" wire:click="$set('showEditModal', false)" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Save
            </flux:button>
        </div>
    </form>
</flux:modal>
