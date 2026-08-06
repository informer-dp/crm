<?php

namespace App\Livewire\Salary;

use App\Models\Account;
use App\Models\Order;
use App\Models\SalaryAllowance;
use App\Models\SalaryBonus;
use App\Models\SalaryPeriod;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class SalaryIndex extends Component
{
    use WithPagination;

    public string $activeTab = 'periods';
    public string $selectedMonth;

    // ── Форма розрахунку періоду ──────────────
    public bool $showCalculateForm = false;
    public ?int $calculateUserId = null;
    public string $periodFrom = '';
    public string $periodTo = '';

    // ── Форма бонусу/утримання ────────────────
    public bool $showBonusForm = false;
    public ?int $bonusUserId = null;
    public string $bonusName = '';
    public string $bonusAmount = '';
    public string $bonusType = 'bonus';
    public ?int $bonusPeriodId = null;

    // ── Форма надбавки ────────────────────────
    public bool $showAllowanceForm = false;
    public ?int $allowanceUserId = null;
    public string $allowanceName = '';
    public string $allowanceAmount = '';
    public string $allowanceType = 'fixed';
    public string $allowanceFrom = '';

    // ── Форма виплати ─────────────────────────
    public bool $showPaymentForm = false;
    public ?int $paymentPeriodId = null;
    public string $paymentAmount = '';
    public ?int $paymentAccountId = null;

    public function mount(): void
    {
        $this->selectedMonth = now()->format('Y-m');
        $this->periodFrom    = now()->startOfMonth()->format('Y-m-d');
        $this->periodTo      = now()->endOfMonth()->format('Y-m-d');
        $this->allowanceFrom = now()->format('Y-m-d');
        $this->paymentAccountId = Account::where('type', 'cash')->first()?->id;
    }

    // ── Розрахунок зарплати ───────────────────

    public function openCalculate(?int $userId = null): void
    {
        $this->calculateUserId = $userId;
        $this->periodFrom = Carbon::parse($this->selectedMonth)->startOfMonth()->format('Y-m-d');
        $this->periodTo   = Carbon::parse($this->selectedMonth)->endOfMonth()->format('Y-m-d');
        $this->showCalculateForm = true;
    }

    public function calculateSalary(): void
    {
        $this->validate([
            'calculateUserId' => 'required|exists:users,id',
            'periodFrom'      => 'required|date',
            'periodTo'        => 'required|date|after_or_equal:periodFrom',
        ]);

        $user    = User::with('salarySettings', 'profile')->findOrFail($this->calculateUserId);
        $setting = $user->currentSalarySetting();

        if (!$setting) {
            $this->addError('calculateUserId', 'У цього співробітника немає налаштувань зарплати');
            return;
        }

        // Перевіряємо чи вже є такий період
        $existing = SalaryPeriod::where('user_id', $user->id)
            ->where('period_from', $this->periodFrom)
            ->where('period_to', $this->periodTo)
            ->first();

        if ($existing) {
            $this->addError('periodFrom', 'Розрахунковий період вже існує');
            return;
        }

        // Базова ставка
        $baseEarned = $setting->base_type === 'fixed' ? (float)$setting->base_amount : 0;

        // Бонус
        $bonusEarned = 0;
        if ($setting->bonus_type === 'percent_orders') {
            // % від суми заявок де цей співробітник менеджер
            $ordersTotal = Order::where('manager_id', $user->id)
                ->where('status', 'issued')
                ->whereBetween('issued_at', [$this->periodFrom, $this->periodTo])
                ->join('estimates', 'orders.id', '=', 'estimates.order_id')
                ->sum('estimates.total');
            $bonusEarned = $ordersTotal * ($setting->bonus_percent / 100);

        } elseif ($setting->bonus_type === 'percent_works') {
            // % від суми виконаних робіт де цей інженер виконавець
            $worksTotal = \App\Models\EstimateWork::where('engineer_id', $user->id)
                ->where('work_type', 'own')
                ->where('is_warranty', false)
                ->whereHas('estimate.order', fn($q) =>
                    $q->where('status', 'issued')
                      ->whereBetween('issued_at', [$this->periodFrom, $this->periodTo])
                )
                ->sum('total');
            $bonusEarned = $worksTotal * ($setting->bonus_percent / 100);
        }

        // Надбавки
        $allowances = SalaryAllowance::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        $allowancesTotal = $allowances->sum(function ($a) use ($baseEarned) {
            return $a->type === 'percent'
                ? $baseEarned * ($a->amount / 100)
                : $a->amount;
        });

        // Разові бонуси за цей період (ще не прив'язані)
        $bonuses = SalaryBonus::where('user_id', $user->id)
            ->whereNull('period_id')
            ->where('type', 'bonus')
            ->sum('amount');

        $deductions = SalaryBonus::where('user_id', $user->id)
            ->whereNull('period_id')
            ->where('type', 'deduction')
            ->sum('amount');

        $total = $baseEarned + $bonusEarned + $allowancesTotal + $bonuses - $deductions;

        $period = SalaryPeriod::create([
            'user_id'          => $user->id,
            'period_from'      => $this->periodFrom,
            'period_to'        => $this->periodTo,
            'base_earned'      => $baseEarned,
            'bonus_earned'     => $bonusEarned,
            'allowances_total' => $allowancesTotal,
            'bonuses_total'    => $bonuses,
            'deductions_total' => $deductions,
            'total_accrued'    => max(0, $total),
            'total_paid'       => 0,
            'status'           => 'draft',
        ]);

        // Прив'язуємо неприв'язані бонуси до цього періоду
        SalaryBonus::where('user_id', $user->id)
            ->whereNull('period_id')
            ->update(['period_id' => $period->id]);

        $this->showCalculateForm = false;
        $this->dispatch('period-created');
    }

    public function approvePeriod(int $id): void
    {
        SalaryPeriod::findOrFail($id)->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);
    }

    // ── Виплата зарплати ──────────────────────

    public function openPayment(int $periodId): void
    {
        $period = SalaryPeriod::findOrFail($periodId);
        $this->paymentPeriodId = $periodId;
        $this->paymentAmount   = (string)($period->total_accrued - $period->total_paid);
        $this->showPaymentForm = true;
    }

    public function savePayment(): void
    {
        $this->validate([
            'paymentAmount'    => 'required|numeric|min:0.01',
            'paymentAccountId' => 'required|exists:accounts,id',
        ]);

        $period  = SalaryPeriod::findOrFail($this->paymentPeriodId);
        $account = Account::findOrFail($this->paymentAccountId);
        $amount  = (float)$this->paymentAmount;

        $transaction = Transaction::create([
            'account_id'       => $account->id,
            'type'             => 'expense',
            'amount'           => $amount,
            'balance_after'    => $account->balance - $amount,
            'user_id'          => auth()->id(),
            'description'      => 'Виплата ЗП: ' . $period->user->name,
            'transaction_date' => now()->toDateString(),
        ]);

        $account->decrement('balance', $amount);

        \App\Models\SalaryPayment::create([
            'period_id'      => $period->id,
            'account_id'     => $account->id,
            'transaction_id' => $transaction->id,
            'amount'         => $amount,
            'user_id'        => $period->user_id,
            'paid_by'        => auth()->id(),
        ]);

        $period->increment('total_paid', $amount);

        if ($period->total_paid >= $period->total_accrued) {
            $period->update(['status' => 'paid']);
        }

        $this->showPaymentForm = false;
    }

    // ── Разовий бонус/утримання ───────────────

    public function openBonus(?int $userId = null, ?int $periodId = null): void
    {
        $this->bonusUserId   = $userId;
        $this->bonusPeriodId = $periodId;
        $this->bonusName     = '';
        $this->bonusAmount   = '';
        $this->bonusType     = 'bonus';
        $this->showBonusForm = true;
    }

    public function saveBonus(): void
    {
        $this->validate([
            'bonusUserId' => 'required|exists:users,id',
            'bonusName'   => 'required|min:2',
            'bonusAmount' => 'required|numeric|min:0.01',
            'bonusType'   => 'required|in:bonus,deduction',
        ]);

        SalaryBonus::create([
            'user_id'    => $this->bonusUserId,
            'period_id'  => $this->bonusPeriodId,
            'name'       => $this->bonusName,
            'amount'     => (float)$this->bonusAmount,
            'type'       => $this->bonusType,
            'created_by' => auth()->id(),
        ]);

        $this->showBonusForm = false;
    }

    // ── Надбавки ──────────────────────────────

    public function openAllowance(?int $userId = null): void
    {
        $this->allowanceUserId = $userId;
        $this->allowanceName   = '';
        $this->allowanceAmount = '';
        $this->allowanceType   = 'fixed';
        $this->allowanceFrom   = now()->format('Y-m-d');
        $this->showAllowanceForm = true;
    }

    public function saveAllowance(): void
    {
        $this->validate([
            'allowanceUserId' => 'required|exists:users,id',
            'allowanceName'   => 'required|min:2',
            'allowanceAmount' => 'required|numeric|min:0.01',
            'allowanceFrom'   => 'required|date',
        ]);

        SalaryAllowance::create([
            'user_id'        => $this->allowanceUserId,
            'name'           => $this->allowanceName,
            'amount'         => (float)$this->allowanceAmount,
            'type'           => $this->allowanceType,
            'is_active'      => true,
            'effective_from' => $this->allowanceFrom,
        ]);

        $this->showAllowanceForm = false;
    }

    public function render()
    {
        $engineers = User::role(['engineer', 'manager', 'accountant'])
            ->active()
            ->with(['profile', 'salarySettings'])
            ->orderBy('name')
            ->get();

        $periods = SalaryPeriod::with(['user.profile', 'payments'])
            ->whereYear('period_from', substr($this->selectedMonth, 0, 4))
            ->whereMonth('period_from', substr($this->selectedMonth, 5, 2))
            ->orderByDesc('created_at')
            ->get();

        $allowances = SalaryAllowance::with('user')
            ->where('is_active', true)
            ->orderBy('user_id')
            ->get();

        $unpaidBonuses = SalaryBonus::with('user')
            ->whereNull('period_id')
            ->orderByDesc('created_at')
            ->get();

        $accounts = Account::active()->get();

        return view('livewire.salary.salary-index', compact(
            'engineers', 'periods', 'allowances',
            'unpaidBonuses', 'accounts'
        ))->extends('layouts.app')->section('content');
    }
}