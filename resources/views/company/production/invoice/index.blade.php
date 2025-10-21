@extends('layouts.app')

@section('content')
<div class="bg-light border-bottom mb-4 p-4 rounded">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="text-dark mb-1 fw-bold">
                Facturas Electrónicas
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
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2"></i>
                    Configuración de Software - Facturas Electrónicas
                </h5>
            </div>
            <div class="card-body">
                <!-- Formulario para modificar ID y PIN del software -->
                <form action="{{ route('company.production.software.store', [$company->identification_number, 'invoice']) }}" method="POST">
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
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            {{ $environmentStatus['has_software'] ? 'Actualizar' : 'Crear' }} Software
                        </button>
                    </div>
                </form>

                <hr>

                <!-- Formulario para cambiar el estado del ambiente -->
                <form action="{{ route('company.production.environment.update', [$company->identification_number, 'invoice']) }}" method="POST">
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
{{-- Paso a Producción --}}
@if($environmentStatus['environment_id'] != 1)
<div class="card border mb-4">
    <div class="card-body">
        <h3>Software de Facturación Electrónica</h3>
        <form id="productionForm" method="POST" action="#" autocomplete="off">
            @csrf
            <div class="form-group">
                <label for="test_set_id">Set de Pruebas DIAN</label>
                <input type="text" class="form-control" id="test_set_id" name="test_set_id" required placeholder="Ingrese el TestSetId entregado por la DIAN">
            </div>
            <button type="submit" class="btn btn-primary mt-2" id="btnIniciar">Iniciar Paso a Producción</button>
        </form>
        <div id="production-steps" style="display:none;">
            <div id="step1" class="mb-3">
                <strong>1. Enviar Factura de Prueba</strong>
                <div class="status"></div>
            </div>
            <div id="step2" class="mb-3">
                <strong>2. Consultar ZipKey</strong>
                <div class="status"></div>
            </div>
            <div id="step3" class="mb-3">
                <strong>3. Cambiar Ambiente a Producción</strong>
                <div class="status"></div>
            </div>
        </div>
        <div id="finalMessage" class="mt-4" style="display:none;"></div>
    </div>
</div>
@endif
{{-- Consulta de Resoluciones y Vista Previa --}}
@if($environmentStatus['environment_id'] == 1)
<div class="card border mt-2">
    <div class="card-body">
        <h3 class="mb-2">
            Consultar Resoluciones Asociadas
            <span
                data-toggle="tooltip"
                data-placement="right"
                title="Aquí puedes consultar las resoluciones asociadas a tu empresa.">
                <i class="fas fa-info-circle text-info" style="cursor:pointer;"></i>
            </span>
            <a href="#" id="btnVistaPrevia" class="ml-2" data-toggle="modal" data-target="#modalVistaPrevia">
                <i class="fas fa-image"></i> Vista Previa
            </a>
        </h3>
        <button id="btnConsultarResoluciones" type="button" class="btn btn-primary">Consultar</button>
        <div id="resolucionesResult" class="mt-3"></div>
    </div>
</div>
@endif

<div class="modal fade" id="modalVistaPrevia" tabindex="-1" role="dialog" aria-labelledby="modalVistaPreviaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-custom-width" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVistaPreviaLabel">Vista previa de Resoluciones</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="carouselPasos" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carouselPasos" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselPasos" data-slide-to="1"></li>
                        <li data-target="#carouselPasos" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner text-center">
                        <div class="carousel-item active">
                            <img src="/resolutions/PASO1.png" class="d-block mx-auto mb-2" style="width: 100%; height: auto;" alt="Paso 1">
                            <div><strong>Paso 1</strong></div>
                        </div>
                        <div class="carousel-item">
                            <img src="/resolutions/PASO2.png" class="d-block mx-auto mb-2" style="width: 100%; height: auto;" alt="Paso 2">
                            <div><strong>Paso 2</strong></div>
                        </div>
                        <div class="carousel-item">
                            <img src="/resolutions/PASO3.png" class="d-block mx-auto mb-2" style="width: 100%; height: auto;" alt="Paso 3">
                            <div><strong>Paso 3</strong></div>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carouselPasos" role="button" data-slide="prev" style="width: 5%; background: rgba(0,0,0,0.2);">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="height: 48px; width: 48px;"></span>
                        <span class="sr-only">Anterior</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselPasos" role="button" data-slide="next" style="width: 5%; background: rgba(0,0,0,0.2);">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="height: 48px; width: 48px;"></span>
                        <span class="sr-only">Siguiente</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    function setStepStatus(step, status, message = '') {
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
        const form = document.getElementById('productionForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                document.getElementById('production-steps').style.display = 'block';
                document.getElementById('finalMessage').style.display = 'block';
                document.getElementById('finalMessage').innerHTML = '';
                setStepStatus('step1', 'loading');
                setStepStatus('step2', '');
                setStepStatus('step3', '');

                const testSetId = document.getElementById('test_set_id').value;
                const url = "{{ route('company.production.process', $company->identification_number) }}";
                const token = '{{ csrf_token() }}';

                // Paso 1: Enviar factura de prueba
                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            test_set_id: testSetId,
                            step: 1
                            type: 'invoice'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) {
                            setStepStatus('step1', 'error', data.error);
                            document.getElementById('finalMessage').innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                            return;
                        }
                        setStepStatus('step1', 'success', 'Factura enviada correctamente');
                        // Paso 2: Consultar ZipKey
                        setStepStatus('step2', 'loading');
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
                                    zipkey: data.zipkey
                                })
                            })
                            .then(res2 => res2.json())
                            .then(data2 => {
                                if (data2.error) {
                                    setStepStatus('step2', 'error', data2.error);
                                    document.getElementById('finalMessage').innerHTML = '<div class="alert alert-danger">' + data2.error + '</div>';
                                    return;
                                }
                                setStepStatus('step2', 'success', 'ZipKey consultado correctamente');
                                // Paso 3: Cambiar ambiente
                                setStepStatus('step3', 'loading');
                                fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': token,
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            test_set_id: testSetId,
                                            step: 3
                                        })
                                    })
                                    .then(res3 => res3.json())
                                    .then(data3 => {
                                        if (data3.error) {
                                            setStepStatus('step3', 'error', data3.error);
                                            document.getElementById('finalMessage').innerHTML = '<div class="alert alert-danger">' + data3.error + '</div>';
                                            return;
                                        }
                                        setStepStatus('step3', 'success', 'Ambiente cambiado a producción correctamente');
                                        document.getElementById('finalMessage').innerHTML = '<div class="alert alert-success">¡Proceso completado correctamente!</div>';
                                    });
                            });
                    })
                    .catch(err => {
                        setStepStatus('step1', 'error', 'Error inesperado');
                        document.getElementById('finalMessage').innerHTML = '<div class="alert alert-danger">Error inesperado</div>';
                        console.error('Error en el proceso de paso a producción:', err);
                    });
            });
        }
    });
</script>
@endpush
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btnConsultarResoluciones');
        if (btn) {
            btn.addEventListener('click', function() {
                const resultDiv = document.getElementById('resolucionesResult');
                resultDiv.innerHTML = 'Consultando...';
                fetch("{{ route('company.production.consult-resolutions', [$company->identification_number, 'invoice']) }}", {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ type: 'invoice' })
                    })
                    .then(response => response.json())
                    .then(data => {
                        let list = [];
                        let opDesc = '';
                        try {
                            const result = data.ResponseDian.Envelope.Body.GetNumberingRangeResponse.GetNumberingRangeResult;
                            opDesc = result.OperationDescription ?? '';
                            if (
                                result.ResponseList &&
                                result.ResponseList.NumberRangeResponse
                            ) {
                                list = result.ResponseList.NumberRangeResponse;
                                if (!Array.isArray(list)) {
                                    list = [list];
                                }
                            }
                        } catch (e) {
                            resultDiv.innerHTML = '<span class="text-danger">No se encontraron resoluciones válidas</span>';
                            return;
                        }
                        if (!list || list.length === 0) {
                            resultDiv.innerHTML = `<span class="text-danger">${opDesc ? opDesc : 'No se encontraron resoluciones válidas.'}</span>`;
                            return;
                        }
                        let html = `
                            <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Prefijo</th>
                                        <th>Número Resolución</th>
                                        <th>Fecha Resolución</th>
                                        <th>Rango</th>
                                        <th>Inicio Vigencia</th>
                                        <th>Fin Vigencia</th>
                                        <th>Clave Técnica</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        list.forEach(item => {
                            const params = new URLSearchParams({
                                prefix: item.Prefix ?? '',
                                resolution: item.ResolutionNumber ?? '',
                                resolution_date: item.ResolutionDate ?? '',
                                from: item.FromNumber ?? '',
                                to: item.ToNumber ?? '',
                                date_from: item.ValidDateFrom ?? '',
                                date_to: item.ValidDateTo ?? '',
                                technical_key: (typeof item.TechnicalKey === 'object' && item.TechnicalKey?._attributes?.nil === 'true') ? '' : (item.TechnicalKey ?? '')
                            }).toString();
                            const createUrl = `/companies/{{ $company->identification_number }}/configuration/resolutions/create?${params}`;
                            html += `
                                <tr>
                                    <td>${item.Prefix ?? ''}</td>
                                    <td>${item.ResolutionNumber ?? ''}</td>
                                    <td>${item.ResolutionDate ?? ''}</td>
                                    <td>${item.FromNumber ?? ''} - ${item.ToNumber ?? ''}</td>
                                    <td>${item.ValidDateFrom ?? ''}</td>
                                    <td>${item.ValidDateTo ?? ''}</td>
                                    <td>${typeof item.TechnicalKey === 'object' && item.TechnicalKey?._attributes?.nil === 'true' ? '' : (item.TechnicalKey ?? '')}</td>
                                    <td>
                                        <a href="${createUrl}" class="btn btn-sm btn-primary text-white" title="Crear resolución">
                                            <i class="fas fa-plus"></i> Crear
                                        </a>
                                    </td>
                                </tr>
                            `;
                        });
                        html += `
                                </tbody>
                            </table>
                            </div>
                        `;
                        resultDiv.innerHTML = html;
                    })
                    .catch(error => {
                        resultDiv.innerHTML = '<span class="text-danger">Error consultando resoluciones</span>';
                    });
            });
        }
    });
</script>
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>
@endpush
@push('styles')
<style>
    .modal-custom-width {
        max-width: 90vw !important;
    }
</style>
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

.card-header.bg-primary {
    background: linear-gradient(90deg, #4170d7ff, #00B4DC) !important;
    border-radius: 15px 15px 0 0;
    border: none;
}

.card-header h5 {
    font-weight: 600;
    letter-spacing: 0.5px;
}

.btn-outline-primary, .btn-primary {
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

.btn-primary {
    background: linear-gradient(90deg, #4170d7ff, #00B4DC);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(90deg, #00B4DC, #4170d7ff);
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

/* Responsive */
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