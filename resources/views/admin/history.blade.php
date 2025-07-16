<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-gray-900">Historique complet</h1>
    </div>
  </x-slot>

  <div class="py-8">
    <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Toutes les visites et formations</h2>
            <div class="flex items-center space-x-4">
              @if(request('search'))
              <div class="text-sm text-gray-600">
                Résultats pour : <span class="font-medium">"{{ request('search') }}"</span>
              </div>
              @endif
              <div class="text-sm text-gray-500">
                Total : {{ $histories->count() }} {{ $histories->count() > 1 ? 'entrées' : 'entrée' }}
              </div>
            </div>
          </div>

          <!-- Barre de recherche -->
          <div class="mb-6">
            <form method="GET" action="{{ route('admin.history') }}" class="flex items-center space-x-4">
              <div class="flex-1 relative">
                <input
                  type="text"
                  name="search"
                  value="{{ request('search') }}"
                  placeholder="Rechercher par nom, email, formation, formateur, local..."
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
              </div>
              <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Rechercher
              </button>
              @if(request('search'))
              <a href="{{ route('admin.history') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Effacer
              </a>
              @endif
            </form>
          </div>

          @if(request('search'))
          <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-400">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-blue-700">
                  <span class="font-medium">{{ $histories->count() }} résultat(s)</span> trouvé(s) pour "<span class="font-medium">{{ request('search') }}</span>"
                </p>
              </div>
            </div>
          </div>
          @endif

          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Information</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Local</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure d'entrée</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure de sortie</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @forelse($histories as $history)
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    @if($history['type'] === 'visiteur')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                      <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="3" />
                      </svg>
                      Visiteur
                    </span>
                    @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="3" />
                      </svg>
                      Formation
                    </span>
                    @endif
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $history['name'] }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $history['email'] ?? '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $history['info'] }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800">
                      {{ $history['local'] }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $history['date'] }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($history['time'])
                    <time datetime="{{ $history['time'] }}">
                      {{ \Carbon\Carbon::parse($history['time'])->format('H:i') }}
                    </time>
                    @else
                    <span class="text-gray-400">-</span>
                    @endif
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($history['exit_time'])
                    <time datetime="{{ $history['exit_time'] }}">
                      {{ \Carbon\Carbon::parse($history['exit_time'])->format('H:i') }}
                    </time>
                    @elseif($history['type'] === 'visiteur')
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-yellow-100 text-yellow-800">
                      Encore présent
                    </span>
                    @else
                    <span class="text-gray-400">-</span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="8" class="px-6 py-12 text-center">
                    <div class="text-gray-400">
                      @if(request('search'))
                      <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                      <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun résultat trouvé</h3>
                      <p class="mt-1 text-sm text-gray-500">Aucune visite ou formation ne correspond à votre recherche "{{ request('search') }}".</p>
                      <div class="mt-4">
                        <a href="{{ route('admin.history') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                          Voir tout l'historique
                        </a>
                      </div>
                      @else
                      <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                      </svg>
                      <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun historique</h3>
                      <p class="mt-1 text-sm text-gray-500">Aucune visite ou formation enregistrée.</p>
                      @endif
                    </div>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if($histories->count() > 0)
          <div class="mt-4 text-sm text-gray-500 text-center">
            <div class="flex items-center justify-center space-x-6">
              <div class="flex items-center">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                  <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3" />
                  </svg>
                  Visiteurs
                </span>
                {{ $histories->where('type', 'visiteur')->count() }}
              </div>
              <div class="flex items-center">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                  <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3" />
                  </svg>
                  Formations
                </span>
                {{ $histories->where('type', 'formation')->count() }}
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>