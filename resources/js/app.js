// Globals made available to Alpine x-data attributes before Livewire's
// @script blocks (which are injected lazily, too late for x-data on
// the initial render).

window.taskReorder = function (initialIds) {
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

// === Critical: re-apply draggable DOM property after every Livewire morph ===
// Per the HTML5 spec, draggable is only native-draggable when the attribute
// value is exactly "true". Laravel/Blaze folds `draggable="true"` on Blade
// component tags into `draggable="draggable"` — which the browser reads as
// NOT draggable. Alpine's init() fixes this on first boot, but Livewire
// morphs create NEW DOM nodes on every update, and init() never re-runs.
// Hook into Livewire's morph lifecycle so every card stays draggable across
// re-order responses.
(function () {
    function makeCardsDraggable() {
        document.querySelectorAll('[data-task-id]').forEach(function (el) {
            el.draggable = true;
        });
    }

    // Run immediately on initial load (before Livewire might be loaded)
    makeCardsDraggable();

    // Then hook into every subsequent Livewire morph
    if (typeof window.Livewire !== 'undefined') {
        window.Livewire.hook('morph.updated', function () { makeCardsDraggable(); });
    } else {
        document.addEventListener('livewire:init', function () {
            window.Livewire.hook('morph.updated', function () { makeCardsDraggable(); });
        });
    }
})();