<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $totalAccounts = Account::count();
        $expiringAccounts = Account::whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>=', now())
            ->count();
        $expiredAccounts = Account::whereDate('expiration_date', '<', now())->count();
        
        $totalInvoices = Invoice::count();
        $totalAmount = Invoice::sum('amount');
        
        // Modificamos esta consulta para que sea compatible con SQLite
        $monthlyInvoices = Invoice::selectRaw("strftime('%Y', invoice_date) as year, strftime('%m', invoice_date) as month, SUM(amount) as total")
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
            
        $recentInvoices = Invoice::with('account')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        $soonExpiringAccounts = Account::whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>=', now())
            ->orderBy('expiration_date')
            ->limit(5)
            ->get();
            
        return view('dashboard', compact(
            'totalAccounts', 
            'expiringAccounts', 
            'expiredAccounts', 
            'totalInvoices', 
            'totalAmount', 
            'monthlyInvoices',
            'recentInvoices',
            'soonExpiringAccounts'
        ));
    }

    /**
     * Get dashboard data for React frontend.
     */
    public function dashboardData()
    {
        $totalAccounts = Account::count();
        $expiringAccounts = Account::whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>=', now())
            ->count();
        $expiredAccounts = Account::whereDate('expiration_date', '<', now())->count();
        
        $totalInvoices = Invoice::count();
        $totalAmount = Invoice::sum('amount');
        
        // Consulta para obtener ingresos mensuales
        $monthlyInvoices = Invoice::selectRaw("strftime('%Y', invoice_date) as year, strftime('%m', invoice_date) as month, SUM(amount) as total")
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($item) {
                // Convertir el mes numérico a nombre del mes
                $monthNames = [
                    '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
                    '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
                    '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
                ];
                
                return [
                    'year' => $item->year,
                    'month' => $monthNames[$item->month] ?? $item->month,
                    'total' => (float) $item->total
                ];
            });
            
        // Facturas recientes
        $recentInvoices = Invoice::with('account')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'account_id' => $invoice->account_id,
                    'account' => [
                        'id' => $invoice->account->id,
                        'page_name' => $invoice->account->page_name
                    ],
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => (float) $invoice->amount,
                    'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
                    'description' => $invoice->description,
                    'has_file' => !empty($invoice->file_path)
                ];
            });
            
        // Cuentas por vencer
        $soonExpiringAccounts = Account::whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>=', now())
            ->orderBy('expiration_date')
            ->limit(5)
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'page_name' => $account->page_name,
                    'outlook_email' => $account->outlook_email,
                    'expiration_date' => $account->expiration_date->format('Y-m-d'),
                    'days_remaining' => $account->expiration_date->diffInDays(now())
                ];
            });
            
        return response()->json([
            'totalAccounts' => $totalAccounts,
            'expiringAccounts' => $expiringAccounts,
            'expiredAccounts' => $expiredAccounts,
            'totalInvoices' => $totalInvoices,
            'totalAmount' => $totalAmount,
            'monthlyInvoices' => $monthlyInvoices,
            'recentInvoices' => $recentInvoices,
            'soonExpiringAccounts' => $soonExpiringAccounts
        ]);
    }
}
