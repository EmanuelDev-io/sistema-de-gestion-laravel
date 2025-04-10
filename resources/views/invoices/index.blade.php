@extends('layouts.app')

@section('title', 'Facturas')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Facturas</h1>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Factura
        </a>
    </div>

    @if($invoices->count() > 0)
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cuenta</th>
                                <th>Número</th>
                                <th>Monto</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('accounts.show', $invoice->account) }}">
                                            {{ $invoice->account->page_name }}
                                        </a>
                                    </td>
                                    <td>{{ $invoice->invoice_number ?? 'N/A' }}</td>
                                    <td>${{ number_format($invoice->amount, 2) }}</td>
                                    <td>{{ Str::limit($invoice->description, 30) ?? 'Sin descripción' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $invoice->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @if($invoice->file_path)
                                                <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-sm btn-success">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @endif
                                        </div>

                                        <!-- Modal de confirmación de eliminación -->
                                        <div class="modal fade" id="deleteModal{{ $invoice->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $invoice->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteModalLabel{{ $invoice->id }}">Confirmar eliminación</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        ¿Está seguro de que desea eliminar esta factura? Esta acción no se puede deshacer.
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="d-inline">
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
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th>${{ number_format($invoices->sum('amount'), 2) }}</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            No hay facturas registradas. <a href="{{ route('invoices.create') }}">Crear una nueva factura</a>.
        </div>
    @endif
</div>
@endsection
