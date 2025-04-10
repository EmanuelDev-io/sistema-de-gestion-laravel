@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Dashboard</h1>
        
        <div class="date-filter">
            <div class="d-flex align-items-center">
                <div class="me-2">
                    <label for="start_date" class="form-label mb-0 me-2">Desde:</label>
                    <input type="date" class="form-control" id="start_date" value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                </div>
                <div>
                    <label for="end_date" class="form-label mb-0 me-2">Hasta:</label>
                    <input type="date" class="form-control" id="end_date" value="{{ date('Y-m-d') }}">
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body p-3">
                    <div class="welcome-card">
                        <div class="avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="welcome-text">
                            <h5>Bienvenido al Sistema de Ingresos</h5>
                            <p>Gestione sus cuentas y facturas de manera eficiente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Sistema de Ingresos</h6>
                            <small class="text-muted">v1.0.0</small>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-github"></i> GitHub
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-dashboard">
                <div class="card-body stat-card primary">
                    <div class="stat-title">Total de Cuentas</div>
                    <div class="stat-value">{{ $totalAccounts }}</div>
                    <div class="stat-desc">
                        <i class="bi bi-globe"></i> Cuentas activas
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-dashboard">
                <div class="card-body stat-card success">
                    <div class="stat-title">Total de Facturas</div>
                    <div class="stat-value">{{ $totalInvoices }}</div>
                    <div class="stat-desc">
                        <i class="bi bi-receipt"></i> Facturas registradas
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-dashboard">
                <div class="card-body stat-card warning">
                    <div class="stat-title">Cuentas por Vencer</div>
                    <div class="stat-value">{{ $expiringAccounts }}</div>
                    <div class="stat-desc">
                        <i class="bi bi-exclamation-triangle"></i> En los próximos 30 días
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-dashboard">
                <div class="card-body stat-card danger">
                    <div class="stat-title">Total Facturado</div>
                    <div class="stat-value">${{ number_format($totalAmount, 2) }}</div>
                    <div class="stat-desc">
                        <i class="bi bi-currency-dollar"></i> Ingresos totales
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold">Ingresos Mensuales</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold">Cuentas por Vencer</h6>
                    <a href="{{ route('accounts.expiring') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
                </div>
                <div class="card-body">
                    @if($soonExpiringAccounts->count() > 0)
                        <div class="list-group">
                            @foreach($soonExpiringAccounts as $account)
                                <a href="{{ route('accounts.show', $account) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $account->page_name }}</h6>
                                        <small>{{ $account->expiration_date->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 small">Vence: {{ $account->expiration_date->format('d/m/Y') }}</p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center">No hay cuentas por vencer próximamente.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold">Facturas Recientes</h6>
                    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
                </div>
                <div class="card-body">
                    @if($recentInvoices->count() > 0)
                        <div class="list-group">
                            @foreach($recentInvoices as $invoice)
                                <a href="{{ route('invoices.show', $invoice) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $invoice->account->page_name }}</h6>
                                        <small>${{ number_format($invoice->amount, 2) }}</small>
                                    </div>
                                    <p class="mb-1 small">{{ $invoice->invoice_date->format('d/m/Y') }} - {{ $invoice->description ?? 'Sin descripción' }}</p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center">No hay facturas recientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración del tema oscuro para Chart.js
        Chart.defaults.color = '#9ca3af';
        Chart.defaults.borderColor = '#374151';
        
        // Area Chart
        var ctx = document.getElementById("myAreaChart");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($monthlyInvoices as $invoice)
                        "{{ date('M Y', mktime(0, 0, 0, $invoice->month, 1, $invoice->year)) }}",
                    @endforeach
                ],
                datasets: [{
                    label: "Ingresos",
                    lineTension: 0.3,
                    backgroundColor: "rgba(13, 110, 253, 0.05)",
                    borderColor: "rgba(13, 110, 253, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(13, 110, 253, 1)",
                    pointBorderColor: "rgba(13, 110, 253, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(13, 110, 253, 1)",
                    pointHoverBorderColor: "rgba(13, 110, 253, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: [
                        @foreach($monthlyInvoices as $invoice)
                            {{ $invoice->total }},
                        @endforeach
                    ],
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value, index, values) {
                                return '$' + value;
                            }
                        },
                        grid: {
                            color: "rgba(55, 65, 81, 0.5)",
                            zeroLineColor: "rgba(55, 65, 81, 0.5)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    },
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "#1f2937",
                        bodyFontColor: "#f3f4f6",
                        titleMarginBottom: 10,
                        titleFontColor: '#f3f4f6',
                        titleFontSize: 14,
                        borderColor: '#374151',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '$' + context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
