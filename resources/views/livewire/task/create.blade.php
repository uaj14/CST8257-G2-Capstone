<flux:modal wire:model="showCreateModal" variant="flyout">
    <flux:heading size="lg">New Task</flux:heading>
    <p class="text-sm text-neutral-500 mt-1">Add a task to {{ $taskList->name }}</p>

    <form wire:submit="create" class="mt-4 space-y-4">
        <flux:field>
            <flux:label>Name</flux:label>
            <flux:input wire:model="name" placeholder="e.g. Buy groceries" autofocus />
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
            <flux:button type="button" wire:click="$set('showCreateModal', false)" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Add Task
            </flux:button>
        </div>
    </form>
</flux:modal>
