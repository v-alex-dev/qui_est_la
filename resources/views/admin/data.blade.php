@extends('admin.partials.nav')

@section('content')
    <div class="max-w-4xl mx-auto mt-8 bg-white rounded shadow p-6">
        @include('admin.partials.data-tabs')
        <div class="mt-6">
            @if(request('tab', 'formations') === 'formations')
                @include('admin.partials.formations-form')
            @else
                @include('admin.partials.staff-form')
            @endif
        </div>
    </div>
@endsection