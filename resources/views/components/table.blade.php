@props(['headers' => [], 'paging' => true])

<div class="w-full max-w-full overflow-x-auto rounded-xl border border-neutral-800 bg-neutral-900">
    <table class="datatable min-w-full w-full divide-y divide-neutral-800 bg-neutral-900 text-neutral-200"
        data-datatable-paging="{{ $paging ? 'true' : 'false' }}">
        <thead>
            <tr class="bg-neutral-800">
                @foreach ($headers as $header)
                    <th scope="col"
                        class="px-5 py-3.5 text-left text-xs font-bold text-neutral-100 uppercase tracking-wider whitespace-normal break-words">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-neutral-800 bg-neutral-900">
            {{ $slot }}
        </tbody>
    </table>
</div>
