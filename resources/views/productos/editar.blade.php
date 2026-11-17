@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
    <div class="container mx-auto mt-10">
        <div class="max-w-xl mx-auto bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-3xl font-bold text-center mb-6">Editar Producto</h2>
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-5">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('productos.update', $productos->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label for="nombre" class="block mb-2 font-semibold">Nombre</label>
                    <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $productos->nombre) }}"
                        maxlength="150" required class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="descripcion" class="block mb-2 font-semibold">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="w-full border rounded px-3 py-2">{{ old('descripcion', $productos->descripcion) }}</textarea>
                </div>
                <div class="mb-5">
                    <label for="precio" class="block mb-2 font-semibold">Precio</label>
                    <input id="precio" type="number" name="precio" value="{{ old('precio', $productos->precio) }}"
                        min="0" step="0.01" required class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="stock" class="block mb-2 font-semibold">Stock</label>
                    <input id="stock" type="number" name="stock" value="{{ old('stock', $productos->stock) }}"
                        min="0" required class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-5">
                    <label for="imagen" class="block mb-2 font-semibold">Imagen del producto</label>
                    <input id="imagen" type="file" name="imagen" accept="image/jpeg,image/png,image/webp"
                        onchange="previsualizarImagen(event, 'vista-imagen')" class="w-full border rounded px-3 py-2">
                    <p class="mt-1 text-xs text-slate-500">Formatos JPG, PNG o WEBP. Tamaño máximo: 2 MB.</p>
                    <img id="vista-imagen" src="{{ $productos->imagen_url }}" alt="Vista previa de la imagen"
                        class="{{ $productos->imagen_url ? '' : 'hidden' }} mt-3 h-40 w-full rounded-lg border object-contain bg-slate-50">
                    @if ($productos->imagen)
                        <label class="mt-3 inline-flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="eliminar_imagen" value="1" @checked(old('eliminar_imagen'))>
                            Eliminar la imagen actual al guardar
                        </label>
                    @endif
                </div>
                <div class="mb-5">
                    <label for="estado" class="block mb-2 font-semibold">Estado</label>
                    <select id="estado" name="estado" required class="w-full border rounded px-3 py-2">
                        <option value="1" @selected(old('estado', (string) $productos->estado) == '1')>Activo</option>
                        <option value="0" @selected(old('estado', (string) $productos->estado) == '0')>Inactivo</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="id_categoria" class="block mb-2 font-semibold">Categoría</label>
                    <select id="id_categoria" name="id_categoria" required class="w-full border rounded px-3 py-2">
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @selected(old('id_categoria', $productos->id_categoria) == $categoria->id)>{{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white rounded px-5 py-2">Guardar
                        cambios</button>
                    <a href="{{ route('productos.index') }}" class="border rounded px-5 py-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previsualizarImagen(evento, destino) {
            const imagen = document.getElementById(destino);
            const archivo = evento.target.files && evento.target.files[0];

            if (!archivo) {
                return;
            }

            imagen.src = URL.createObjectURL(archivo);
            imagen.classList.remove('hidden');
        }
    </script>
@endpush
