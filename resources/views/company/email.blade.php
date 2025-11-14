{{-- filepath: e:\www\facturalatam-apidian\resources\views\company\email.blade.php --}}
@extends('layouts.app')

@section('content')
<header class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h2>
            Configuración de Correo
        </h2>
        <br>
        <span class="text-muted">{{ $company->user->name }} - {{ $company->user->email }} - {{ $company->identification_number }}-{{ $company->dv }}</span>
    </div>
    <div class="right-wrapper text-right mt-auto pb-1">
        <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>
</header>

<div class="card card-config">
    <div class="card-header">
        <h5 class="m-0">
            @if($emailConfig['has_custom_config'])
                <span>Configuración personalizada</span>
            @else
                <span>Usando configuración general del sistema</span>
            @endif
        </h5>
    </div>

    <div class="card-body card-body-config">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 6l-12 12" /><path d="M6 6l12 12" /></svg></span>
                </button>
            </div>
        @endif

        <form method="POST" action="{{ route('company.email.store', $company->id) }}">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mail_host">Servidor SMTP *</label>
                        <input type="text"
                               class="form-control @error('mail_host') is-invalid @enderror"
                               id="mail_host"
                               name="mail_host"
                               value="{{ old('mail_host', $emailConfig['mail_host']) }}"
                               required>
                        @error('mail_host')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="mail_port">Puerto *</label>
                        <input type="number"
                               class="form-control @error('mail_port') is-invalid @enderror"
                               id="mail_port"
                               name="mail_port"
                               value="{{ old('mail_port', $emailConfig['mail_port']) }}"
                               required>
                        @error('mail_port')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="mail_encryption">Encriptación *</label>
                        <select class="form-control @error('mail_encryption') is-invalid @enderror"
                                id="mail_encryption"
                                name="mail_encryption"
                                required>
                            <option value="tls" {{ old('mail_encryption', $emailConfig['mail_encryption']) == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('mail_encryption', $emailConfig['mail_encryption']) == 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                        @error('mail_encryption')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mail_username">Usuario *</label>
                        <input type="text"
                               class="form-control @error('mail_username') is-invalid @enderror"
                               id="mail_username"
                               name="mail_username"
                               value="{{ old('mail_username', $emailConfig['mail_username']) }}"
                               required>
                        @error('mail_username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mail_password">Contraseña *</label>
                        <input type="password"
                               class="form-control @error('mail_password') is-invalid @enderror"
                               id="mail_password"
                               name="mail_password"
                               placeholder="{{ $emailConfig['has_custom_config'] ? 'Ingrese nueva contraseña para cambiar' : 'Contraseña' }}"
                               {{ !$emailConfig['has_custom_config'] ? 'required' : '' }}>
                        @error('mail_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($emailConfig['has_custom_config'])
                            <small class="form-text text-muted">Deje vacío para mantener la contraseña actual</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-right">
                    <a href="{{ route('home') }}" class="btn btn-secondary mr-2 text-white">Volver</a>
                    <button type="submit" class="btn btn-primary">Guardar Configuración</button>                    
                </div>
            </div>
        </form>
    </div>
</div>

{{-- @if($emailConfig['has_custom_config'])
<div class="card border mt-3">
    <div class="card-header">
        <h6 class="card-title mb-0">Configuración Actual</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <strong>Servidor:</strong> {{ $emailConfig['mail_host'] }}
            </div>
            <div class="col-md-2">
                <strong>Puerto:</strong> {{ $emailConfig['mail_port'] }}
            </div>
            <div class="col-md-3">
                <strong>Usuario:</strong> {{ $emailConfig['mail_username'] }}
            </div>
            <div class="col-md-3">
                <strong>Encriptación:</strong> {{ strtoupper($emailConfig['mail_encryption']) }}
            </div>
        </div>
    </div>
</div>
@endif --}}
@endsection