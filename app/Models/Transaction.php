<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'type',
        'amount',
        'balance_after',
        'reference_type',
        'reference_id',
        'user_id',
        'description',
        'transaction_date',
        'basis_id',
        'order_id',
        'supplier_id',
        'client_id',
        'counterparty_name',
        'payment_method',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'balance_after'=> 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function basis(): BelongsTo
    {
        return $this->belongsTo(TransactionBasis::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Контрагент — повертає назву незалежно від типу
    public function getCounterpartyLabelAttribute(): string
    {
        if ($this->client) return $this->client->name;
        if ($this->supplier) return $this->supplier->name;
        if ($this->counterparty_name) return $this->counterparty_name;
        return '—';
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'income'   => 'Надходження',
            'expense'  => 'Витрата',
            'transfer' => 'Переказ',
            default    => $this->type,
        };
    }
}