<header class="page-header d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2>{{ $company->user->name }} - {{ $company->identification_number }}</h2>
    </div>
    <div>
        <a href="{{ route('company.production.index', $company->identification_number) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>
</header>
<div class="card mt-2">
    @include('partials.payrolls.table')
</div>

@push('scripts')
<script>
</script>
@endpush
