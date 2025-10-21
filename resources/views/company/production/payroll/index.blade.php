@extends('layouts.app')

@section('content')
<div class="bg-light border-bottom mb-4 p-4 rounded">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="text-dark mb-1 fw-bold">
                Nómina Electrónica
            </h2>
            <p class="text-muted mb-0">
                <i class="fas fa-building me-2"></i>{{ $company->identification_number }}
            </p>
        </div>
        <div>
            <a href="{{ route('company.production.index', $company->identification_number) }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2"></i>
                    Configuración de Software - Nómina Electrónica
                </h5>
            </div>
            <div class="card-body">
                <!-- Formulario para modificar ID y PIN del software -->
                <form action="{{ route('company.production.software.store', [$company->identification_number, 'payroll']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="software_id" class="form-label">
                                    <strong>ID Software <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="software_id"
                                       name="id"
                                       value="{{ $environmentStatus['has_software'] ? $environmentStatus['software_info']['identifier'] : '' }}"
                                       required
                                       placeholder="Ej: 12345678-1234-1234-1234-123456789012">
                                <small class="form-text text-muted">ID único del software proporcionado por la DIAN</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="software_pin" class="form-label">
                                    <strong>PIN Software <span class="text-danger">*</span></strong>
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="software_pin"
                                       name="pin"
                                       value="{{ $environmentStatus['has_software'] ? $environmentStatus['software_info']['pin'] : '' }}"
                                       required
                                       placeholder="Ej: 12345">
                                <small class="form-text text-muted">PIN del software proporcionado por la DIAN</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>
                            {{ $environmentStatus['has_software'] ? 'Actualizar' : 'Crear' }} Software
                        </button>
                    </div>
                </form>

                <hr>

                <!-- Formulario para cambiar el estado del ambiente -->
                <form action="{{ route('company.production.environment.update', [$company->identification_number, 'payroll']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label"><strong>Estado del Ambiente:</strong></label>
                        <select name="environment_id" class="form-select" onchange="this.form.submit()">
                            <option value="2" {{ $environmentStatus['environment_id'] == 2 ? 'selected' : '' }}>Habilitación</option>
                            <option value="1" {{ $environmentStatus['environment_id'] == 1 ? 'selected' : '' }}>Producción</option>
                        </select>
                    </div>
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
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Paso a Producción Nómina --}}
@if($environmentStatus['environment_id'] != 1)
<div class="card border mb-4">
    <div class="card-body">
        <h3>Software de Nómina Electrónica</h3>
        <form id="productionFormPayroll" method="POST" action="#" autocomplete="off">
            @csrf
            <div class="form-group">
                <label for="test_set_id">Set de Pruebas DIAN</label>
                <input type="text" class="form-control" id="test_set_id" name="test_set_id" required placeholder="Ingrese el TestSetId entregado por la DIAN">
            </div>
            <button type="submit" class="btn btn-warning mt-2" id="btnIniciarPayroll">Iniciar Paso a Producción Nómina</button>
        </form>
        <div id="production-steps-payroll" style="display:none;">
            <div id="step1-payroll" class="mb-3">
                <strong>1. Enviar 4 Nóminas y 4 Ajustes</strong>
                <div class="status"></div>
            </div>
            <div id="step2-payroll" class="mb-3">
                <strong>2. Consultar ZipKeys</strong>
                <div class="status"></div>
            </div>
            <div id="step3-payroll" class="mb-3">
                <strong>3. Cambiar Ambiente a Producción</strong>
                <div class="status"></div>
            </div>
        </div>
        <div id="finalMessagePayroll" class="mt-4" style="display:none;"></div>
    </div>
</div>
@endif
@endsection
@push('scripts')
<script>
function setStepStatusPayroll(step, status, message = '') {
    const el = document.querySelector('#' + step + ' .status');
    if (status === 'loading') {
        el.innerHTML = '<span class="text-info"><i class="fa fa-spinner fa-spin"></i> Procesando...</span>';
    } else if (status === 'success') {
        el.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> ' + message + '</span>';
    } else if (status === 'error') {
        el.innerHTML = '<span class="text-danger"><i class="fa fa-times-circle"></i> ' + message + '</span>';
    } else {
        el.innerHTML = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productionFormPayroll');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('production-steps-payroll').style.display = 'block';
            document.getElementById('finalMessagePayroll').style.display = 'block';
            document.getElementById('finalMessagePayroll').innerHTML = '';
            setStepStatusPayroll('step1-payroll', 'loading');
            setStepStatusPayroll('step2-payroll', '');
            setStepStatusPayroll('step3-payroll', '');

            const testSetId = document.getElementById('test_set_id').value;
            const url = "{{ route('company.production.process', $company->identification_number) }}";
            const token = '{{ csrf_token() }}';

            // Paso 1: Enviar 4 nóminas y 4 ajustes
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    test_set_id: testSetId,
                    step: 1,
                    type: 'payroll'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    setStepStatusPayroll('step1-payroll', 'error', data.error);
                    document.getElementById('finalMessagePayroll').innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                    return;
                }
                setStepStatusPayroll('step1-payroll', 'success', 'Documentos enviados correctamente');
                // Paso 2: Consultar ZipKeys
                setStepStatusPayroll('step2-payroll', 'loading');

                // Si es payroll, consultar todos los zipkeys
                if (Array.isArray(data.zipkeys)) {
                    let zipkeys = data.zipkeys;
                    let results = [];
                    let errors = [];
                    let completed = 0;

                    zipkeys.forEach(function(zipkey, idx) {
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                test_set_id: testSetId,
                                step: 2,
                                zipkey: zipkey,
                                type: 'payroll'
                            })
                        })
                        .then(res2 => res2.json())
                        .then(data2 => {
                            completed++;
                            if (data2.error) {
                                errors.push('ZipKey ' + (idx+1) + ': ' + data2.error);
                            } else {
                                results.push('ZipKey ' + (idx+1) + ': OK');
                            }
                            if (completed === zipkeys.length) {
                                if (errors.length > 0) {
                                    setStepStatusPayroll('step2-payroll', 'error', errors.join('<br>'));
                                    document.getElementById('finalMessagePayroll').innerHTML = '<div class="alert alert-danger">' + errors.join('<br>') + '</div>';
                                } else {
                                    setStepStatusPayroll('step2-payroll', 'success', 'ZipKeys consultados correctamente');
                                    // Paso 3: Cambiar ambiente
                                    setStepStatusPayroll('step3-payroll', 'loading');
                                    fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            test_set_id: testSetId,
                                            step: 3,
                                            type: 'payroll'
                                        })
                                    })
                                    .then(res3 => res3.json())
                                    .then(data3 => {
                                        if (data3.error) {
                                            setStepStatusPayroll('step3-payroll', 'error', data3.error);
                                            document.getElementById('finalMessagePayroll').innerHTML = '<div class="alert alert-danger">' + data3.error + '</div>';
                                            return;
                                        }
                                        setStepStatusPayroll('step3-payroll', 'success', 'Ambiente cambiado a producción correctamente');
                                        document.getElementById('finalMessagePayroll').innerHTML = '<div class="alert alert-success">¡Proceso completado correctamente!</div>';
                                    });
                                }
                            }
                        });
                    });
                } else {
                    setStepStatusPayroll('step2-payroll', 'loading');
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            test_set_id: testSetId,
                            step: 2,
                            zipkey: data.zipkey,
                            type: 'payroll'
                        })
                    })
                    .then(res2 => res2.json())
                    .then(data2 => {
                        if (data2.error) {
                            setStepStatusPayroll('step2-payroll', 'error', data2.error);
                            document.getElementById('finalMessagePayroll').innerHTML = '<div class="alert alert-danger">' + data2.error + '</div>';
                            return;
                        }
                        setStepStatusPayroll('step2-payroll', 'success', 'ZipKeys consultados correctamente');
                        // Paso 3: Cambiar ambiente
                        setStepStatusPayroll('step3-payroll', 'loading');
                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                test_set_id: testSetId,
                                step: 3,
                                type: 'payroll'
                            })
                        })
                        .then(res3 => res3.json())
                        .then(data3 => {
                            if (data3.error) {
                                setStepStatusPayroll('step3-payroll', 'error', data3.error);
                                document.getElementById('finalMessagePayroll').innerHTML = '<div class="alert alert-danger">' + data3.error + '</div>';
                                return;
                            }
                            setStepStatusPayroll('step3-payroll', 'success', 'Ambiente cambiado a producción correctamente');
                            document.getElementById('finalMessagePayroll').innerHTML = '<div class="alert alert-success">¡Proceso completado correctamente!</div>';
                        });
                    });
                }
            })
            .catch(err => {
                setStepStatusPayroll('step1-payroll', 'error', 'Error inesperado');
                document.getElementById('finalMessagePayroll').innerHTML = '<div class="alert alert-danger">Error inesperado</div>';
                console.error('Error en el proceso de paso a producción nómina:', err);
            });
        });
    }
});
</script>
@endpush
@push('styles')
<style>
/* Fondo y contenedores */
.bg-light.border-bottom {
    background: #f8f9fa !important;
    border-radius: 15px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.card {
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: none;
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
.btn-outline-primary,
.btn-primary,
.btn-info,
.btn-success,
.btn-warning,
.btn-danger {
    border-radius: 20px;
    font-weight: bold;
    font-size: 16px;
    padding: 8px 24px;
    transition: all 0.3s;
}
.btn-outline-primary {
    border-color: #4170d7ff;
    color: #4170d7ff;
    background: #fff;
}
.btn-outline-primary:hover {
    background: linear-gradient(90deg, #4170d7ff, #00B4DC);
    color: #fff;
    border-color: #4170d7ff;
}
.btn-primary,
.btn-info,
.btn-success,
.btn-warning,
.btn-danger {
    background: linear-gradient(90deg, #4170d7ff, #00B4DC);
    border: none;
    color: #fff;
}
.btn-primary:hover,
.btn-info:hover,
.btn-success:hover,
.btn-warning:hover,
.btn-danger:hover {
    background: linear-gradient(90deg, #00B4DC, #4170d7ff);
    color: #fff;
}
.form-label strong {
    color: #4170d7ff;
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