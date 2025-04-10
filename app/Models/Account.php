<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'page_name',
        'outlook_email',
        'outlook_password',
        'hostinger_password',
        'purchase_date',
        'expiration_date',
    ];
    
    protected $casts = [
        'purchase_date' => 'date',
        'expiration_date' => 'date',
    ];
    
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
