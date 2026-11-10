<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar mi Tienda | VOID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 px-4 py-10 text-slate-100 flex items-center justify-center">

    <main class="w-full max-w-2xl rounded-2xl bg-white p-8 text-slate-900 shadow-2xl">
        {{-- Logotipo y Encabezado --}}
        <div class="text-center">
            <a href="{{ route('inicio') }}" class="text-3xl font-extrabold tracking-widest text-slate-900">
                V<span class="text-blue-600">Ø</span>ID
            </a>
            <h1 class="mt-4 text-2xl font-bold">Registra tu tienda en VOID</h1>
            <p class="mt-1 text-sm text-slate-500">Comienza a administrar tus productos y publica tu catálogo gratis.</p>
        </div>

        {{-- Mostrar errores si algo falla (ej. correo ya registrado) --}}
        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario que envía los datos al RegistroTiendaController --}}
        <form method="POST" action="{{ route('registro.tienda.store') }}" class="mt-6 space-y-6">
            @csrf

            {{-- SECCIÓN 1: DATOS DE LA TIENDA --}}
            <div class="border-b border-slate-200 pb-5">
                <h2 class="text-lg font-semibold text-blue-600">1. Información de la Tienda</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-600">Nombre de la Tienda *</label>
                        <input type="text" name="nombre_tienda" value="{{ old('nombre_tienda') }}" required class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 outline-none focus:border-blue-600">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold uppercase text-slate-600">Descripción</label>
                        <textarea name="descripcion_tienda" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 outline-none focus:border-blue-600">{{ old('descripcion_tienda') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600">Teléfono de contacto</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion') }}" class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 outline-none focus:border-blue-600">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: DATOS DEL PROPIETARIO (ADMINISTRADOR) --}}
            <div>
                <h2 class="text-lg font-semibold text-blue-600">2. Cuenta del Administrador</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600">Apellido *</label>
                        <input type="text" name="apellido" value="{{ old('apellido') }}" required class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600">Documento / Cédula *</label>
                        <input type="text" name="documento" value="{{ old('documento') }}" required class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600">Correo Electrónico (Login) *</label>
                        <input type="email" name="correo" value="{{ old('correo') }}" required class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600">Contraseña *</label>
                        <input type="password" name="password" required class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600">Confirmar Contraseña *</label>
                        <input type="password" name="password_confirmation" required class="mt-1 w-full rounded-lg border border-slate-300 p-2.5 outline-none focus:border-blue-600">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full rounded-lg bg-blue-600 py-3 font-semibold text-white transition hover:bg-blue-700">
                Crear Tienda y Entrar
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            ¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:underline">Inicia sesión</a>
        </p>
    </main>

</body>
</html>