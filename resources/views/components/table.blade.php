@props(['headers' => [], 'paging' => true])

<div class="w-full min-w-0 max-w-full rounded-xl border border-neutral-800 bg-neutral-900">
    <table class="datatable w-full min-w-full divide-y divide-neutral-800 bg-neutral-900 text-neutral-200"
        data-datatable-paging="{{ $paging ? 'true' : 'false' }}">
        <thead>
            <tr class="bg-neutral-800">
                @foreach ($headers as $header)
                    @php
                        $headerLabel = is_array($header) ? $header['label'] ?? '' : $header;
                        $headerNormalizado = strtolower(trim((string) $headerLabel));
                        $esColumnaAcciones = in_array(
                            $headerNormalizado,
                            ['accion', 'acción', 'acciones', 'opciones'],
                            true,
                        );
                        $esColumnaFecha = is_array($header) && ($header['date_filter'] ?? false);
                    @endphp
                    <th scope="col"
                        @if ($esColumnaAcciones) data-searchable="false" data-orderable="false" @endif
                        @if ($esColumnaFecha) data-date-filter="true" @endif
                        class="px-5 py-3.5 text-left text-xs font-bold text-neutral-100 uppercase tracking-wider whitespace-nowrap">
                        {{ $headerLabel }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-800 bg-neutral-900">
            {{ $slot }}
        </tbody>
    </table>
</div>
