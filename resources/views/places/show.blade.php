@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold mb-6">Város: {{ $entity->name ?? 'N/A' }}</h1>

                <div class="mb-6">
                    <div class="mb-4">
                        <span class="block text-gray-700 font-bold">ID:</span>
                        <span class="text-gray-900">{{ $entity->id ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-4">
                        <span class="block text-gray-700 font-bold">Város Név:</span>
                        <span class="text-gray-900">{{ $entity->name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-4">
                        <span class="block text-gray-700 font-bold">Megye:</span>
                        <span class="text-gray-900">{{ $entity->county->name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-4">
                        <span class="block text-gray-700 font-bold">Irányítószám:</span>
                        <span class="text-gray-900">{{ $entity->postal_code->code ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <a href="{{ route('places.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                        Vissza
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
