@props([
    'editTarget' => null,
    'editHref' => null,
    'editLabel' => 'Edit',
    'resetAction' => null,
    'resetConfirm' => 'Reset data ini?',
    'resetLabel' => 'Reset',
    'deleteAction' => null,
    'deleteConfirm' => 'Hapus data ini?',
    'deleteLabel' => 'Hapus',
    'downloadHref' => null,
    'downloadLabel' => 'Download',
])

<div {{ $attributes->merge(['class' => 'd-inline-flex align-items-center gap-1']) }}>
    @if($editTarget)
        <button type="button" class="btn btn-sm btn-warning btn-icon" data-bs-toggle="modal" data-bs-target="{{ $editTarget }}" title="{{ $editLabel }}" aria-label="{{ $editLabel }}">
            <i class="bi bi-pencil" aria-hidden="true"></i>
        </button>
    @elseif($editHref)
        <a href="{{ $editHref }}" class="btn btn-sm btn-warning btn-icon" title="{{ $editLabel }}" aria-label="{{ $editLabel }}">
            <i class="bi bi-pencil" aria-hidden="true"></i>
        </a>
    @endif

    @if($downloadHref)
        <a href="{{ $downloadHref }}" class="btn btn-sm btn-outline-primary btn-icon" title="{{ $downloadLabel }}" aria-label="{{ $downloadLabel }}">
            <i class="bi bi-download" aria-hidden="true"></i>
        </a>
    @endif

    {{ $slot }}

    @if($resetAction)
        <form action="{{ $resetAction }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-sm btn-outline-secondary btn-icon" data-confirm="{{ $resetConfirm }}" title="{{ $resetLabel }}" aria-label="{{ $resetLabel }}">
                <i class="bi bi-key" aria-hidden="true"></i>
            </button>
        </form>
    @endif

    @if($deleteAction)
        <form action="{{ $deleteAction }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-outline-danger btn-icon" data-confirm="{{ $deleteConfirm }}" title="{{ $deleteLabel }}" aria-label="{{ $deleteLabel }}">
                <i class="bi bi-trash" aria-hidden="true"></i>
            </button>
        </form>
    @endif
</div>
