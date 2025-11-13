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

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert">
        <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg></span>
    </button>
</div>
@endif

<div class="row mt-3">
    <!-- Estado del Ambiente para Eventos -->
    <div class="col-md-12 mb-4">
        <div class="card card-config">
            <div class="card-header">
                <h5 class="mb-0 m-0">
                    <i class="fas fa-server me-2"></i>
                    Estado del Ambiente - Eventos RADIAN
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Información:</strong> Los eventos RADIAN no requieren software específico. Se manejan directamente a través de la API de la DIAN para aceptación, rechazo y recibo de documentos.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <form action="{{ route('company.production.environment.update', [$company->identification_number, 'event']) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label"><strong>Estado del Ambiente:</strong></label>
                                <select name="environment_id" class="form-select" onchange="this.form.submit()">
                                    <option value="2" {{ $environmentStatus['environment_id'] == 2 ? 'selected' : '' }}>Habilitación</option>
                                    <option value="1" {{ $environmentStatus['environment_id'] == 1 ? 'selected' : '' }}>Producción</option>
                                </select>
                            </div>
                        </form>
                        <div class="mt-2">
                            @if($environmentStatus['environment_id'] == 1)
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Producción
                                </span>
                            @else
                                <span class="badge bg-warning fs-6">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Habilitación
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
/* Fondo y contenedores */
.bg-light.border-bottom {
    background: #f8f9fa !important;
    border-radius: 15px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.card-header.bg-primary,
.card-header.bg-info,
.card-header.bg-success,
.card-header.bg-warning,
.card-header.bg-danger {
    background: linear-gradient(90deg, #4170d7ff, #00B4DC) !important;
    border-radius: 15px 15px 0 0;
    border: none;
}
.card-header h5 {
    font-weight: 600;
    letter-spacing: 0.5px;
}

.form-control:focus {
    border-color: #4170d7ff;
    box-shadow: 0 0 0 0.2rem rgba(65,112,215,0.15);
}
.badge.bg-success, .badge.bg-warning {
    border-radius: 12px;
    padding: 8px 18px;
    font-size: 16px;
    font-weight: 500;
}
.badge.bg-success {
    background: linear-gradient(90deg, #4170d7ff, #00B4DC);
    color: #fff;
}
.badge.bg-warning {
    background: linear-gradient(90deg, #FFD600, #FFB400);
    color: #262944;
}
hr {
    border: none;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ddd, transparent);
    margin: 30px 0;
}
@media (max-width: 768px) {
    .bg-light.border-bottom, .card {
        padding: 10px !important;
    }
    .card-header {
        padding: 12px 16px !important;
    }
}
</style>
@endpush