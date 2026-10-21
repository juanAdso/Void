<div>
    <label for="nombre">Nombre *</label>
    <input id="nombre" name="nombre" type="text" maxlength="100" required value="{{ old('nombre', $tienda?->nombre) }}">
</div>
<div>
    <label for="descripcion">Descripción</label>
    <textarea id="descripcion" name="descripcion">{{ old('descripcion', $tienda?->descripcion) }}</textarea>
</div>
<div>
    <label for="logo">Logo (URL o ruta)</label>
    <input id="logo" name="logo" type="text" maxlength="255" value="{{ old('logo', $tienda?->logo) }}">
</div>
<div>
    <label for="direccion">Dirección</label>
    <input id="direccion" name="direccion" type="text" maxlength="255" value="{{ old('direccion', $tienda?->direccion) }}">
</div>
<div>
    <label for="telefono">Teléfono</label>
    <input id="telefono" name="telefono" type="text" maxlength="20" value="{{ old('telefono', $tienda?->telefono) }}">
</div>
<div>
    <label for="correo">Correo electrónico</label>
    <input id="correo" name="correo" type="email" maxlength="100" value="{{ old('correo', $tienda?->correo) }}">
</div>
<div>
    <label for="estado">Estado *</label>
    <select id="estado" name="estado" required>
        <option value="1" {{ (string) old('estado', $tienda?->estado ?? 1) === '1' ? 'selected' : '' }}>Activa</option>
        <option value="0" {{ (string) old('estado', $tienda?->estado ?? 1) === '0' ? 'selected' : '' }}>Inactiva</option>
    </select>
</div>
