@extends('layouts.app')

@section('title', 'Editar Factura')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Factura</h1>
        <div>
            @if(request()->has('from_account'))
                <a href="{{ route('accounts.show', $invoice->account_id) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a Cuenta
                </a>
            @else
                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a Detalles
                </a>
            @endif
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('invoices.update', $invoice) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                @if(request()->has('from_account'))
                    <input type="hidden" name="from_account" value="1">
                @endif
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="account_id" class="form-label">Cuenta *</label>
                        <select class="form-select @error('account_id') is-invalid @enderror" id="account_id" name="account_id" required>
                            <option value="">Seleccionar cuenta</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ (old('account_id', $invoice->account_id) == $account->id) ? 'selected' : '' }}>
                                    {{ $account->page_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="invoice_number" class="form-label">Número de Factura</label>
                        <input type="text" class="form-control @error('invoice_number') is-invalid @enderror" id="invoice_number" name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number) }}">
                        @error('invoice_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="amount" class="form-label">Monto *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $invoice->amount) }}" step="0.01" min="0" required>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="invoice_date" class="form-label">Fecha de Factura *</label>
                        <input type="date" class="form-control @error('invoice_date') is-invalid @enderror" id="invoice_date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
                        @error('invoice_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $invoice->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="invoice_file" class="form-label">Archivo de Factura</label>
                    <input type="file" class="form-control @error('invoice_file') is-invalid @enderror" id="invoice_file" name="invoice_file">
                    <div class="form-text">
                        Formatos permitidos: PDF, JPG, JPEG, PNG (máx. 2MB)
                        @if($invoice->file_path)
                            <br>Archivo actual: <a href="{{ route('invoices.download', $invoice) }}">Descargar</a>
                            <br><small class="text-muted">Subir un nuevo archivo reemplazará el actual</small>
                        @endif
                    </div>
                    @error('invoice_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Actualizar Factura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
