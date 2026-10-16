/**
 * shadcn/ui JavaScript primitives for Laravel UI
 * Provides Sonner-style Toasts, Dialog/Modal controller, Dropdowns, and DataTable utilities.
 */

(function () {
    'use strict';

    // 1. Toast Notification System (Sonner / shadcn style)
    const ToastSystem = {
        container: null,

        init() {
            if (this.container) return;
            this.container = document.createElement('div');
            this.container.id = 'shadcn-toast-container';
            this.container.className = 'fixed bottom-5 right-5 z-[9999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none transition-all';
            document.body.appendChild(this.container);
        },

        show(options = {}) {
            this.init();

            const {
                type = 'info', // 'success', 'error', 'info', 'warning', 'telemetry'
                title = '',
                message = '',
                duration = 4500,
                action = null,
            } = options;

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto flex items-start gap-3 p-4 rounded-xl border border-border bg-card/95 backdrop-blur-md text-card-foreground shadow-lg transition-all duration-300 transform translate-y-3 opacity-0';

            // Icons based on type
            let iconSvg = '';
            let iconBg = 'bg-primary/10 text-primary';

            if (type === 'success') {
                iconBg = 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20';
                iconSvg = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
            } else if (type === 'error') {
                iconBg = 'bg-rose-500/10 text-rose-600 border border-rose-500/20';
                iconSvg = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>`;
            } else if (type === 'warning') {
                iconBg = 'bg-amber-500/10 text-amber-600 border border-amber-500/20';
                iconSvg = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
            } else if (type === 'telemetry') {
                iconBg = 'bg-blue-500/10 text-blue-600 border border-blue-500/20';
                iconSvg = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.393 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>`;
            } else {
                iconBg = 'bg-muted text-muted-foreground border border-border';
                iconSvg = `<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>`;
            }

            toast.innerHTML = `
                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 ${iconBg}">
                    ${iconSvg}
                </div>
                <div class="flex-1 text-left min-w-0 pr-1">
                    ${title ? `<div class="text-sm font-semibold text-foreground tracking-tight">${title}</div>` : ''}
                    ${message ? `<div class="text-xs text-muted-foreground mt-0.5 leading-relaxed">${message}</div>` : ''}
                </div>
                <button type="button" class="text-muted-foreground/60 hover:text-foreground p-1 rounded-md transition-colors shrink-0" aria-label="Dismiss">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            `;

            const closeBtn = toast.querySelector('button');
            const dismiss = () => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-3', 'opacity-0');
                setTimeout(() => toast.remove(), 250);
            };

            closeBtn.addEventListener('click', dismiss);

            this.container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-3', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });

            if (duration > 0) {
                setTimeout(dismiss, duration);
            }
        },

        success(title, message) { this.show({ type: 'success', title, message }); },
        error(title, message) { this.show({ type: 'error', title, message }); },
        warning(title, message) { this.show({ type: 'warning', title, message }); },
        info(title, message) { this.show({ type: 'info', title, message }); },
        telemetry(title, message) { this.show({ type: 'telemetry', title, message }); },
    };

    window.toast = ToastSystem;

    // 2. shadcn Dialog & Modal Controller
    const ModalController = {
        open(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');

            const content = modal.querySelector('.modal-content, [role="dialog"]');
            if (content) {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }

            if (window.lucide) {
                window.lucide.createIcons();
            }
        },

        close(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            const content = modal.querySelector('.modal-content, [role="dialog"]');
            if (content) {
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
            }

            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }, 180);
        },

        confirm(options = {}) {
            const {
                title = 'Are you sure?',
                description = 'This action cannot be undone.',
                confirmText = 'Continue',
                cancelText = 'Cancel',
                variant = 'destructive', // 'destructive' or 'default'
                onConfirm = () => {},
            } = options;

            let existing = document.getElementById('shadcn-confirm-dialog');
            if (existing) existing.remove();

            const dialog = document.createElement('div');
            dialog.id = 'shadcn-confirm-dialog';
            dialog.className = 'fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200';

            const buttonStyle = variant === 'destructive'
                ? 'bg-destructive text-destructive-foreground hover:bg-destructive/90'
                : 'bg-primary text-primary-foreground hover:bg-primary/90';

            dialog.innerHTML = `
                <div class="relative w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-2xl transition-all duration-200 transform scale-95 opacity-0 modal-content">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 ${variant === 'destructive' ? 'bg-destructive/10 text-destructive' : 'bg-primary/10 text-primary'}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="flex-1 text-left">
                            <h3 class="text-base font-semibold text-foreground tracking-tight">${title}</h3>
                            <p class="text-xs text-muted-foreground mt-1.5 leading-relaxed">${description}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-border/60">
                        <button type="button" class="btn-cancel px-4 py-2 text-xs font-medium rounded-lg border border-input bg-card hover:bg-muted text-foreground transition-colors shadow-xs">
                            ${cancelText}
                        </button>
                        <button type="button" class="btn-confirm px-4 py-2 text-xs font-semibold rounded-lg ${buttonStyle} transition-all shadow-xs">
                            ${confirmText}
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(dialog);
            document.body.classList.add('overflow-hidden');

            const content = dialog.querySelector('.modal-content');
            requestAnimationFrame(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            });

            const close = () => {
                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    dialog.remove();
                    document.body.classList.remove('overflow-hidden');
                }, 180);
            };

            dialog.querySelector('.btn-cancel').addEventListener('click', close);
            dialog.addEventListener('click', (e) => {
                if (e.target === dialog) close();
            });

            dialog.querySelector('.btn-confirm').addEventListener('click', () => {
                close();
                if (typeof onConfirm === 'function') onConfirm();
            });
        }
    };

    window.shadcnModal = ModalController;

    // 3. DataTable Live Filter & Search
    function initDataTableFilters() {
        document.querySelectorAll('[data-shadcn-table-search]').forEach((searchInput) => {
            const tableId = searchInput.getAttribute('data-shadcn-table-search');
            const table = document.getElementById(tableId);
            if (!table) return;

            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const rows = table.querySelectorAll('tbody tr:not(.empty-row)');
                let visibleCount = 0;

                rows.forEach((row) => {
                    const text = row.textContent.toLowerCase();
                    const matches = text.includes(query);
                    row.style.display = matches ? '' : 'none';
                    if (matches) visibleCount++;
                });

                // Update counter if counter element exists
                const counter = document.querySelector(`[data-counter-for="${tableId}"]`);
                if (counter) counter.textContent = visibleCount;
            });
        });

        // Status pill filters
        document.querySelectorAll('[data-shadcn-filter-group]').forEach((group) => {
            const tableId = group.getAttribute('data-shadcn-filter-group');
            const table = document.getElementById(tableId);
            if (!table) return;

            const buttons = group.querySelectorAll('button[data-filter]');
            buttons.forEach((btn) => {
                btn.addEventListener('click', function () {
                    buttons.forEach((b) => {
                        b.classList.remove('bg-foreground', 'text-background', 'font-semibold', 'shadow-xs');
                        b.classList.add('text-muted-foreground', 'hover:text-foreground');
                    });
                    this.classList.remove('text-muted-foreground', 'hover:text-foreground');
                    this.classList.add('bg-foreground', 'text-background', 'font-semibold', 'shadow-xs');

                    const filterValue = this.getAttribute('data-filter');
                    const rows = table.querySelectorAll('tbody tr:not(.empty-row)');
                    let visibleCount = 0;

                    rows.forEach((row) => {
                        const status = row.getAttribute('data-status') || '';
                        const matches = filterValue === 'all' || status.toLowerCase() === filterValue.toLowerCase();
                        row.style.display = matches ? '' : 'none';
                        if (matches) visibleCount++;
                    });

                    const counter = document.querySelector(`[data-counter-for="${tableId}"]`);
                    if (counter) counter.textContent = visibleCount;
                });
            });
        });
    }

    // 4. Keyboard Shortcuts (Cmd+K / Ctrl+K to search)
    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('header input[type="text"], #topNavSearch, #rosterSearch, #tripSearch');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });

    // 5. Initialize Lucide icons on load
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
        initDataTableFilters();
    });
})();
