<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | VOID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-950 px-4 py-10 text-slate-900">
    <main class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
        <a href="{{ route('inicio') }}" class="text-2xl font-extrabold tracking-widest">V<span class="text-violet-600">Ø</span>ID</a>
        <h1 class="mt-8 text-2xl font-bold">Acceso administrativo</h1>
        <p class="mt-2 text-sm text-slate-500">Ingresa con tu cuenta para administrar tiendas, productos, clientes y facturas.</p>

        @if ($errors->any())
            <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="mt-5 rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold">Correo electrónico</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-violet-600 focus:ring-2 focus:ring-violet-100">
            </div>
            <div>
                <label for="password" class="mb-2 block text-sm font-semibold">Contraseña</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-violet-600 focus:ring-2 focus:ring-violet-100">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                Mantener sesión iniciada
            </label>
            <button type="submit" class="w-full rounded-lg bg-violet-600 px-4 py-3 font-semibold text-white transition hover:bg-violet-700">Iniciar sesión</button>
        </form>
        <a href="{{ route('inicio') }}" class="mt-6 block text-center text-sm font-medium text-slate-500 hover:text-violet-700">Volver al catálogo público</a>
    </main>
</body>
</html>
