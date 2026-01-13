<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Кошторис замовлення</h5>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEstimationModal">
            + Додати позицію
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-striped align-right mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Назва роботи / деталі</th>
                    <th class="text-end">Ціна деталі</th>
                    <th class="text-end">Ціна роботи</th>
                    <th class="text-end">Ціна операції</th>
                    <th class="text-end"style="width:130px;">Дії</th>
                </tr>
            </thead>

            <tbody>

            @php $total = 0; @endphp

            @forelse($order->estimations as $i => $item)
                @php $total += $item->total_cost; @endphp

                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="text-end"><strong>{{ number_format($item->part_cost, 0, '.', ' ') }} грн</strong></td>
                    <td class="text-end"><strong>{{ number_format($item->labor_cost, 0, '.', ' ') }} грн</strong></td>
                    <td class="text-end"><strong>{{ number_format($item->total_cost, 0, '.', ' ') }} грн</strong></td>

                    <td class="text-end">
                        {{-- Редагування --}}
                        <button class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#editEstimationModal{{ $item->id }}">
                               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                </svg>
                            <!-- Редагувати -->
                        </button>

                        {{-- Видалення --}}
                        <form method="POST"
                              action="{{ route('orders.estimations.destroy', $item->id) }}"
                              class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button onclick="return confirm('Видалити позицію?')"
                                    class="btn btn-sm btn-outline-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                    <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                    </svg>
                                <!-- Видалити -->
                            </button>
                        </form>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        Кошторис порожній
                    </td>
                </tr>
            @endforelse

            </tbody>

            @if($order->estimations->count())
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Разом:</th>
                    <th class="text-end" colspan="1" >
                        <strong>{{ number_format($total, 0, '.', ' ') }} грн</strong>
                    </th>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ================= Modal Додати ================= --}}
<div class="modal fade" id="addEstimationModal">
    <div class="modal-dialog">
        <form class="modal-content"
              method="POST"
              action="{{ route('orders.estimations.store', $order->id) }}">

            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Додати позицію до кошторису</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
    <label>Опис</label>
    <input name="description" class="form-control" required>
</div>

<div class="mb-3">
    <label>Запчастина</label>
    <input type="number" name="part_cost" class="form-control" min="0">
</div>

<div class="mb-3">
    <label>Робота</label>
    <input type="number" name="labor_cost" class="form-control" min="0">
</div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Відміна</button>
                <button class="btn btn-primary">Додати</button>
            </div>

        </form>
    </div>
</div>
 {{-- ================= Модали редагування ================= --}}
@foreach($order->estimations as $item)
<div class="modal fade"
     id="editEstimationModal{{ $item->id }}"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content"
              method="POST"
              action="{{ route('orders.estimations.update', $item->id) }}">

            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">
                    Редагування позиції кошторису
                </h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Опис</label>
                    <textarea name="description"
                              class="form-control"
                              rows="2"
                              required>{{ $item->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Запчастини</label>
                    <input type="number"
                           step="0.01"
                           min="0"
                           name="part_cost"
                           class="form-control"
                           value="{{ $item->part_cost }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Робота</label>
                    <input type="number"
                           step="0.01"
                           min="0"
                           name="labor_cost"
                           class="form-control"
                           value="{{ $item->labor_cost }}">
                </div>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Скасувати
                </button>
                <button type="submit" class="btn btn-primary">
                    Зберегти
                </button>
            </div>

        </form>
    </div>
</div>
@endforeach
