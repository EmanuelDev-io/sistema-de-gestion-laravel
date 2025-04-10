@extends('layouts.app')

@section('title', 'Cuentas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Cuentas</h1>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Cuenta
        </a>
    </div>

    @if($accounts->count() > 0)
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Página</th>
                                <th>Email</th>
                                <th>Fecha de Compra</th>
                                <th>Fecha de Vencimiento</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                                <tr>
                                    <td>{{ $account->page_name }}</td>
                                    <td>{{ $account->outlook_email }}</td>
                                    <td>{{ $account->purchase_date->format('d/m/Y') }}</td>
                                    <td>{{ $account->expiration_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($account->expiration_date < now())
                                            <span class="badge bg-danger">Vencida</span>
                                        @elseif($account->expiration_date < now()->addDays(30))
                                            <span class="badge bg-warning">Por vencer</span>
                                        @else
                                            <span class="badge bg-success">Activa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('accounts.show', $account) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $account->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal de confirmación de eliminación -->
                                        <div class="modal fade" id="deleteModal{{ $account->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $account->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $account->id }}">Confirmar eliminación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Está seguro de que desea eliminar la cuenta <strong>{{ $account->page_name }}</strong>? Esta acción no se puede deshacer y eliminará todas las facturas asociadas.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Eliminar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            No hay cuentas registradas. <a href="{{ route('accounts.create') }}">Crear una nueva cuenta</a>.
        </div>
    @endif
</div>
@endsection
