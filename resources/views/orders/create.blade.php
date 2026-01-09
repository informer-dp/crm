@extends('layouts.app')
@if(session('new_counterparty_id'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let select = document.querySelector('select[name="counterparty_id"]');
        if (select) {
            select.value = "{{ session('new_counterparty_id') }}";
            select.dispatchEvent(new Event('change'));
        }
    });
</script>
@endif

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Нове замовлення</h4>

        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            ← Назад
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('orders.store') }}">
                @csrf

                {{-- ================= Клієнт ================= --}}
                <div class="mb-3">
                    <label class="form-label">Клієнт</label>
                
                <div class="d-flex justify-content-between ">            
                        <button type="button" class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal" data-bs-target="#createClientModal">
                            + Новий клієнт
                        </button>
                    <select name="counterparty_id" class="form-select @error('counterparty_id') is-invalid @enderror" required>
                        <option value="">— Оберіть клієнта —</option>

                        @foreach($counterparties as $c)
                            <option value="{{ $c->id }}"
                                {{ old('counterparty_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->contact->name ?? 'Без імені' }}
                            </option>
                        @endforeach
                    </select>
                    

                    @error('counterparty_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                </div>
<div id="clientInfo" class="alert alert-light border d-none">
    <div><strong>Телефон:</strong> <span id="clientPhone">—</span></div>
    <div><strong>Email:</strong> <span id="clientEmail">—</span></div>
</div>


                {{-- ================= Пристрій ================= --}}
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Тип пристрою</label>

                        <select name="device_id" class="form-select @error('device_id') is-invalid @enderror" required>
                            <option value="">— Оберіть тип —</option>

                            @foreach($devices as $d)
                                <option value="{{ $d->id }}"
                                    {{ old('device_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('device_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Бренд</label>
                        <select name="brand_id" class="form-control" required>
                            <option value="">— Оберіть бренд —</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>




                        @error('device_brand')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Модель</label>
                        <input name="device_model"
                               value="{{ old('device_model') }}"
                               class="form-control @error('device_model') is-invalid @enderror"
                               placeholder="Наприклад: iPhone 14 Pro"
                               required>

                        @error('device_model')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Серійний номер --}}
                <div class="mb-3">
                    <label class="form-label">Серійний номер (необовʼязково)</label>
                    <input name="serial_number"
                           value="{{ old('serial_number') }}"
                           class="form-control @error('serial_number') is-invalid @enderror">

                    @error('serial_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                {{-- ================= Опис несправності ================= --}}
                <div class="mb-4">
                    <label class="form-label">Опис проблеми</label>

                    <textarea name="problem_description"
                              class="form-control @error('problem_description') is-invalid @enderror"
                              rows="4"
                              placeholder="Опишіть, що не працює..."
                              required>{{ old('problem_description') }}</textarea>

                    @error('problem_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                {{-- ================= Submit ================= --}}
                <div class="text-end">
                    <button class="btn btn-primary">
                        Створити замовлення
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
{{-- ================= Modal Додати клієнта ================= --}}
<div class="modal fade" id="createClientModal">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="/ajax/create-counterparty">
    @csrf

    <div class="modal-header">
        <h5 class="modal-title">Новий клієнт</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">

        <div class="mb-3">
            <label>Імʼя</label>
            <input name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Телефон</label>
            <input name="phone" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input name="email" class="form-control">
        </div>

    </div>

    <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
        <button class="btn btn-primary">Зберегти</button>
    </div>

</form>

    </div>
</div>

@endsection
<script>
document.querySelector('select[name="counterparty_id"]')
.addEventListener('change', function () {

    if (!this.value) return;

    fetch('{{ route('ajax.counterparty', '') }}/' + this.value)
        .then(r => r.json())
        .then(data => {
            document.getElementById('clientInfo').classList.remove('d-none');
            document.getElementById('clientPhone').innerText = data.phone ?? '—';
            document.getElementById('clientEmail').innerText = data.email ?? '—';
        });
});
</script>
<script>
const modelsByBrand = {
    "Apple": ["iPhone 11", "iPhone 12", "iPhone 13", "Macbook Air", "Macbook Pro"],
    "Samsung": ["S21", "S22", "S23", "A52", "A73"],
    "Xiaomi": ["Mi 11", "Mi 12", "Redmi Note 10", "Redmi Note 11"],
    "Lenovo": ["ThinkPad T14", "ThinkPad X1", "Yoga 7"],
};

const brandSelect = document.querySelector('select[name="device_brand"]');
const modelInput = document.querySelector('input[name="device_model"]');

brandSelect.addEventListener('change', function () {
    let brand = this.value;

    if (!modelsByBrand[brand]) return;

    let datalistId = "modelsList";

    if (!document.getElementById(datalistId)) {
        let dl = document.createElement('datalist');
        dl.id = datalistId;
        document.body.appendChild(dl);
        modelInput.setAttribute("list", datalistId);
    }

    let dl = document.getElementById(datalistId);
    dl.innerHTML = "";

    modelsByBrand[brand].forEach(m => {
        let opt = document.createElement("option");
        opt.value = m;
        dl.appendChild(opt);
    });
});
</script>
