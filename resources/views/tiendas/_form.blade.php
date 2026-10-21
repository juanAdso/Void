<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="nombre" class="mb-1.5 block text-sm font-semibold text-slate-700">Nombre de la tienda <span class="text-red-500">*</span></label>
        <input id="nombre" name="nombre" type="text" maxlength="100" required value="{{ old('nombre', $tienda?->nombre) }}" class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100" placeholder="Ej. Tienda Central">
        @error('nombre') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="descripcion" class="mb-1.5 block text-sm font-semibold text-slate-700">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="3" class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100" placeholder="Describe brevemente la tienda">{{ old('descripcion', $tienda?->descripcion) }}</textarea>
        @error('descripcion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="logo" class="mb-1.5 block text-sm font-semibold text-slate-700">Logo (URL o ruta)</label>
        <input id="logo" name="logo" type="text" maxlength="255" value="{{ old('logo', $tienda?->logo) }}" class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100" placeholder="https://...">
        @error('logo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="direccion" class="mb-1.5 block text-sm font-semibold text-slate-700">Dirección</label>
        <input id="direccion" name="direccion" type="text" maxlength="255" value="{{ old('direccion', $tienda?->direccion) }}" class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100" placeholder="Dirección de la tienda">
        @error('direccion') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="telefono" class="mb-1.5 block text-sm font-semibold text-slate-700">Teléfono</label>
        <input id="telefono" name="telefono" type="text" maxlength="20" value="{{ old('telefono', $tienda?->telefono) }}" class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100" placeholder="Número de contacto">
        @error('telefono') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="correo" class="mb-1.5 block text-sm font-semibold text-slate-700">Correo electrónico</label>
        <input id="correo" name="correo" type="email" maxlength="100" value="{{ old('correo', $tienda?->correo) }}" class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100" placeholder="contacto@tienda.com">
        @error('correo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="estado" class="mb-1.5 block text-sm font-semibold text-slate-700">Estado <span class="text-red-500">*</span></label>
        <select id="estado" name="estado" required class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
            <option value="1" {{ (string) old('estado', $tienda?->estado ?? 1) === '1' ? 'selected' : '' }}>Activa</option>
            <option value="0" {{ (string) old('estado', $tienda?->estado ?? 1) === '0' ? 'selected' : '' }}>Inactiva</option>
        </select>
        @error('estado') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
