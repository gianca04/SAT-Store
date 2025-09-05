<div style="display: flex; flex-direction: column; gap: 1rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        @if($brand->foto_path)
            <img src="{{ asset('storage/' . $brand->foto_path) }}" 
                 style="width: 4rem; height: 4rem; border-radius: 50%; object-fit: cover;" 
                 alt="{{ $brand->name }}">
        @else
            <div style="width: 4rem; height: 4rem; background-color: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 2rem; height: 2rem; color: #9ca3af;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                </svg>
            </div>
        @endif
        <div>
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0;">{{ $brand->name }}</h3>
            <p style="font-size: 0.875rem; margin: 0;">{{ $brand->products_count }} productos</p>
        </div>
    </div>

    @if($brand->description)
        <div>
            <h4 style="font-weight: 500; margin: 0 0 0.5rem 0;">Descripción:</h4>
            <div style="color: #374151; line-height: 1.5;">
                {!! $brand->description !!}
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.875rem;">
        <div>
            <span style="font-weight: 500; color: #6b7280;">Creado:</span>
            <p style="color: #363a43ff; margin: 0;">{{ $brand->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div>
            <span style="font-weight: 500; color: #6b7280;">Actualizado:</span>
            <p style="color: #363a43ff; margin: 0;">{{ $brand->updated_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    {{-- @if($brand->products_count > 0)
        <div>
            <h4 style="font-weight: 500; color: #111827; margin: 0 0 0.5rem 0;">Productos de esta marca:</h4>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 10rem; overflow-y: auto;">
                @foreach($brand->products()->limit(10)->get() as $product)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem; background-color: #f9fafb; border-radius: 0.375rem;">
                        <span style="font-size: 0.875rem; font-weight: 500;">{{ $product->name }}</span>
                        @if($product->active)
                            <span style="font-size: 0.75rem; color: #065f46; background-color: #d1fae5; padding: 0.25rem 0.5rem; border-radius: 9999px;">
                                Activo
                            </span>
                        @else
                            <span style="font-size: 0.75rem; color: #991b1b; background-color: #fee2e2; padding: 0.25rem 0.5rem; border-radius: 9999px;">
                                Inactivo
                            </span>
                        @endif
                    </div>
                @endforeach
                @if($brand->products_count > 10)
                    <p style="font-size: 0.75rem; color: #6b7280; text-align: center; margin: 0;">
                        Y {{ $brand->products_count - 10 }} productos más...
                    </p>
                @endif
            </div>
        </div>
    @endif --}}

</div>
