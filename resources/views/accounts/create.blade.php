@extends('layouts.app')

@section('title', 'Crear Cuenta')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Crear Nueva Cuenta</h1>
        <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Cuentas
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('accounts.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="page_name" class="form-label">Nombre de la Página *</label>
                        <input type="text" class="form-control @error('page_name') is-invalid @enderror" id="page_name" name="page_name" value="{{ old('page_name') }}" required>
                        @error('page_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="outlook_email" class="form-label">Email de Outlook *</label>
                        <input type="email" class="form-control @error('outlook_email') is-invalid @enderror" id="outlook_email" name="outlook_email" value="{{ old('outlook_email') }}" required>
                        @error('outlook_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="outlook_password" class="form-label">Contraseña de Outlook *</label>
                        <input type="password" class="form-control @error('outlook_password') is-invalid @enderror" id="outlook_password" name="outlook_password" required>
                        @error('outlook_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="hostinger_password" class="form-label">Contraseña de Hostinger *</label>
                        <input type="password" class="form-control @error('hostinger_password') is-invalid @enderror" id="hostinger_password" name="hostinger_password" required>
                        @error('hostinger_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="purchase_date" class="form-label">Fecha de Compra *</label>
                        <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" required>
                        @error('purchase_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="expiration_date" class="form-label">Fecha de Vencimiento *</label>
                        <input type="date" class="form-control @error('expiration_date') is-invalid @enderror" id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}" required>
                        @error('expiration_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
