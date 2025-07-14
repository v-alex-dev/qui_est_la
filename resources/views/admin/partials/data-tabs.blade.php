<ul class="flex border-b mb-4">
    <li class="-mb-px mr-2">
        <a href="{{ route('admin.data', ['tab' => 'formations']) }}"
           class="inline-block py-2 px-4 border-b-2 {{ request('tab', 'formations') === 'formations' ? 'border-blue-500 font-semibold text-blue-600' : 'border-transparent text-gray-500 hover:text-blue-600' }}">
            Formations
        </a>
    </li>
    <li class="-mb-px mr-2">
        <a href="{{ route('admin.data', ['tab' => 'personnel']) }}"
           class="inline-block py-2 px-4 border-b-2 {{ request('tab') === 'personnel' ? 'border-blue-500 font-semibold text-blue-600' : 'border-transparent text-gray-500 hover:text-blue-600' }}">
            Personnel
        </a>
    </li>
</ul>