<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     */
    public function index()
    {
        $invoices = Invoice::with('account')->orderBy('invoice_date', 'desc')->get();
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $accounts = Account::orderBy('page_name')->get();
        return view('invoices.create', compact('accounts'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'invoice_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
            'description' => 'nullable|string',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $invoice = new Invoice();
        $invoice->account_id = $validated['account_id'];
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->amount = $validated['amount'];
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->description = $validated['description'];

        if ($request->hasFile('invoice_file')) {
            $file = $request->file('invoice_file');
            $path = $file->store('invoices', 'public');
            $invoice->file_path = $path;
        }

        $invoice->save();

        if ($request->has('from_account')) {
            return redirect()->route('accounts.show', $invoice->account_id)
                ->with('success', 'Factura creada con éxito.');
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Factura creada con éxito.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $accounts = Account::orderBy('page_name')->get();
        return view('invoices.edit', compact('invoice', 'accounts'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'invoice_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
            'description' => 'nullable|string',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $invoice->account_id = $validated['account_id'];
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->amount = $validated['amount'];
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->description = $validated['description'];

        if ($request->hasFile('invoice_file')) {
            // Delete old file if exists
            if ($invoice->file_path) {
                Storage::disk('public')->delete($invoice->file_path);
            }
            
            $file = $request->file('invoice_file');
            $path = $file->store('invoices', 'public');
            $invoice->file_path = $path;
        }

        $invoice->save();

        if ($request->has('from_account')) {
            return redirect()->route('accounts.show', $invoice->account_id)
                ->with('success', 'Factura actualizada con éxito.');
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Factura actualizada con éxito.');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $accountId = $invoice->account_id;
        
        // Delete file if exists
        if ($invoice->file_path) {
            Storage::disk('public')->delete($invoice->file_path);
        }
        
        $invoice->delete();

        if (request()->has('from_account')) {
            return redirect()->route('accounts.show', $accountId)
                ->with('success', 'Factura eliminada con éxito.');
        }

        return redirect()->route('invoices.index')
            ->with('success', 'Factura eliminada con éxito.');
    }

    /**
     * Download the invoice file.
     */
    public function download(Invoice $invoice)
    {
        if (!$invoice->file_path) {
            return back()->with('error', 'No hay archivo disponible para esta factura.');
        }

        return Storage::disk('public')->download($invoice->file_path);
    }

    /**
     * API Methods for React Frontend
     */
    
    public function apiIndex()
    {
        $invoices = Invoice::with('account')
            ->orderBy('invoice_date', 'desc')
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
        
        return response()->json($invoices);
    }
    
    public function apiShow(Invoice $invoice)
    {
        $invoice->load('account');
        
        $invoiceData = [
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
            'has_file' => !empty($invoice->file_path),
            'file_path' => $invoice->file_path
        ];
        
        return response()->json($invoiceData);
    }
    
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'invoice_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
            'description' => 'nullable|string',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);
        
        $invoice = new Invoice();
        $invoice->account_id = $validated['account_id'];
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->amount = $validated['amount'];
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->description = $validated['description'] ?? null;
        
        if ($request->hasFile('invoice_file')) {
            $file = $request->file('invoice_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('invoices', $filename, 'public');
            $invoice->file_path = 'invoices/' . $filename;
        }
        
        $invoice->save();
        
        return response()->json([
            'id' => $invoice->id,
            'account_id' => $invoice->account_id,
            'invoice_number' => $invoice->invoice_number,
            'amount' => (float) $invoice->amount,
            'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
            'description' => $invoice->description,
            'has_file' => !empty($invoice->file_path)
        ], 201);
    }
    
    public function apiUpdate(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'invoice_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
            'description' => 'nullable|string',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);
        
        $invoice->account_id = $validated['account_id'];
        $invoice->invoice_number = $validated['invoice_number'];
        $invoice->amount = $validated['amount'];
        $invoice->invoice_date = $validated['invoice_date'];
        $invoice->description = $validated['description'] ?? null;
        
        if ($request->hasFile('invoice_file')) {
            // Eliminar archivo anterior si existe
            if (!empty($invoice->file_path) && Storage::disk('public')->exists($invoice->file_path)) {
                Storage::disk('public')->delete($invoice->file_path);
            }
            
            $file = $request->file('invoice_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('invoices', $filename, 'public');
            $invoice->file_path = 'invoices/' . $filename;
        }
        
        $invoice->save();
        
        return response()->json([
            'id' => $invoice->id,
            'account_id' => $invoice->account_id,
            'invoice_number' => $invoice->invoice_number,
            'amount' => (float) $invoice->amount,
            'invoice_date' => $invoice->invoice_date->format('Y-m-d'),
            'description' => $invoice->description,
            'has_file' => !empty($invoice->file_path)
        ]);
    }
    
    public function apiDestroy(Invoice $invoice)
    {
        // Eliminar archivo si existe
        if (!empty($invoice->file_path) && Storage::disk('public')->exists($invoice->file_path)) {
            Storage::disk('public')->delete($invoice->file_path);
        }
        
        $invoice->delete();
        
        return response()->json(null, 204);
    }
    
    public function apiDownload(Invoice $invoice)
    {
        if (empty($invoice->file_path) || !Storage::disk('public')->exists($invoice->file_path)) {
            return response()->json(['error' => 'Archivo no encontrado'], 404);
        }
        
        return Storage::disk('public')->download($invoice->file_path);
    }
}
