{{-- filepath: e:\www\facturalatam-apidian\resources\views\company\email.blade.php --}}
@extends('layouts.app')

@section('content')
<header class="page-header">
    <h2>
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path><path d="M3 7l9 6l9 -6"></path></svg>
    </h2>
    <ol class="breadcrumbs">
        <li class="active">
            <span>Configuración de Correo</span>
        </li> 
        <li class="active">
            <span>{{ $company->user->name }} - {{ $company->user->email }} - {{ $company->identification_number }}-{{ $company->dv }}</span>
        </li> 
    </ol>
</header>

<div class="card card-config">
    <div class="card-header">
        <h5 class="card-title mb-0">
            @if($emailConfig['has_custom_config'])
                <small class="text-muted">Configuración personalizada</small>
            @else
                <small class="text-muted">Usando configuración general del sistema</small>
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
                    <span aria-hidden="true">&times;</span>
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
                    <a href="{{ route('home') }}" class="btn btn-secondary ml-2 text-white">Volver</a>
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