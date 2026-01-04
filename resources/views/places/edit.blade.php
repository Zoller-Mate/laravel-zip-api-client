@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold mb-6">Város módosítása</h1>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500">
                        <div class="text-red-700">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('places.update', $entity->id ?? 0) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-bold mb-2">Város Név</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $entity->name ?? '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Város neve" />
                    </div>

                    <div class="mb-4">
                        <label for="county_id" class="block text-gray-700 font-bold mb-2">Megye</label>
                        <select id="county_id" name="county_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Válassz egy megyét --</option>
                            @foreach($counties as $county)
                                <option value="{{ $county->id }}" {{ old('county_id', $entity->county->id ?? '') == $county->id ? 'selected' : '' }}>{{ $county->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="postal_code_id" class="block text-gray-700 font-bold mb-2">Irányítószám</label>
                        <select id="postal_code_id" name="postal_code_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Válassz egy irányítószámot --</option>
                            @foreach($postalCodes as $postalCode)
                                <option value="{{ $postalCode->id }}" {{ old('postal_code_id', $entity->postal_code->id ?? '') == $postalCode->id ? 'selected' : '' }}>{{ $postalCode->code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex space-x-4">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Frissítés
                        </button>
                        <a href="{{ route('places.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                            Vissza
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
