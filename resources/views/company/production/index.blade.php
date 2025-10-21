@extends('layouts.app')

@push('styles')
<style>
/* Estilos para los botones del menú */
button.hab-menu-button {
    background: #fff;
    border: none;
    border-radius: 20px;
    width: 286px;
    height: 286px;
    padding: 25px 30px 55px;
    font-size: 18px;
    font-weight: bold;
    line-height: 26px;
    text-align: center;
    color: #2B323D;
    box-shadow: 0px 7px 16px -7px rgba(0, 0, 0, 0.75);
    -webkit-box-shadow: 0px 7px 16px -7px rgba(0,0,0,0.75);
    -moz-box-shadow: 0px 7px 16px -7px rgba(0,0,0,0.75);
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 15px;
}

button.hab-menu-button:hover {
    transform: translateY(-2px);
    box-shadow: 0px 9px 20px -7px rgba(0, 0, 0, 0.85);
    background: linear-gradient(90deg, #4170d7ff, #00B4DC);
    color: white;
}

button.hab-menu-button img {
    display: block;
    margin: 0 auto 15px;
}

/* Container para los botones */
.hab-menu-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    margin: 30px 0;
}

/* Estilos para el header */
.page-header h2 {
    color: #2B323D;
    font-weight: 600;
    margin-bottom: 0;
}

/* Estilos para el subtítulo */
.sub-title {
    font-size: 22px;
    font-weight: bold;
    line-height: 30px;
    text-align: left;
    color: #262944;
    margin: 20px 0;
}

/* Mejorar el hr */
hr {
    border: none;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ddd, transparent);
    margin: 30px 0;
}

/* Container principal */
.RadianContainerBegin {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 30px;
    margin: 20px 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Responsive */
@media (max-width: 768px) {
    button.hab-menu-button {
        width: 250px;
        height: 250px;
        margin: 10px;
    }
    
    .hab-menu-container {
        gap: 10px;
    }
}
</style>
@endpush

@section('content')
<header class="page-header">
    <h2>Tipos de Documentos - {{ $company->identification_number }}</h2>
    <div class="right-wrapper text-end">
        <a href="{{ route('home') }}" class="btn btn-outline-primary mt-1 mr-2">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>
</header>

<div id="panel-form" class="container-fluid RadianContainerBegin">
    <p class="sub-title">Seleccione el tipo de documento:</p>
    <hr>

    <div class="hab-menu-container">
        <!-- Facturas Electrónicas de Venta (FV) -->
        <button class="hab-menu-button" onclick="window.location.href='{{ route('company.production.invoice.index', $company->identification_number) }}'" 
                id="1" contributortype="1" operationmode="0">
            <img src="{{ asset('production/factura-electronica-icon.svg') }}" height="130">
            Factura electrónica
        </button>

        <!-- Nómina Electrónica (NE) -->
        <button class="hab-menu-button" onclick="window.location.href='{{ route('company.production.payroll.index', $company->identification_number) }}'" 
                contributortype="2" operationmode="1" id="2">
            <img src="{{ asset('production/nomina-electronica-icon.svg') }}" height="130">
            Nómina electrónica
        </button>

        <!-- Documentos Soporte (DS) -->
        <button class="hab-menu-button" onclick="window.location.href='{{ route('company.production.support.index', $company->identification_number) }}'" 
                contributortype="3" operationmode="1" electronicdocumentid="3">
            <img src="{{ asset('production/documento-soporte-icon.svg') }}" height="130">
            Documento soporte
        </button>

        <!-- Eventos RADIAN -->
        <button class="hab-menu-button" onclick="window.location.href='{{ route('company.production.event.index', $company->identification_number) }}'" 
                contributortype="4" operationmode="1" id="4">
            <img src="{{ asset('production/eventos-radian-icon.svg') }}" height="130">
            Eventos RADIAN
        </button>

        <!-- Documento Equivalente Electrónico (DE) -->
        <button class="hab-menu-button" onclick="window.location.href='{{ route('company.production.pos.index', $company->identification_number) }}'" 
                contributortype="3" operationmode="1" id="3">
            <img src="{{ asset('production/documentos-equivalentes-icon.svg') }}" height="130">
            Documentos equivalentes
        </button>
    </div>
</div>
@endsection