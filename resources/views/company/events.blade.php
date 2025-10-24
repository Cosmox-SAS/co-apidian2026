<header class="page-header d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2>{{ $company->user->name }} - {{ $company->identification_number }}</h2>
    </div>
    <div>
        <a href="{{ route('company.production.index', $company->identification_number) }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>
</header>
<div class="card">
    <div class="card-body p-0">
        @include('partials.events.table')
    </div>
</div>

@push('scripts')
<script>
</script>
@endpush
