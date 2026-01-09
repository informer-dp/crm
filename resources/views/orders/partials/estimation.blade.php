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
                    <th>Ціна деталі</th>
                    <th>Ціна роботи</th>
                    <th>Ціна</th>
                    <th style="width:130px;"></th>
                </tr>
            </thead>

            <tbody>

            @php $total = 0; @endphp

            @forelse($order->estimations as $i => $item)
                @php $total += $item->total_cost; @endphp

                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td ><strong>{{ number_format($item->part_cost, 0, '.', ' ') }} грн</strong></td>
                    <td><strong>{{ number_format($item->labor_cost, 0, '.', ' ') }} грн</strong></td>
                    <td><strong>{{ number_format($item->total_cost, 0, '.', ' ') }} грн</strong></td>

                    <td class="text-end">
                        {{-- Редагування --}}
                        <button class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#editEstimationModal{{ $item->id }}">
                            Редагувати
                        </button>

                        {{-- Видалення --}}
                        <form method="POST"
                              action="{{ route('orders.estimations.destroy', $item->id) }}"
                              class="d-inline">

                            @csrf
                            @method('DELETE')

                            <button onclick="return confirm('Видалити позицію?')"
                                    class="btn btn-sm btn-outline-danger">
                                Видалити
                            </button>
                        </form>
                    </td>
                </tr>


                {{-- ================= Modal Редагування позиції кошторису ================= --}}
<div class="modal fade" id="editEstimationModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content"
              method="POST"
              action="{{ route('orders.estimations.update', $item->id) }}">

            @csrf
            @method('PATCH')

            <div class="modal-header">
                <h5 class="modal-title">Редагування позиції кошторису</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                {{-- Опис --}}
                <div class="mb-3">
                    <label class="form-label">Опис роботи / деталі</label>
                    <textarea name="description"
                              class="form-control"
                              rows="2"
                              required>{{ $item->description }}</textarea>
                </div>

                {{-- Вартість запчастин --}}
                <div class="mb-3">
                    <label class="form-label">Вартість запчастин (грн)</label>
                    <input type="number"
                           step="0.01"
                           min="0"
                           name="part_cost"
                           class="form-control"
                           value="{{ $item->part_cost }}">
                </div>

                {{-- Вартість роботи --}}
                <div class="mb-3">
                    <label class="form-label">Вартість роботи (грн)</label>
                    <input type="number"
                           step="0.01"
                           min="0"
                           name="labor_cost"
                           class="form-control"
                           value="{{ $item->labor_cost }}">
                </div>

                <small class="text-muted">
                    Загальна сума буде перерахована автоматично
                </small>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Скасувати
                </button>

                <button type="submit" class="btn btn-primary">
                    Зберегти зміни
                </button>
            </div>

        </form>
    </div>
</div>


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
                    <th colspan="2" text-align="right">
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
