@extends('layouts.app')

@section('content')
<header class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-users-group"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1"></path><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M17 10h2a2 2 0 0 1 2 2v1"></path><path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path><path d="M3 13v-1a2 2 0 0 1 2 -2h2"></path></svg>
        </h2>
        <ol class="breadcrumbs">
            <li class="active">
                <span>Listado de Usuarios</span>
            </li> 
            <li class="active">
                <span>
                    {{ $company->user->name }}
                </span>
            </li> 
        </ol>
    </div>
    <div class="right-wrapper text-right">
        <button class="btn btn-primary btn-sm text-white mr-2" data-toggle="modal" data-target="#userModal">Añadir usuario</button>
    </div>
</header>

<div class="card">

    <!-- Users Table -->
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Tipo</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Documento</th>
                    <th class="text-right"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr class="table-light">
                    <td>
                        {{ $user->can_rips ? 'RIPS' : 'Facturación' }}
                        @if($user->id == $company->user->id)
                            <span class="badge bg-primary text-white">Principal</span>
                        @endif
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->document_type_id ? $user->document_type->name : '' }} {{ $user->document_number }}</td>
                    <td class="text-right">
                        @if($user->id != $company->user->id)
                            <button class="btn btn-sm btn-warning"
                                data-toggle="modal" data-target="#userModal"
                                data-id="{{ $user->id }}"
                                data-name="{{ $user->name }}"
                                data-email="{{ $user->email }}"
                                data-document_number="{{ $user->document_number }}"
                                data-document_type_id="{{ $user->document_type_id }}"
                                data-can_rips="{{ $user->can_rips }}"
                                data-can_health="{{ $user->can_health }}"
                                data-code_service_provider="{{ $user->code_service_provider}}"
                                data-url_fevrips="{{ $user->url_fevrips }}">Editar</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-12 d-flex justify-content-end pr-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="userForm" method="POST" action="{{ route('company.users.store', $company->id) }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="userId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Formulario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row">
                        <div class="form-group col-6">
                            <label for="document_type_id">Tipo de documento</label>
                            <select name="document_type_id" id="document_type_id" class="form-control" required>
                                @foreach ($document_types as $documentType)
                                <option value="{{ $documentType->id }}"
                                    {{ old('document_type_id') == $documentType->id ? 'selected' : '' }}>
                                    {{ $documentType->name }}
                                </option>
                                @endforeach
                            </select>
                            @if ($errors->has('document_type_id'))
                                <span class="text-danger">{{ $errors->first('document_type_id') }}</span>
                            @endif
                        </div>

                        <div class="form-group col-6">
                            <label for="document_number">Número de documento</label>
                            <input type="text" name="document_number" id="document_number" class="form-control" value="{{ old('document_number') }}" required>
                            @if ($errors->has('document_number'))
                                <span class="text-danger">{{ $errors->first('document_number') }}</span>
                            @endif
                        </div>

                        <div class="form-group col-6">
                            <label for="name">Nombre</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                            @if ($errors->has('name'))
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-6">
                            <label for="email">Correo electrónico</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                            @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-6">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control">
                            @if ($errors->has('password'))
                                <span class="text-danger">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-6">
                            <label for="password_confirmation">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                            @if ($errors->has('password_confirmation'))
                                <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-6">
                            <label for="code_service_provider">Código de prestador de servicio</label>
                            <input type="text" name="code_service_provider" id="code_service_provider" class="form-control" value="{{ old('code_service_provider') }}">
                            <span class="text-muted"><small>Código de 12 digitos</small></span>
                            @if ($errors->has('code_service_provider'))
                                <span class="text-danger">{{ $errors->first('code_service_provider') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-6">
                            <label for="url_fevrips">URL Validador fev-rips</label>
                            <input type="text" name="url_fevrips" id="url_fevrips" class="form-control" value="{{ old('url_fevrips') }}">
                            @if ($errors->has('url_fevrips'))
                                <span class="text-danger">{{ $errors->first('url_fevrips') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-6 mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="can_rips" id="can_rips_yes" value="1" {{ old('can_rips') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="can_rips_yes">RIPS</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="can_rips" id="can_rips_no" value="0" {{ old('can_rips') == '0' || old('can_rips') === null ? 'checked' : '' }}>
                                <label class="form-check-label" for="can_rips_no">FACTURACIÓN</label>
                            </div>
                            <br>
                            {{-- <input type="checkbox" value="1" name="can_health" id="can_health" {{ old('can_health') ? 'checked' : '' }}> --}}
                            {{-- <label for="can_health">Generar Factura de Sector Salud</label> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Abrir el modal automáticamente si hay errores de validación
    @if ($errors->any())
        $('#userModal').modal('show');
    @endif

    // Configurar el modal para edición o creación
    $('#userModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id') || '';
        var name = button.data('name') || '';
        var email = button.data('email') || '';
        var can_rips = button.data('can_rips') || false;
        var can_health = button.data('can_health') || false;
        var code_service_provider = button.data('code_service_provider') || '';
        var document_type_id = button.data('document_type_id') || '';
        var document_number = button.data('document_number') || '';
        var url_fevrips = button.data('url_fevrips') || '';

        var modal = $(this);
        modal.find('.modal-title').text(id ? 'Editar Usuario' : 'Agregar Usuario');
        modal.find('#userId').val(id);
        modal.find('#name').val(name);
        modal.find('#email').val(email);
        modal.find('#code_service_provider').val(code_service_provider);
        modal.find('#document_type_id').val(document_type_id);
        modal.find('#document_number').val(document_number);
        modal.find('#url_fevrips').val(url_fevrips);

        // Set checkboxes
        // modal.find('#can_rips').prop('checked', can_rips);
        if (can_rips) {
            modal.find('#can_rips_yes').prop('checked', true);
        } else {
            modal.find('#can_rips_no').prop('checked', true);
        }
        modal.find('#can_health').prop('checked', can_health);

        if (id) {
            modal.find('#formMethod').val('PUT');
            modal.find('#userForm').attr('action', '/companies/{{ $company->id }}/users/' + id);
        } else {
            modal.find('#formMethod').val('POST');
            modal.find('#userForm').attr('action', '{{ route('company.users.store', $company->id) }}');
        }
    });

    // Mostrar mensaje de éxito
    @if (session('success'))
        new PNotify({
            text: '{{ session('success') }}',
            type: 'success',
            addclass: 'notification-success',
            delay: 3000
        });
    @endif

    // Mostrar mensaje de error
    @if (session('error'))
        new PNotify({
            text: '{{ session('error') }}',
            type: 'error',
            addclass: 'notification-danger',
            delay: 3000
        });
    @endif
});
</script>
@endpush