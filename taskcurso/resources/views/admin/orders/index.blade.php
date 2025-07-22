

<div class="max-w-5xl mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Listado de Pedidos</h2>

    <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium mb-1">Estado</label>
            <select name="status" class="border rounded px-2 py-1">
                <option value="">Todos</option>
                @foreach(['pendiente', 'en produccion', 'enviado', 'entregado'] as $estado)
                    <option value="{{ $estado }}" {{ request('status') == $estado ? 'selected' : '' }}>{{ ucfirst($estado) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Cliente</label>
            <input type="text" name="client" value="{{ request('client') }}" class="border rounded px-2 py-1" placeholder="Nombre cliente">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Desde</label>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="border rounded px-2 py-1">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Hasta</label>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="border rounded px-2 py-1">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filtrar</button>
    </form>

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

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border rounded shadow">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border">ID</th>
                    <th class="px-4 py-2 border">Cliente</th>
                    <th class="px-4 py-2 border">Estado</th>
                    <th class="px-4 py-2 border">Fecha</th>
                    <th class="px-4 py-2 border">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-2 border">{{ $order->id }}</td>
                        <td class="px-4 py-2 border">{{ $order->user->name ?? 'Desconocido' }}</td>
                        <td class="px-4 py-2 border">{{ ucfirst($order->status) }}</td>
                        <td class="px-4 py-2 border">{{ $order->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 border">
                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                                @csrf
                                @method('PUT')
                                <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1">
                                    @foreach(['pendiente', 'en produccion', 'enviado', 'entregado'] as $estado)
                                        <option value="{{ $estado }}" {{ $order->status == $estado ? 'selected' : '' }}>{{ ucfirst($estado) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-2 border text-center text-gray-500">No hay pedidos para mostrar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>