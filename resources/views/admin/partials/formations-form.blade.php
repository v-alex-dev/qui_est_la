<h2 class="text-xl font-bold mb-4">Gérer les formations</h2>
<form method="POST" action="{{ route('admin.trainings.store') }}" class="mb-6 flex flex-col md:flex-row gap-4 items-end">
    @csrf
    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700">Nom de la formation</label>
        <input type="text" name="name" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ajouter</button>
</form>

<table class="min-w-full bg-white border rounded shadow">
    <thead>
        <tr>
            <th class="px-4 py-2 border-b">Nom</th>
            <th class="px-4 py-2 border-b">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($trainings as $training)
            <tr>
                <td class="px-4 py-2 border-b">{{ $training->name }}</td>
                <td class="px-4 py-2 border-b flex gap-2">
                    <a href="{{ route('admin.trainings.edit', $training) }}" class="text-blue-600 hover:underline">Éditer</a>
                    <form method="POST" action="{{ route('admin.trainings.destroy', $training) }}" onsubmit="return confirm('Supprimer cette formation ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="px-4 py-2 text-center text-gray-500">Aucune formation enregistrée.</td>
            </tr>
        @endforelse
    </tbody>
</table> 