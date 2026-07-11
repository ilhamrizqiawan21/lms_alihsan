import $ from 'jquery';
import Alpine from 'alpinejs';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

window.$ = window.jQuery = $;
window.Alpine = Alpine;

Alpine.data('appShell', () => ({
    sidebarOpen: window.innerWidth >= 992,
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    },
    closeSidebar() {
        this.sidebarOpen = false;
    },
}));

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (match) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    }[match]));
}

window.showToast = function (message, type = 'success') {
    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill',
    };
    const container = document.getElementById('toastContainer');

    if (!container) {
        return;
    }

    const el = document.createElement('div');
    el.className = `toast-item ${type}`;
    el.setAttribute('role', type === 'error' ? 'alert' : 'status');
    el.innerHTML = `<i class="bi ${icons[type] || icons.info}" aria-hidden="true"></i><span>${escapeHtml(message)}</span>`;
    container.appendChild(el);

    setTimeout(() => {
        el.classList.add('removing');
        setTimeout(() => {
            if (el.parentNode) {
                el.remove();
            }
        }, 400);
    }, 4000);
};

window.renderChart = async function (target, config) {
    const canvas = typeof target === 'string' ? document.getElementById(target) : target;

    if (!canvas) {
        return null;
    }

    const { Chart, registerables } = await import('chart.js');
    Chart.register(...registerables);

    return new Chart(canvas, config);
};

window.confirmAction = function (message, callback, options = {}) {
    const title = options.title || 'Konfirmasi';
    const confirmText = options.confirmText || 'Ya, lanjutkan';
    const cancelText = options.cancelText || 'Batal';
    const isDanger = options.danger === true;
    const overlay = document.createElement('div');
    const previousFocus = document.activeElement;

    overlay.className = 'confirm-overlay';
    overlay.innerHTML = '<div class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="confirmTitle" aria-describedby="confirmMessage">' +
        `<div class="confirm-icon ${isDanger ? 'danger' : 'warning'}"><i class="bi ${isDanger ? 'bi-exclamation-triangle-fill' : 'bi-question-circle-fill'}" aria-hidden="true"></i></div>` +
        `<h5 id="confirmTitle" class="confirm-title">${escapeHtml(title)}</h5>` +
        `<p id="confirmMessage" class="confirm-message">${escapeHtml(message)}</p>` +
        '<div class="confirm-actions">' +
        `<button type="button" id="confirmCancel" class="btn btn-outline-secondary">${escapeHtml(cancelText)}</button>` +
        `<button type="button" id="confirmOk" class="btn ${isDanger ? 'btn-danger' : 'btn-success'}">${escapeHtml(confirmText)}</button>` +
        '</div></div>';

    const close = (result) => {
        if (overlay.parentNode) {
            overlay.remove();
        }
        document.removeEventListener('keydown', escapeHandler);
        if (previousFocus && typeof previousFocus.focus === 'function') {
            previousFocus.focus();
        }
        callback(result);
    };
    const escapeHandler = (event) => {
        if (event.key === 'Escape') {
            close(false);
            return;
        }

        if (event.key === 'Tab') {
            const focusable = overlay.querySelectorAll('button');
            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    };

    document.body.appendChild(overlay);
    document.getElementById('confirmOk').onclick = () => close(true);
    document.getElementById('confirmCancel').onclick = () => close(false);
    overlay.onclick = (event) => {
        if (event.target === overlay) {
            close(false);
        }
    };
    document.addEventListener('keydown', escapeHandler);
    document.getElementById('confirmOk').focus();
};

function setFormLoading(form) {
    if (!form || form.dataset.loading === 'true' || form.dataset.noLoading === 'true') {
        return;
    }

    form.dataset.loading = 'true';
    form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach((button) => {
        button.dataset.originalHtml = button.innerHTML;
        button.disabled = true;
        button.classList.add('is-loading');

        if (button.tagName === 'BUTTON') {
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span><span>Memproses...</span>';
        }
    });
}

document.addEventListener('click', (event) => {
    const el = event.target.closest('[data-confirm]');

    if (!el) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();

    const msg = el.getAttribute('data-confirm') || 'Anda yakin ingin melanjutkan?';
    const danger = el.className.indexOf('danger') !== -1 || el.getAttribute('data-danger') === 'true';

    window.confirmAction(msg, (ok) => {
        if (!ok) {
            return;
        }

        const parentForm = el.closest('form');
        const formAction = el.getAttribute('data-action');
        const method = (el.getAttribute('data-method') || 'get').toUpperCase();

        if (parentForm) {
            if (el.name) {
                const submitValue = document.createElement('input');
                submitValue.type = 'hidden';
                submitValue.name = el.name;
                submitValue.value = el.value;
                parentForm.appendChild(submitValue);
            }
            setFormLoading(parentForm);
            parentForm.submit();
        } else if (formAction) {
            const form = document.createElement('form');
            form.method = method === 'POST' ? 'POST' : 'GET';
            form.action = formAction;

            if (method === 'DELETE') {
                form.innerHTML = '<input type="hidden" name="_method" value="DELETE">';
                form.method = 'POST';
            }

            const csrf = document.querySelector('meta[name="csrf-token"]');
            if (csrf && method !== 'GET') {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_token';
                input.value = csrf.content;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            setFormLoading(form);
            form.submit();
        } else if (el.href) {
            window.location.href = el.href;
        }
    }, { danger, title: el.getAttribute('data-title') || 'Konfirmasi' });
});

document.addEventListener('submit', (event) => {
    const submitter = event.submitter;

    if (submitter?.name && !event.target.querySelector(`input[type="hidden"][data-submit-proxy="${submitter.name}"]`)) {
        const submitValue = document.createElement('input');
        submitValue.type = 'hidden';
        submitValue.name = submitter.name;
        submitValue.value = submitter.value;
        submitValue.dataset.submitProxy = submitter.name;
        event.target.appendChild(submitValue);
    }

    setFormLoading(event.target);
});

$(document).ready(async () => {
    if ($('.select2').length > 0) {
        const select2Module = await import('select2');
        const registerSelect2 = select2Module.default || select2Module;
        registerSelect2(window, $);
        $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
    }

    if ($('.datatable').length > 0) {
        await import('datatables.net-bs5');

        $('.datatable').each(function () {
            const hasServerPagination = $(this).closest('.card').find('.pagination').length > 0;

            if (!hasServerPagination) {
                $(this).DataTable({
                    language: {
                        search: 'Cari:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                        infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                        infoFiltered: '(difilter dari _MAX_ total data)',
                        zeroRecords: 'Tidak ada data yang cocok',
                        emptyTable: 'Tidak ada data tersedia',
                        paginate: {
                            first: 'Pertama',
                            previous: 'Sebelumnya',
                            next: 'Berikutnya',
                            last: 'Terakhir',
                        },
                    },
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                });
            }
        });
    }
});

Alpine.start();
