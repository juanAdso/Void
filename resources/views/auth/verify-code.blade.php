<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar código y restablecer | VOID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-950 px-4 py-10 text-slate-900">
    <main class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
        <a href="{{ route('inicio') }}" class="text-2xl font-extrabold tracking-widest">V<span class="text-violet-600">Ø</span>ID</a>
        <h1 class="mt-8 text-2xl font-bold">Restablecer contraseña</h1>
        <p class="mt-2 text-sm text-slate-500">Ingresa el código numérico de 6 dígitos enviado a tu correo y define tu nueva contraseña.</p>

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

        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
            @csrf
            <div>
                <label for="codigo" class="mb-2 block text-sm font-semibold">Código de 6 dígitos</label>
                <input id="codigo" name="codigo" type="text" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" placeholder="123456" required autofocus class="w-full text-center tracking-widest text-xl font-bold rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-violet-600 focus:ring-2 focus:ring-violet-100">
            </div>
            <div>
                <label for="password" class="mb-2 block text-sm font-semibold">Nueva contraseña</label>
                <input id="password" name="password" type="password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-violet-600 focus:ring-2 focus:ring-violet-100">
            </div>
            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-semibold">Confirmar nueva contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-violet-600 focus:ring-2 focus:ring-violet-100">
            </div>
            <button type="submit" class="w-full rounded-lg bg-violet-600 px-4 py-3 font-semibold text-white transition hover:bg-violet-700">Actualizar contraseña</button>
        </form>

        <div class="mt-6 flex flex-col gap-2 text-center text-sm">
            <a href="{{ route('password.request') }}" class="text-violet-600 hover:text-violet-800">¿No recibiste el código? Solicitar otro</a>
            <a href="{{ route('login') }}" class="text-slate-500 hover:text-violet-700">&larr; Volver a iniciar sesión</a>
        </div>
    </main>
</body>
</html>
