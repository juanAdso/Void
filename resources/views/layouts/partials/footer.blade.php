<footer class="border-t border-neutral-800 bg-neutral-950 text-amber-50">
    <div class="px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-slate-400">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'VOID') }}. Todos los derechos reservados.</p>
        <p>Versión {{ config('app.version', '1.0.0') }}</p>
    </div>
</footer>
