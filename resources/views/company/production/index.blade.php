@extends('layouts.app')

@push('styles')
<style>
/* Barra de botones horizontal arriba */
.hab-menu-bar {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    margin-top: 0;
    padding: 0;
}

/* Botón horizontal pequeño */
button.hab-menu-bar-btn {
    background: #fff;
    border: none;
    border-radius: 10px;
    padding: 8px;
    font-size: 15px;
    font-weight: 500;
    color: #2B323D;
    display: flex;
    align-items: center;
    box-shadow: 0px 2px 8px -4px rgba(0,0,0,0.15);
    cursor: pointer;
    transition: all 0.2s;
    min-width: 0;
    margin: 0;
}

button.hab-menu-bar-btn:hover {
    background: linear-gradient(90deg, #4170d7ff, #00B4DC);
    color: #fff;
}

button.hab-menu-bar-btn img {
    height: 32px;
    width: 32px;
    margin-right: 8px;
    margin-bottom: 0;
    display: inline-block;
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
    .hab-menu-bar {
        flex-wrap: wrap;
        gap: 8px;
    }
    button.hab-menu-bar-btn {
        font-size: 14px;
        padding: 8px 10px 8px 8px;
    }
    button.hab-menu-bar-btn img {
        height: 28px;
        width: 28px;
    }
}
</style>
@endpush

@section('content')
<header class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-invoice"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M14 3v4a1 1 0 0 0 1 1h4"></path><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path><path d="M9 7l1 0"></path><path d="M9 13l6 0"></path><path d="M13 17l2 0"></path></svg>
        </h2>
        <ol class="breadcrumbs">
            <li class="active">
                <span>Seleccione el tipo de documento</span>
            </li> 
            <li class="active">
                <span>{{ $company->user->name }} - {{ $company->identification_number }}</span>
            </li> 
        </ol>
    </div>
    <div class="right-wrapper text-right">
        <a href="{{ route('home') }}" class="btn btn-secondary mt-1 mr-2">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>
</header>

<div id="panel-form" class="card-body">
    <div class="hab-menu-bar">
        <button class="hab-menu-bar-btn" onclick="window.location.href='{{ route('company.production.tabs', [$company->identification_number, 'invoice']) }}'">
            <img src="{{ asset('production/factura-electronica-icon.svg') }}" alt="Factura electrónica">
            Factura electrónica
        </button>
        <button class="hab-menu-bar-btn" onclick="window.location.href='{{ route('company.production.tabs', [$company->identification_number, 'payroll']) }}'">
            <img src="{{ asset('production/nomina-electronica-icon.svg') }}" alt="Nómina electrónica">
            Nómina electrónica
        </button>
        <button class="hab-menu-bar-btn" onclick="window.location.href='{{ route('company.production.tabs', [$company->identification_number, 'support']) }}'">
            <img src="{{ asset('production/documento-soporte-icon.svg') }}" alt="Documento soporte">
            Documento soporte
        </button>
        <button class="hab-menu-bar-btn" onclick="window.location.href='{{ route('company.production.tabs', [$company->identification_number, 'event']) }}'">
            <img src="{{ asset('production/eventos-radian-icon.svg') }}" alt="Eventos RADIAN">
            Eventos RADIAN
        </button>
        <button class="hab-menu-bar-btn" onclick="window.location.href='{{ route('company.production.tabs', [$company->identification_number, 'pos']) }}'">
            <img src="{{ asset('production/documentos-equivalentes-icon.svg') }}" alt="Documentos equivalentes">
            Documentos equivalentes
        </button>
    </div>
</div>
@endsection