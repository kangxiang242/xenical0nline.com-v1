<x-filament::widget>
    <x-filament::card>
        <div class="flex items-center justify-between gap-4 mb-4">
            <h2 class="text-lg font-bold">頁面訪問排行 (前10個)</h2>
            <select wire:model.live="filter" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @foreach($this->getFilters() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <table class="w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">#</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">訪問次數</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($this->getRanking() as $index => $log)
                    <tr>
                        <td class="px-2 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-2 py-2 text-sm text-gray-900 truncate" title="{{ $log->url }}">{{ $log->url }}</td>
                        <td class="px-2 py-2 text-sm text-gray-500">{{ $log->count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-2 py-4 text-sm text-gray-500 text-center">暫無數據</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-filament::card>
</x-filament::widget>