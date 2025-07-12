<x-app-layout>
  <x-slot name="header">
    <h1 class="text-2xl font-bold mb-6">Gestion des données</h1>
  </x-slot>

  <div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- Messages de succès -->
      @if(session('success'))
      <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
      </div>
      @endif

      <!-- Onglets -->
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="border-b border-gray-200">
          <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button
              onclick="showTab('formations')"
              id="tab-formations"
              class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('tab', 'formations') === 'formations' ? 'border-indigo-500 text-indigo-600' : '' }}">
              Formations
            </button>
            <button
              onclick="showTab('staff')"
              id="tab-staff"
              class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request('tab') === 'staff' ? 'border-indigo-500 text-indigo-600' : '' }}">
              Personnel
            </button>
          </nav>
        </div>

        <!-- Contenu des onglets -->
        <div class="p-6">
          <!-- Onglet Formations -->
          <div id="content-formations" class="tab-content {{ request('tab', 'formations') === 'formations' ? '' : 'hidden' }}">
            <h2 class="text-xl font-semibold mb-4">Gestion des formations</h2>

            <!-- Formulaire ajout/modification formation -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
              <h3 class="text-lg font-medium mb-3">
                {{ isset($editingTraining) ? 'Modifier la formation' : 'Ajouter une formation' }}
              </h3>
              <form method="POST" action="{{ isset($editingTraining) ? route('admin.trainings.update', $training) : route('admin.trainings.store') }}">
                @csrf
                @if(isset($editingTraining))
                @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Intitulé</label>
                    <input type="text" name="title" id="title"
                      value="{{ old('title', isset($training) ? $training->title : '') }}"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      required>
                    @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" id="date"
                      value="{{ old('date', isset($training) ? $training->date : '') }}"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      required>
                    @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label for="room" class="block text-sm font-medium text-gray-700">Local</label>
                    <input type="text" name="room" id="room"
                      value="{{ old('room', isset($training) ? $training->room : '') }}"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      required>
                    @error('room')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label for="staff_member_id" class="block text-sm font-medium text-gray-700">Formateur</label>
                    <select name="staff_member_id" id="staff_member_id"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      required>
                      <option value="">Sélectionnez un formateur</option>
                      @foreach($allStaffMembers->where('function', 'formateur') as $staffMember)
                      <option value="{{ $staffMember->id }}"
                        {{ old('staff_member_id', isset($training) ? $training->staff_member_id : '') == $staffMember->id ? 'selected' : '' }}>
                        {{ $staffMember->first_name }} {{ $staffMember->last_name }}
                      </option>
                      @endforeach
                    </select>
                    @error('staff_member_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>
                </div>

                <div class="mt-4 flex space-x-3">
                  <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md">
                    {{ isset($editingTraining) ? 'Modifier' : 'Ajouter' }}
                  </button>
                  @if(isset($editingTraining))
                  <a href="{{ route('admin.manage-data', ['tab' => 'formations']) }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md">
                    Annuler
                  </a>
                  @endif
                </div>
              </form>
            </div>

            <!-- Tableau des formations -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intitulé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Local</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @forelse($trainings as $formation)
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $formation->title }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($formation->date)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $formation->room }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ $formation->staffMember ? $formation->staffMember->first_name . ' ' . $formation->staffMember->last_name : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                      <a href="{{ route('admin.trainings.edit', $formation) }}"
                        class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                      <form method="POST" action="{{ route('admin.trainings.destroy', $formation) }}"
                        class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette formation ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                      </form>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune formation trouvée</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          <!-- Onglet Personnel -->
          <div id="content-staff" class="tab-content {{ request('tab') === 'staff' ? '' : 'hidden' }}">
            <h2 class="text-xl font-semibold mb-4">Gestion du personnel</h2>

            <!-- Formulaire ajout/modification personnel -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
              <h3 class="text-lg font-medium mb-3">
                {{ isset($editingStaff) ? 'Modifier le membre du personnel' : 'Ajouter un membre du personnel' }}
              </h3>
              <form method="POST" action="{{ isset($editingStaff) ? route('admin.staff.update', $staff) : route('admin.staff.store') }}">
                @csrf
                @if(isset($editingStaff))
                @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input type="text" name="first_name" id="first_name"
                      value="{{ old('first_name', isset($staff) ? $staff->first_name : '') }}"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      required>
                    @error('first_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" name="last_name" id="last_name"
                      value="{{ old('last_name', isset($staff) ? $staff->last_name : '') }}"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      required>
                    @error('last_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label for="function" class="block text-sm font-medium text-gray-700">Fonction</label>
                    <select name="function" id="function"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      required>
                      <option value="">Sélectionnez une fonction</option>
                      <option value="formateur" {{ old('function', isset($staff) ? $staff->function : '') === 'formateur' ? 'selected' : '' }}>Formateur</option>
                      <option value="administration" {{ old('function', isset($staff) ? $staff->function : '') === 'administration' ? 'selected' : '' }}>Administration</option>
                    </select>
                    @error('function')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label for="room" class="block text-sm font-medium text-gray-700">Local</label>
                    <input type="text" name="room" id="room_staff"
                      value="{{ old('room', isset($staff) ? $staff->room : '') }}"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                      required>
                    @error('room')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone interne</label>
                    <input type="text" name="phone" id="phone"
                      value="{{ old('phone', isset($staff) ? $staff->phone : '') }}"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>
                </div>

                <div class="mt-4 flex space-x-3">
                  <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md">
                    {{ isset($editingStaff) ? 'Modifier' : 'Ajouter' }}
                  </button>
                  @if(isset($editingStaff))
                  <a href="{{ route('admin.manage-data', ['tab' => 'staff']) }}"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md">
                    Annuler
                  </a>
                  @endif
                </div>
              </form>
            </div>

            <!-- Tableau du personnel -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prénom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fonction</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Local</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  @forelse($staffMembers as $member)
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $member->last_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->first_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member->function === 'formateur' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($member->function) }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->room }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->phone ?: '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                      <a href="{{ route('admin.staff.edit', $member) }}"
                        class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                      <form method="POST" action="{{ route('admin.staff.destroy', $member) }}"
                        class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce membre du personnel ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                      </form>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun membre du personnel trouvé</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function showTab(tabName) {
      // Masquer tous les contenus
      document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
      });

      // Réinitialiser tous les boutons d'onglets
      document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-indigo-500', 'text-indigo-600');
        button.classList.add('border-transparent', 'text-gray-500');
      });

      // Afficher le contenu sélectionné
      document.getElementById('content-' + tabName).classList.remove('hidden');

      // Mettre en surbrillance le bouton sélectionné
      const activeButton = document.getElementById('tab-' + tabName);
      activeButton.classList.remove('border-transparent', 'text-gray-500');
      activeButton.classList.add('border-indigo-500', 'text-indigo-600');

      // Mettre à jour l'URL
      const url = new URL(window.location);
      url.searchParams.set('tab', tabName);
      window.history.replaceState({}, '', url);
    }

    // Initialiser l'onglet actif au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const activeTab = urlParams.get('tab') || 'formations';
      showTab(activeTab);
    });
  </script>
</x-app-layout>