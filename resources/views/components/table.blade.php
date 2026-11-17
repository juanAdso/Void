
@props(['headers' => []])

<div class="w-full max-w-full overflow-x-auto rounded-xl">
    <table class="min-w-max w-full divide-y divide-neutral-800">
        <thead>
            <tr class="bg-slate-50">
                @foreach ($headers as $header)
                    <th scope="col" class="px-5 py-3 text-left text-xs font-semibold text-neutral-500 uppercase tracking-wider whitespace-nowrap">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
