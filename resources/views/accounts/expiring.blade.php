@extends('layouts.app')

@section('title', 'Cuentas por Vencer')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Cuentas por Vencer (Próximos 30 días)</h1>
        <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Cuentas
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
                                <th>Fecha de Vencimiento</th>
                                <th>Días Restantes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                                <tr class="{{ $account->expiration_date->diffInDays(now()) <= 7 ? 'table-danger' : 'table-warning' }}">
                                    <td>{{ $account->page_name }}</td>
                                    <td>{{ $account->outlook_email }}</td>
                                    <td>{{ $account->expiration_date->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>{{ $account->expiration_date->diffInDays(now()) }} días</strong>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('accounts.show', $account) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
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
        <div class="alert alert-success">
            No hay cuentas que venzan en los próximos 30 días.
        </div>
    @endif
</div>
@endsection
