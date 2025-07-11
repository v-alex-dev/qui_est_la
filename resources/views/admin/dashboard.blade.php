<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold mb-6">Présences et formations du jour</h1>
    </x-slot>
    <div class="py-8">
        <div class="bg-white shadow rounded-lg overflow-hidden ">
            <table class=" divide-y divide-gray-200 mx-auto" >
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Local</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-blue-50 transition border-b-1 p-b-1">
                            <td class="px-4 py-3">
                                @if($entry['type'] === 'visiteur')
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Visiteur</span>
                                @else
                                    <span class="inline-block bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Formation</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $entry['name'] }}</td>
                            <td class="px-4 py-3">{{ $entry['info'] }}</td>
                            <td class="px-4 py-3">{{ $entry['local'] }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($entry['time'])->format('H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-400">Aucune présence ou formation aujourd'hui.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>