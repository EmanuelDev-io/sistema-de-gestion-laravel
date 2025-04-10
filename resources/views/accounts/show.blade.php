@extends('layouts.app')

@section('title', $account->page_name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $account->page_name }}</h1>
        <div>
            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a Cuentas
            </a>
            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información de la Cuenta</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 40%">Nombre de la Página:</th>
                            <td>{{ $account->page_name }}</td>
                        </tr>
                        <tr>
                            <th>Email de Outlook:</th>
                            <td>{{ $account->outlook_email }}</td>
                        </tr>
                        <tr>
                            <th>Contraseña de Outlook:</th>
                            <td>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="outlookPassword" value="{{ $account->outlook_password }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('outlookPassword')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Contraseña de Hostinger:</th>
                            <td>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="hostingerPassword" value="{{ $account->hostinger_password }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('hostingerPassword')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Compra:</th>
                            <td>{{ $account->purchase_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Vencimiento:</th>
                            <td>
                                {{ $account->expiration_date->format('d/m/Y') }}
                                @if($account->expiration_date < now())
                                    <span class="badge bg-danger">Vencida</span>
                                @elseif($account->expiration_date < now()->addDays(30))
                                    <span class="badge bg-warning">Por vencer</span>
                                @else
                                    <span class="badge bg-success">Activa</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tiempo Restante:</th>
                            <td>
                                @if($account->expiration_date < now())
                                    <span class="text-danger">Vencida hace {{ now()->diffInDays($account->expiration_date) }} días</span>
                                @else
                                    {{ now()->diffInDays($account->expiration_date) }} días
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Resumen de Facturas</h5>
                    <a href="{{ route('invoices.create') }}?account_id={{ $account->id }}&from_account=1" class="btn btn-sm btn-light">
                        <i class="bi bi-plus-circle"></i> Nueva Factura
                    </a>
                </div>
                <div class="card-body">
                    @if($invoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Número</th>
                                        <th>Monto</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                                            <td>{{ $invoice->invoice_number ?? 'N/A' }}</td>
                                            <td>${{ number_format($invoice->amount, 2) }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('invoices.edit', $invoice) }}?from_account=1" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteInvoiceModal{{ $invoice->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Modal de confirmación de eliminación de factura -->
                                                <div class="modal fade" id="deleteInvoiceModal{{ $invoice->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Confirmar eliminación</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                ¿Está seguro de que desea eliminar esta factura? Esta acción no se puede deshacer.
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="from_account" value="1">
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
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">Total:</th>
                                        <th>${{ number_format($invoices->sum('amount'), 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-center">No hay facturas registradas para esta cuenta.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('invoices.create') }}?account_id={{ $account->id }}&from_account=1" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Crear Primera Factura
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación de cuenta -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar la cuenta <strong>{{ $account->page_name }}</strong>? Esta acción no se puede deshacer y eliminará todas las facturas asociadas.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('accounts.destroy', $account) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
        } else {
            input.type = "password";
        }
    }
</script>
@endsection
