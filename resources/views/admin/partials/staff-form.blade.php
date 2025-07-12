<h2 class="text-xl font-bold mb-4">Gérer le personnel</h2>
<form method="POST" action="{{ route('admin.staff.store') }}" class="mb-6 flex flex-col md:flex-row gap-4 items-end">
    @csrf
    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700">Nom</label>
        <input type="text" name="name" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
    </div>
    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700">Prénom</label>
        <input type="text" name="firstname" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
    </div>
    <div class="flex-1">
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Ajouter</button>
</form>

<table class="min-w-full bg-white border rounded shadow">
    <thead>
        <tr>
            <th class="px-4 py-2 border-b">Nom</th>
            <th class="px-4 py-2 border-b">Prénom</th>
            <th class="px-4 py-2 border-b">Email</th>
            <th class="px-4 py-2 border-b">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($staffMembers as $staff)
            <tr>
                <td class="px-4 py-2 border-b">{{ $staff->name }}</td>
                <td class="px-4 py-2 border-b">{{ $staff->firstname }}</td>
                <td class="px-4 py-2 border-b">{{ $staff->email }}</td>
                <td class="px-4 py-2 border-b flex gap-2">
                    <a href="{{ route('admin.staff.edit', $staff) }}" class="text-blue-600 hover:underline">Éditer</a>
                    <form method="POST" action="{{ route('admin.staff.destroy', $staff) }}" onsubmit="return confirm('Supprimer ce membre ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-4 py-2 text-center text-gray-500">Aucun membre enregistré.</td>
            </tr>
        @endforelse
    </tbody>
</table> 