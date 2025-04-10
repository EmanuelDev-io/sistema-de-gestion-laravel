<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'account_id',
        'invoice_number',
        'amount',
        'invoice_date',
        'description',
        'file_path',
    ];
    
    protected $casts = [
        'invoice_date' => 'date',
        'amount' => 'decimal:2',
    ];
    
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
