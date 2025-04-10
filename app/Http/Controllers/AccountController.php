<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    /**
     * Display a listing of the accounts.
     */
    public function index()
    {
        $accounts = Account::orderBy('page_name')->get();
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new account.
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created account in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_name' => 'required|string|max:255',
            'outlook_email' => 'required|email|max:255',
            'outlook_password' => 'required|string|max:255',
            'hostinger_password' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'expiration_date' => 'required|date|after:purchase_date',
        ]);

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Cuenta creada con éxito.');
    }

    /**
     * Display the specified account.
     */
    public function show(Account $account)
    {
        $invoices = $account->invoices()->orderBy('invoice_date', 'desc')->get();
        return view('accounts.show', compact('account', 'invoices'));
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified account in storage.
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'page_name' => 'required|string|max:255',
            'outlook_email' => 'required|email|max:255',
            'outlook_password' => 'required|string|max:255',
            'hostinger_password' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'expiration_date' => 'required|date|after:purchase_date',
        ]);

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Cuenta actualizada con éxito.');
    }

    /**
     * Remove the specified account from storage.
     */
    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Cuenta eliminada con éxito.');
    }

    /**
     * Display accounts that are about to expire.
     */
    public function expiringSoon()
    {
        $thirtyDaysFromNow = now()->addDays(30);
        $accounts = Account::whereDate('expiration_date', '<=', $thirtyDaysFromNow)
            ->whereDate('expiration_date', '>=', now())
            ->orderBy('expiration_date')
            ->get();
            
        return view('accounts.expiring', compact('accounts'));
    }

    /**
     * Display dashboard with summary information.
     */
    public function dashboard()
    {
        $totalAccounts = Account::count();
        $expiringAccounts = Account::whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>=', now())
            ->count();
        $expiredAccounts = Account::whereDate('expiration_date', '<', now())->count();
        
        // Modificamos esta consulta para que sea compatible con SQLite
        $monthlyInvoices = Invoice::selectRaw("strftime('%Y', invoice_date) as year, strftime('%m', invoice_date) as month, SUM(amount) as total")
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
            
        return view('dashboard', compact('totalAccounts', 'expiringAccounts', 'expiredAccounts', 'monthlyInvoices'));
    }

    /**
     * API Methods for React Frontend
     */
    
    public function apiIndex()
    {
        $accounts = Account::orderBy('page_name')->get()->map(function ($account) {
            return [
                'id' => $account->id,
                'page_name' => $account->page_name,
                'outlook_email' => $account->outlook_email,
                'outlook_password' => $account->outlook_password,
                'page_email' => $account->page_email,
                'page_password' => $account->page_password,
                'purchase_date' => $account->purchase_date->format('Y-m-d'),
                'expiration_date' => $account->expiration_date->format('Y-m-d'),
                'days_remaining' => now()->diffInDays($account->expiration_date, false),
                'notes' => $account->notes,
                'invoices_count' => $account->invoices()->count(),
                'total_billed' => $account->invoices()->sum('amount')
            ];
        });
        
        return response()->json($accounts);
    }
    
    public function apiShow(Account $account)
    {
        $account->load('invoices');
        
        $accountData = [
            'id' => $account->id,
            'page_name' => $account->page_name,
            'outlook_email' => $account->outlook_email,
            'outlook_password' => $account->outlook_password,
            'page_email' => $account->page_email,
            'page_password' => $account->page_password,
            'purchase_date' => $account->purchase_date->format('Y-m-d'),
            'expiration_date' => $account->expiration_date->format('Y-m-d'),
            'days_remaining' => now()->diffInDays($account->expiration_date, false),
            'notes' => $account->notes,
            'invoices' => $account->invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'amount' => (float) $invoice->amount,
                    'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
                    'description' => $invoice->description,
                    'has_file' => !empty($invoice->file_path)
                ];
            })
        ];
        
        return response()->json($accountData);
    }
    
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'page_name' => 'required|string|max:255',
            'outlook_email' => 'required|string|max:255',
            'outlook_password' => 'required|string|max:255',
            'page_email' => 'required|string|max:255',
            'page_password' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'expiration_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);
        
        $account = Account::create($validated);
        
        return response()->json([
            'id' => $account->id,
            'page_name' => $account->page_name,
            'outlook_email' => $account->outlook_email,
            'outlook_password' => $account->outlook_password,
            'page_email' => $account->page_email,
            'page_password' => $account->page_password,
            'purchase_date' => $account->purchase_date->format('Y-m-d'),
            'expiration_date' => $account->expiration_date->format('Y-m-d'),
            'notes' => $account->notes
        ], 201);
    }
    
    public function apiUpdate(Request $request, Account $account)
    {
        $validated = $request->validate([
            'page_name' => 'required|string|max:255',
            'outlook_email' => 'required|string|max:255',
            'outlook_password' => 'required|string|max:255',
            'page_email' => 'required|string|max:255',
            'page_password' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'expiration_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);
        
        $account->update($validated);
        
        return response()->json([
            'id' => $account->id,
            'page_name' => $account->page_name,
            'outlook_email' => $account->outlook_email,
            'outlook_password' => $account->outlook_password,
            'page_email' => $account->page_email,
            'page_password' => $account->page_password,
            'purchase_date' => $account->purchase_date->format('Y-m-d'),
            'expiration_date' => $account->expiration_date->format('Y-m-d'),
            'notes' => $account->notes
        ]);
    }
    
    public function apiDestroy(Account $account)
    {
        $account->delete();
        
        return response()->json(null, 204);
    }
    
    public function apiExpiringSoon()
    {
        $accounts = Account::whereDate('expiration_date', '<=', now()->addDays(30))
            ->whereDate('expiration_date', '>=', now())
            ->orderBy('expiration_date')
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'page_name' => $account->page_name,
                    'outlook_email' => $account->outlook_email,
                    'expiration_date' => $account->expiration_date->format('Y-m-d'),
                    'days_remaining' => now()->diffInDays($account->expiration_date, false)
                ];
            });
            
        return response()->json($accounts);
    }
}
