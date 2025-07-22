
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@foreach($orders as $order)
    <div class="p-4 border mb-2">
        <p>Pedido #{{ $order->id }} - Cliente: {{ $order->user->name ?? 'Desconocido' }}</p>
        <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
            @csrf
            @method('PUT')
            <select name="status" onchange="this.form.submit()">
                @foreach(['pendiente', 'en produccion', 'enviado', 'entregado'] as $estado)
                    <option value="{{ $estado }}" {{ $order->status == $estado ? 'selected' : '' }}>
                        {{ ucfirst($estado) }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
@endforeach