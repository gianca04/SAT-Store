<div class="space-y-4">
    <div class="flex items-center space-x-4">
        @if($brand->foto_path)
            <img src="{{ asset('storage/' . $brand->foto_path) }}" 
                 class="w-16 h-16 rounded-full object-cover" 
                 alt="{{ $brand->name }}">
        @else
            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                </svg>
            </div>
        @endif
        <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ $brand->name }}</h3>
            <p class="text-sm text-gray-500">{{ $brand->products_count }} productos</p>
        </div>
    </div>

    @if($brand->description)
        <div>
            <h4 class="font-medium text-gray-900 mb-2">Descripción:</h4>
            <div class="text-gray-700 prose prose-sm max-w-none">
                {!! $brand->description !!}
            </div>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="font-medium text-gray-500">Creado:</span>
            <p class="text-gray-900">{{ $brand->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <span class="font-medium text-gray-500">Actualizado:</span>
            <p class="text-gray-900">{{ $brand->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    @if($brand->products_count > 0)
        <div>
            <h4 class="font-medium text-gray-900 mb-2">Productos de esta marca:</h4>
            <div class="space-y-2 max-h-40 overflow-y-auto">
                @foreach($brand->products()->limit(10)->get() as $product)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <span class="text-sm font-medium">{{ $product->name }}</span>
                        <span class="text-xs text-gray-500 px-2 py-1 rounded-full {{ $product->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                @endforeach
                @if($brand->products_count > 10)
                    <p class="text-xs text-gray-500 text-center">
                        Y {{ $brand->products_count - 10 }} productos más...
                    </p>
                @endif
            </div>
        </div>
    @endif
</div>
