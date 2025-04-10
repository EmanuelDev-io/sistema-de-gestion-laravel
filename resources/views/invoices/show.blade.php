@extends('layouts.app')

@section('title', 'Factura #' . ($invoice->invoice_number ?? $invoice->id))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Factura #{{ $invoice->invoice_number ?? $invoice->id }}</h1>
        <div>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a Facturas
            </a>
            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash"></i> Eliminar
            </button>
            @if($invoice->file_path)
                <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-success">
                    <i class="bi bi-download"></i> Descargar
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información de la Factura</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">Cuenta:</th>
                            <td>
                                <a href="{{ route('accounts.show', $invoice->account) }}">
                                    {{ $invoice->account->page_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Número de Factura:</th>
                            <td>{{ $invoice->invoice_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Monto:</th>
                            <td>${{ number_format($invoice->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Fecha de Factura:</th>
                            <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Descripción:</th>
                            <td>{{ $invoice->description ?? 'Sin descripción' }}</td>
                        </tr>
                        <tr>
                            <th>Archivo:</th>
                            <td>
                                @if($invoice->file_path)
                                    <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-download"></i> Descargar Archivo
                                    </a>
                                @else
                                    <span class="text-muted">No hay archivo adjunto</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Creación:</th>
                            <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Actualización:</th>
                            <td>{{ $invoice->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Información de la Cuenta</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Nombre:</th>
                            <td>{{ $invoice->account->page_name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $invoice->account->outlook_email }}</td>
                        </tr>
                        <tr>
                            <th>Vencimiento:</th>
                            <td>
                                {{ $invoice->account->expiration_date->format('d/m/Y') }}
                                @if($invoice->account->expiration_date < now())
                                    <span class="badge bg-danger">Vencida</span>
                                @elseif($invoice->account->expiration_date < now()->addDays(30))
                                    <span class="badge bg-warning">Por vencer</span>
                                @else
                                    <span class="badge bg-success">Activa</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                    <div class="d-grid gap-2">
                        <a href="{{ route('accounts.show', $invoice->account) }}" class="btn btn-primary">
                            <i class="bi bi-eye"></i> Ver Detalles de la Cuenta
                        </a>
                    </div>
                </div>
            </div>

            @if($invoice->file_path && pathinfo(storage_path('app/public/' . $invoice->file_path), PATHINFO_EXTENSION) != 'pdf')
                <div class="card shadow mt-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Vista Previa</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('storage/' . $invoice->file_path) }}" class="img-fluid" alt="Vista previa de la factura">
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
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
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
