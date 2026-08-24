<?php

namespace App\Livewire\Finance;

use App\Models\Account;
use App\Models\Client;
use App\Models\Order;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionBasis;
use Livewire\Component;
use Livewire\WithPagination;

class FinanceIndex extends Component
{
    use WithPagination;

    public string $activeTab = 'income';
    public string $dateFrom = '';
    public string $dateTo = '';

    // ── Форма нової транзакції ────────────────
    public bool $showTransactionForm = false;
    public string $formType = 'income'; // income / expense / transfer

    // Основні поля
    public ?int $formAccountId = null;
    public ?int $formToAccountId = null; // для transfer
    public string $formAmount = '';
    public string $formDate = '';
    public string $formDescription = '';
    public ?int $formBasisId = null;
    public string $formPaymentMethod = 'cash';

    // Контрагент
    public string $counterpartyType = 'none'; // none / client / supplier / free
    public string $clientSearch = '';
    public ?int $formClientId = null;
    public bool $showClientDropdown = false;
    public ?int $formSupplierId = null;
    public string $formCounterpartyName = '';

    // Прив'язка до заявки
    public string $orderSearch = '';
    public ?int $formOrderId = null;
    public bool $showOrderDropdown = false;

    // ── Картка транзакції ─────────────────────
    public ?int $viewingTransactionId = null;
    public bool $showTransactionModal = false;
    public bool $editingTransaction = false;
    public string $editDescription = '';
    public string $editDate = '';
    public ?int $editBasisId = null;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
        $this->formDate = now()->format('Y-m-d');
        $this->formAccountId = Account::where('type', 'cash')->first()?->id;
    }

    // ── Оновлення пошуку ──────────────────────

    public function updatedClientSearch(): void
    {
        $this->showClientDropdown = strlen($this->clientSearch) >= 2;
    }

    public function updatedOrderSearch(): void
    {
        $this->showOrderDropdown = strlen($this->orderSearch) >= 2;
    }

    public function updatedCounterpartyType(): void
    {
        $this->formClientId = null;
        $this->formSupplierId = null;
        $this->formCounterpartyName = '';
        $this->clientSearch = '';
        $this->showClientDropdown = false;
    }

    public function updatedFormType(): void
    {
        $this->formBasisId = null;
        $this->counterpartyType = 'none';
        $this->formClientId = null;
        $this->formSupplierId = null;
        $this->formCounterpartyName = '';
        $this->clientSearch = '';
        $this->formOrderId = null;
        $this->orderSearch = '';
    }

    // ── Вибір клієнта ─────────────────────────

    public function selectClient(int $id): void
    {
        $client = Client::find($id);
        if (!$client) return;
        $this->formClientId  = $client->id;
        $this->clientSearch  = $client->name . ' · ' . $client->phone;
        $this->showClientDropdown = false;
    }

    public function clearClient(): void
    {
        $this->formClientId = null;
        $this->clientSearch = '';
    }

    // ── Вибір заявки ──────────────────────────

    public function selectOrder(int $id, string $number): void
    {
        $this->formOrderId  = $id;
        $this->orderSearch  = $number;
        $this->showOrderDropdown = false;

        // Автозаповнення клієнта якщо не вказаний
        if (!$this->formClientId && $this->formType === 'income') {
            $order = Order::with('client')->find($id);
            if ($order) {
                $this->formClientId = $order->client_id;
                $this->clientSearch = $order->client->name . ' · ' . $order->client->phone;
                $this->counterpartyType = 'client';
            }
        }
    }

    public function clearOrder(): void
    {
        $this->formOrderId = null;
        $this->orderSearch = '';
    }

    // ── Відкрити форму ────────────────────────

    public function openForm(string $type = 'income'): void
    {
        $this->reset([
            'formAmount', 'formDescription', 'formBasisId',
            'formClientId', 'formSupplierId', 'formCounterpartyName',
            'formOrderId', 'clientSearch', 'orderSearch',
            'formToAccountId',
        ]);
        $this->formType          = $type;
        $this->formDate          = now()->format('Y-m-d');
        $this->formPaymentMethod = 'cash';
        $this->counterpartyType  = $type === 'income' ? 'client' : 'supplier';
        $this->showTransactionForm = true;
    }

    // ── Збереження транзакції ─────────────────

    public function saveTransaction(): void
    {
        $rules = [
            'formAccountId'  => 'required|exists:accounts,id',
            'formAmount'     => 'required|numeric|min:0.01',
            'formDate'       => 'required|date',
            'formBasisId'    => 'required|exists:transaction_bases,id',
        ];

        if ($this->formType === 'transfer') {
            $rules['formToAccountId'] = 'required|exists:accounts,id|different:formAccountId';
        }

        $this->validate($rules, [
            'formAccountId.required'  => 'Оберіть рахунок',
            'formAmount.required'     => 'Введіть суму',
            'formDate.required'       => 'Введіть дату',
            'formBasisId.required'    => 'Оберіть статтю руху коштів',
            'formToAccountId.required'=> 'Оберіть рахунок призначення',
            'formToAccountId.different'=> 'Рахунки мають бути різними',
        ]);

        $account = Account::findOrFail($this->formAccountId);
        $amount  = (float)$this->formAmount;

        $clientId   = $this->counterpartyType === 'client' ? $this->formClientId : null;
        $supplierId = $this->counterpartyType === 'supplier' ? $this->formSupplierId : null;
        $cpName     = $this->counterpartyType === 'free' ? $this->formCounterpartyName : null;

        if ($this->formType === 'transfer') {
            $toAccount = Account::findOrFail($this->formToAccountId);

            Transaction::create([
                'account_id'       => $account->id,
                'type'             => 'transfer',
                'amount'           => $amount,
                'balance_after'    => $account->balance - $amount,
                'user_id'          => auth()->id(),
                'basis_id'         => $this->formBasisId,
                'description'      => $this->formDescription ?: "Переказ на: {$toAccount->name}",
                'transaction_date' => $this->formDate,
                'payment_method'   => $this->formPaymentMethod,
            ]);

            Transaction::create([
                'account_id'       => $toAccount->id,
                'type'             => 'transfer',
                'amount'           => $amount,
                'balance_after'    => $toAccount->balance + $amount,
                'user_id'          => auth()->id(),
                'basis_id'         => $this->formBasisId,
                'description'      => $this->formDescription ?: "Переказ з: {$account->name}",
                'transaction_date' => $this->formDate,
                'payment_method'   => $this->formPaymentMethod,
            ]);

            $account->decrement('balance', $amount);
            $toAccount->increment('balance', $amount);

        } else {
            $isIncome = $this->formType === 'income';

            Transaction::create([
                'account_id'        => $account->id,
                'type'              => $this->formType,
                'amount'            => $amount,
                'balance_after'     => $account->balance + ($isIncome ? $amount : -$amount),
                'user_id'           => auth()->id(),
                'basis_id'          => $this->formBasisId,
                'order_id'          => $this->formOrderId ?: null,
                'client_id'         => $clientId,
                'supplier_id'       => $supplierId,
                'counterparty_name' => $cpName,
                'description'       => $this->formDescription ?: null,
                'transaction_date'  => $this->formDate,
                'payment_method'    => $this->formPaymentMethod,
            ]);

            if ($isIncome) {
                $account->increment('balance', $amount);
            } else {
                $account->decrement('balance', $amount);
            }
        }

        $this->showTransactionForm = false;
        $this->resetPage();
    }

    // ── Картка транзакції ─────────────────────

    public function openTransaction(int $id): void
    {
        $this->viewingTransactionId = $id;
        $this->editingTransaction   = false;
        $this->showTransactionModal = true;
    }

    public function startEdit(): void
    {
        $tx = Transaction::findOrFail($this->viewingTransactionId);
        $this->editDescription = $tx->description ?? '';
        $this->editDate        = $tx->transaction_date->format('Y-m-d');
        $this->editBasisId     = $tx->basis_id;
        $this->editingTransaction = true;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editDate'        => 'required|date',
        ]);

        Transaction::findOrFail($this->viewingTransactionId)->update([
            'description'      => $this->editDescription,
            'transaction_date' => $this->editDate,
            'basis_id'         => $this->editBasisId,
        ]);

        $this->editingTransaction = false;
    }

    // ── Рендер ────────────────────────────────

    public function render()
    {
        $accounts     = Account::active()->get();
        $totalBalance = $accounts->sum('balance');

        $baseQuery = Transaction::with(['account', 'user', 'basis', 'order.client', 'client', 'supplier'])
            ->whereBetween('transaction_date', [$this->dateFrom, $this->dateTo]);

        $income = (clone $baseQuery)->where('type', 'income')
            ->orderByDesc('transaction_date')->orderByDesc('id')
            ->paginate(20, ['*'], 'income_page');

        $expense = (clone $baseQuery)->where('type', 'expense')
            ->orderByDesc('transaction_date')->orderByDesc('id')
            ->paginate(20, ['*'], 'expense_page');

        $transfer = (clone $baseQuery)->where('type', 'transfer')
            ->orderByDesc('transaction_date')->orderByDesc('id')
            ->paginate(20, ['*'], 'transfer_page');

        $periodIncome   = (clone $baseQuery)->where('type', 'income')->sum('amount');
        $periodExpense  = (clone $baseQuery)->where('type', 'expense')->sum('amount');

        $incomeBases   = TransactionBasis::active()->forIncome()->orderBy('sort_order')->get()->groupBy('group');
        $expenseBases  = TransactionBasis::active()->forExpense()->orderBy('sort_order')->get()->groupBy('group');
        $transferBases = TransactionBasis::active()->forInternal()->orderBy('sort_order')->get();

        $suppliers = Supplier::active()->orderBy('name')->get();

        $viewingTransaction = $this->viewingTransactionId
            ? Transaction::with(['account', 'user', 'basis', 'order.client', 'client', 'supplier'])->find($this->viewingTransactionId)
            : null;

        return view('livewire.finance.finance-index', compact(
            'accounts', 'totalBalance',
            'income', 'expense', 'transfer',
            'periodIncome', 'periodExpense',
            'incomeBases', 'expenseBases', 'transferBases',
            'suppliers', 'viewingTransaction'
        ))->extends('layouts.app')->section('content');
    }
}