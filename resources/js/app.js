// Globals made available to Alpine x-data attributes before Livewire's
// @script blocks (which are injected lazily, too late for x-data on
// the initial render).

window.taskReorder = function (initialIds) {
    return {
        draggingId: null,
        indicatorId: null,     // card the drag is currently over
        indicatorSide: null,   // 'top' = insert before, 'bottom' = insert after
        order: initialIds,

        // Returns the card under the pointer plus the insertion side:
        // pointer in the top half of a card => 'top' (insert before it),
        // bottom half => 'bottom' (insert after it). Handles the gaps
        // between cards by snapping to the nearest card above/below.
        resolveCardAt(clientY) {
            const cards = [...this.$root.querySelectorAll('[data-task-id]')];
            if (cards.length === 0) {
                return null;
            }

            let chosen = null;
            let side = 'top';

            for (const card of cards) {
                const rect = card.getBoundingClientRect();
                if (clientY < rect.top) {
                    break; // above this card; keep previous choice
                }
                chosen = Number(card.getAttribute('data-task-id'));
                side = clientY < rect.top + rect.height / 2 ? 'top' : 'bottom';
            }

            if (chosen === null) {
                // Above the first card
                chosen = Number(cards[0].getAttribute('data-task-id'));
                side = 'top';
            }

            return { id: chosen, side };
        },

        clearIndicator() {
            this.indicatorId = null;
            this.indicatorSide = null;
        },

        onDragStart(event) {
            const card = event.target.closest('[data-task-id]');
            if (!card) {
                return;
            }

            this.draggingId = Number(card.getAttribute('data-task-id'));
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', String(this.draggingId));
        },

        onDragOver(event) {
            if (this.draggingId === null) {
                return;
            }

            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';

            const res = this.resolveCardAt(event.clientY);
            if (res && res.id !== this.draggingId) {
                this.indicatorId = res.id;
                this.indicatorSide = res.side;
            } else {
                this.clearIndicator();
            }
        },

        onDrop(event) {
            if (this.draggingId === null) {
                return;
            }

            event.preventDefault();

            const res = this.resolveCardAt(event.clientY);

            if (!res || res.id === this.draggingId) {
                // Dropped back onto itself or outside any card
                this.clearIndicator();
                this.draggingId = null;
                return;
            }

            const from = this.order.indexOf(this.draggingId);
            if (from === -1) {
                this.clearIndicator();
                this.draggingId = null;
                return;
            }

            // Remove the dragged item, then insert before/after the target.
            const moved = this.order.splice(from, 1)[0];
            let to = this.order.indexOf(res.id);
            if (res.side === 'bottom') {
                to += 1;
            }
            this.order.splice(to, 0, moved);

            this.clearIndicator();

            // $wire is not a global; it's an Alpine magic exposed on `this`
            // by Livewire's Alpine plugin. Using the bare global throws
            // ReferenceError: $wire is not defined.
            this.$wire.call('reorder', this.order);
            this.draggingId = null;
        },

        onDragEnd() {
            this.draggingId = null;
            this.clearIndicator();
        },
    };
};

// === Critical: re-apply draggable DOM property after every Livewire morph ===
// Per the HTML5 spec, draggable is only native-draggable when the attribute
// value is exactly "true". Laravel/Blaze folds `draggable="true"` on Blade
// component tags into `draggable="draggable"` — which the browser reads as
// NOT draggable. Livewire morphs create NEW DOM nodes on every update, so
// hook into Livewire's morph lifecycle and re-apply the property each time.
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