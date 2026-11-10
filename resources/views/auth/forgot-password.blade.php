<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña | VOID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-950 px-4 py-10 text-slate-900">
    <main class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
        <a href="{{ route('inicio') }}" class="text-2xl font-extrabold tracking-widest">V<span class="text-violet-600">Ø</span>ID</a>
        <h1 class="mt-8 text-2xl font-bold">Recuperar contraseña</h1>
        <p class="mt-2 text-sm text-slate-500">Ingresa el correo electrónico de tu cuenta o de tu tienda. Te enviaremos un código de 6 dígitos para restablecer tu acceso.</p>

        @if (session('status'))
            <div class="mt-5 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
            @csrf
            <div>
                <label for="correo" class="mb-2 block text-sm font-semibold">Correo de la cuenta o tienda</label>
                <input id="correo" name="correo" type="email" value="{{ old('correo') }}" required autofocus placeholder="ejemplo@tienda.com" class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-violet-600 focus:ring-2 focus:ring-violet-100">
            </div>
            <button type="submit" class="w-full rounded-lg bg-violet-600 px-4 py-3 font-semibold text-white transition hover:bg-violet-700">Enviar código de recuperación</button>
        </form>

        <div class="mt-6 flex flex-col gap-2 text-center text-sm">
            <a href="{{ route('password.code') }}" class="text-violet-600 hover:text-violet-800">¿Ya tienes un código? Ingrésalo aquí</a>
            <a href="{{ route('login') }}" class="text-slate-500 hover:text-violet-700">&larr; Volver a iniciar sesión</a>
        </div>
    </main>
</body>
</html>
