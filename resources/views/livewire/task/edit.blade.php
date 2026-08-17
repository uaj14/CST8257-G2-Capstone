<flux:modal wire:model="showModal" variant="flyout">
    <flux:heading size="lg">Edit Task</flux:heading>

    <form wire:submit="save" class="mt-4 space-y-4">
        <flux:field>
            <flux:label>Name</flux:label>
            <flux:input wire:model="name" autofocus />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>Description</flux:label>
            <flux:textarea wire:model="description" placeholder="Optional details" rows="3" />
            <flux:error name="description" />
        </flux:field>

        <flux:field>
            <flux:label>Priority</flux:label>
            <flux:select wire:model.number="priority">
                <option value="0">Low</option>
                <option value="1">Medium</option>
                <option value="2">High</option>
            </flux:select>
            <flux:error name="priority" />
        </flux:field>

        <flux:field>
            <flux:label>Deadline</flux:label>
            <flux:input type="date" wire:model="deadline" />
            <flux:error name="deadline" />
        </flux:field>

        <div class="flex justify-end gap-3 pt-2">
            <flux:button type="button" wire:click="$set('showModal', false)" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Save
            </flux:button>
        </div>
    </form>
</flux:modal>
