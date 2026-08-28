<?php

namespace App\Livewire\Planning;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class PlanningIndex extends Component
{
    public string $viewMode = 'month'; // day / week / month / quarter
    public string $currentDate;
    public ?int $selectedEngineerId = null;
    public int $maxPerDay = 4; // норма замовлень на інженера в день

    public function mount(): void
    {
        $this->currentDate = now()->startOfMonth()->format('Y-m-d');
    }

    public function prev(): void
    {
        $this->currentDate = Carbon::parse($this->currentDate)
            ->sub($this->getPeriodUnit())
            ->format('Y-m-d');
    }

    public function next(): void
    {
        $this->currentDate = Carbon::parse($this->currentDate)
            ->add($this->getPeriodUnit())
            ->format('Y-m-d');
    }

    public function today(): void
    {
        $this->currentDate = now()->startOf(
            str_replace('quarter', 'month', $this->viewMode)
        )->format('Y-m-d');
    }

    private function getPeriodUnit(): string
    {
        return match($this->viewMode) {
            'day'     => '1 day',
            'week'    => '1 week',
            'month'   => '1 month',
            'quarter' => '3 months',
            default   => '1 month',
        };
    }

    private function getDateRange(): array
    {
        $start = Carbon::parse($this->currentDate);

        return match($this->viewMode) {
            'day'     => [$start->copy(), $start->copy()],
            'week'    => [$start->copy()->startOfWeek(), $start->copy()->endOfWeek()],
            'month'   => [$start->copy()->startOfMonth(), $start->copy()->endOfMonth()],
            'quarter' => [$start->copy()->startOfMonth(), $start->copy()->addMonths(2)->endOfMonth()],
            default   => [$start->copy()->startOfMonth(), $start->copy()->endOfMonth()],
        };
    }

    public function render()
    {
        [$startDate, $endDate] = $this->getDateRange();

        // Отримуємо інженерів
        $engineers = User::role('engineer')
            ->active()
            ->with('profile')
            ->orderBy('name')
            ->get();

        // Отримуємо активні замовлення за період
        // Замовлення "в роботі" якщо його estimated_date в межах або воно ще активне
        $orders = Order::active()
            ->with(['client', 'device.brand', 'device.model', 'engineers'])
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('estimated_date', [$startDate, $endDate])
                  ->orWhere(function($q2) use ($startDate, $endDate) {
                      $q2->whereNull('estimated_date')
                         ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
                  });
            })
            ->get();

        // Будуємо дні
        $days = [];
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $days[$dateStr] = [
                'date'      => $date->copy(),
                'engineers' => [],
                'total'     => 0,
            ];
        }

        // Розподіляємо замовлення по інженерах і днях
        foreach ($engineers as $engineer) {
            // Замовлення цього інженера
            $engineerOrders = $orders->filter(function($order) use ($engineer) {
                return $order->engineers->contains('id', $engineer->id);
            });

            // Замовлення без інженера (тільки для загального адміна)
            $unassigned = $orders->filter(function($order) {
                return $order->engineers->isEmpty();
            });

            foreach ($days as $dateStr => &$day) {
                $date = $day['date'];

                // Замовлення інженера що активні в цей день
                $dayOrders = $engineerOrders->filter(function($order) use ($date) {
                    return $this->isOrderActiveOnDate($order, $date);
                });

                $count = $dayOrders->count();
                $load = $count > 0 ? min(100, round($count / $this->maxPerDay * 100)) : 0;

                $day['engineers'][$engineer->id] = [
                    'engineer' => $engineer,
                    'orders'   => $dayOrders,
                    'count'    => $count,
                    'load'     => $load,
                    'color'    => $this->loadColor($load),
                ];

                $day['total'] += $count;
            }
        }

        // Для таймлайну (Ганта) — всі активні замовлення в діапазоні
        $ganttOrders = Order::active()
            ->with(['client', 'device.brand', 'device.model', 'engineers'])
            ->whereNotNull('estimated_date')
            ->where('estimated_date', '>=', $startDate)
            ->orderBy('estimated_date')
            ->get();

        return view('livewire.planning.planning-index', compact(
            'engineers', 'days', 'ganttOrders',
            'startDate', 'endDate'
        ))->extends('layouts.app')->section('content');
    }

    private function isOrderActiveOnDate(Order $order, Carbon $date): bool
    {
        $created = $order->created_at->startOfDay();
        $deadline = $order->estimated_date
            ? Carbon::parse($order->estimated_date)->startOfDay()
            : $created->copy()->addDays(3);

        return $date->between($created, $deadline);
    }

    private function loadColor(int $load): string
    {
        if ($load === 0)   return '#f3f4f6'; // пусто
        if ($load <= 50)   return '#86efac'; // зелений — є місце
        if ($load <= 75)   return '#fde68a'; // жовтий — майже повний
        if ($load <= 100)  return '#fca5a5'; // червоний — повний
        return '#ef4444';                    // темно-червоний — перевантажений
    }
}