// Globals made available to Alpine x-data attributes before Livewire's
// @script blocks (which are injected lazily, too late for x-data on
// the initial render).

window.taskReorder = function (initialIds) {
    return {
        draggingId: null,
        order: initialIds,

        init() {
            // The HTML5 spec only treats draggable="true" as natively draggable.
            // Laravel/Blaze folds boolean attributes into draggable="draggable",
            // which the browser reads as NOT draggable. Set the DOM property
            // directly to bypass the attribute mangling.
            this.$root.querySelectorAll('[data-task-id]').forEach((el) => {
                el.draggable = true;
            });
        },

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

            // $wire is not a global; it's an Alpine magic exposed on `this`
            // by Livewire's Alpine plugin. Using the bare global throws
            // ReferenceError: $wire is not defined.
            this.$wire.call('reorder', this.order);
            this.draggingId = null;
        },

        onDragEnd() {
            this.draggingId = null;
        },
    };
};