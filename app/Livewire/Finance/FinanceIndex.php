<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\OrderPayment;
use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class FinanceIndex extends Component
{
    use WithPagination;

    public string $activeTab = 'overview';
    public string $dateFrom = '';
    public string $dateTo = '';

    // ── Форма оплати по заявці ────────────────
    public bool $showPaymentModal = false;
    public ?int $paymentOrderId = null;
    public string $paymentAmount = '';
    public string $paymentMethod = 'cash';
    public string $paymentType = 'final';
    public ?int $paymentAccountId = null;

    // ── Форма витрати ─────────────────────────
    public bool $showExpenseForm = false;
    public string $expenseDescription = '';
    public string $expenseAmount = '';
    public ?int $expenseCategoryId = null;
    public ?int $expenseAccountId = null;
    public string $expenseDate = '';
    public bool $expenseIsPaid = true;

    // ── Форма переказу між рахунками ──────────
    public bool $showTransferForm = false;
    public ?int $transferFromId = null;
    public ?int $transferToId = null;
    public string $transferAmount = '';
    public string $transferNotes = '';

    // Пошук заявки для витрати
    public string $expenseOrderSearch = '';
    public ?int $expenseOrderId = null;
    public bool $showExpenseOrderDropdown = false;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
        $this->expenseDate = now()->format('Y-m-d');
        $this->paymentAccountId = Account::where('type', 'cash')->first()?->id;
        $this->expenseAccountId = Account::where('type', 'cash')->first()?->id;
    }

    public function updatedExpenseOrderSearch(): void
    {
        $this->showExpenseOrderDropdown = strlen($this->expenseOrderSearch) >= 2;
    }

    public function selectExpenseOrder(int $id, string $number): void
    {
        $this->expenseOrderId = $id;
        $this->expenseOrderSearch = $number;
        $this->showExpenseOrderDropdown = false;
    }

    public function clearExpenseOrder(): void
    {
        $this->expenseOrderId = null;
        $this->expenseOrderSearch = '';
    }
    // ── Оплата по заявці ──────────────────────

    public function openPaymentModal(int $orderId, float $amount = 0): void
    {
        $this->paymentOrderId = $orderId;
        $this->paymentAmount  = $amount > 0 ? (string)$amount : '';
        $this->showPaymentModal = true;
    }

    public function savePayment(): void
    {
        $this->validate([
            'paymentOrderId'  => 'required|exists:orders,id',
            'paymentAmount'   => 'required|numeric|min:0.01',
            'paymentAccountId'=> 'required|exists:accounts,id',
            'paymentMethod'   => 'required|in:cash,terminal,transfer',
            'paymentType'     => 'required|in:prepayment,final,refund',
        ], [
            'paymentAmount.required' => 'Введіть суму',
            'paymentAmount.min'      => 'Сума має бути більше 0',
        ]);

        $account = Account::findOrFail($this->paymentAccountId);
        $amount  = (float)$this->paymentAmount;

        // Створюємо транзакцію
        $transaction = Transaction::create([
            'account_id'       => $account->id,
            'type'             => $this->paymentType === 'refund' ? 'expense' : 'income',
            'amount'           => $amount,
            'balance_after'    => $account->balance + ($this->paymentType === 'refund' ? -$amount : $amount),
            'reference_type'   => 'App\Models\Order',
            'reference_id'     => $this->paymentOrderId,
            'user_id'          => auth()->id(),
            'description'      => 'Оплата по заявці #' . $this->paymentOrderId,
            'transaction_date' => now()->toDateString(),
        ]);

        // Оновлюємо баланс рахунку
        $account->increment('balance',
            $this->paymentType === 'refund' ? -$amount : $amount
        );

        // Створюємо оплату по заявці
        OrderPayment::create([
            'order_id'       => $this->paymentOrderId,
            'account_id'     => $account->id,
            'transaction_id' => $transaction->id,
            'amount'         => $amount,
            'payment_method' => $this->paymentMethod,
            'type'           => $this->paymentType,
            'user_id'        => auth()->id(),
        ]);

        $this->showPaymentModal = false;
        $this->paymentAmount = '';
        $this->dispatch('payment-saved');
    }

    // ── Витрата ───────────────────────────────

    public function saveExpense(): void
    {
        $this->validate([
            'expenseDescription' => 'required|min:2',
            'expenseAmount'      => 'required|numeric|min:0.01',
            'expenseCategoryId'  => 'required|exists:expense_categories,id',
            'expenseDate'        => 'required|date',
        ], [
            'expenseDescription.required' => 'Введіть опис витрати',
            'expenseAmount.required'      => 'Введіть суму',
            'expenseCategoryId.required'  => 'Оберіть категорію',
        ]);

        $expense = Expense::create([
            'category_id'  => $this->expenseCategoryId,
            'account_id'   => $this->expenseIsPaid ? $this->expenseAccountId : null,
            'order_id'     => $this->expenseOrderId,
            'description'  => $this->expenseDescription,
            'amount'       => (float)$this->expenseAmount,
            'is_paid'      => $this->expenseIsPaid,
            'expense_date' => $this->expenseDate,
            'created_by'   => auth()->id(),
        ]);

        // Якщо оплачено — створюємо транзакцію
        if ($this->expenseIsPaid && $this->expenseAccountId) {
            $account = Account::findOrFail($this->expenseAccountId);
            $amount  = (float)$this->expenseAmount;

            $transaction = Transaction::create([
                'account_id'       => $account->id,
                'type'             => 'expense',
                'amount'           => $amount,
                'balance_after'    => $account->balance - $amount,
                'reference_type'   => 'App\Models\Expense',
                'reference_id'     => $expense->id,
                'user_id'          => auth()->id(),
                'description'      => $this->expenseDescription,
                'transaction_date' => $this->expenseDate,
            ]);

            $account->decrement('balance', $amount);
            $expense->update(['transaction_id' => $transaction->id]);
        }

        $this->expenseDescription = '';
        $this->expenseAmount = '';
        $this->expenseCategoryId = null;
        $this->expenseDate = now()->format('Y-m-d');
        $this->showExpenseForm = false;
        $this->expenseOrderId = null;
        $this->expenseOrderSearch = '';
    }

    // ── Переказ між рахунками ─────────────────

    public function saveTransfer(): void
    {
        $this->validate([
            'transferFromId' => 'required|exists:accounts,id|different:transferToId',
            'transferToId'   => 'required|exists:accounts,id',
            'transferAmount' => 'required|numeric|min:0.01',
        ], [
            'transferFromId.required'  => 'Оберіть рахунок відправника',
            'transferToId.required'    => 'Оберіть рахунок отримувача',
            'transferFromId.different' => 'Рахунки мають бути різними',
            'transferAmount.required'  => 'Введіть суму',
        ]);

        $from   = Account::findOrFail($this->transferFromId);
        $to     = Account::findOrFail($this->transferToId);
        $amount = (float)$this->transferAmount;

        // Транзакція списання
        $txFrom = Transaction::create([
            'account_id'       => $from->id,
            'type'             => 'transfer',
            'amount'           => $amount,
            'balance_after'    => $from->balance - $amount,
            'user_id'          => auth()->id(),
            'description'      => "Переказ на рахунок: {$to->name}",
            'transaction_date' => now()->toDateString(),
        ]);

        // Транзакція надходження
        $txTo = Transaction::create([
            'account_id'       => $to->id,
            'type'             => 'transfer',
            'amount'           => $amount,
            'balance_after'    => $to->balance + $amount,
            'user_id'          => auth()->id(),
            'description'      => "Переказ з рахунку: {$from->name}",
            'transaction_date' => now()->toDateString(),
        ]);

        $from->decrement('balance', $amount);
        $to->increment('balance', $amount);

        $this->showTransferForm = false;
        $this->transferAmount = '';
        $this->transferNotes = '';
    }

    public function render()
    {
        $accounts = Account::active()->get();

        $totalBalance = $accounts->sum('balance');

        // Транзакції за період
        $transactions = Transaction::with(['account', 'user'])
            ->whereBetween('transaction_date', [$this->dateFrom, $this->dateTo])
            ->orderByDesc('created_at')
            ->paginate(20);

        // Витрати за період
        $expenses = Expense::with(['category', 'creator'])
            ->whereBetween('expense_date', [$this->dateFrom, $this->dateTo])
            ->orderByDesc('expense_date')
            ->get();

        // Статистика за період
        $periodIncome = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', [$this->dateFrom, $this->dateTo])
            ->sum('amount');

        $periodExpenses = Transaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$this->dateFrom, $this->dateTo])
            ->sum('amount');

        $expenseCategories = ExpenseCategory::whereNull('parent_id')
            ->with('children')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.finance.finance-index', compact(
            'accounts', 'totalBalance', 'transactions',
            'expenses', 'periodIncome', 'periodExpenses',
            'expenseCategories'
        ))->extends('layouts.app')->section('content');
    }
}