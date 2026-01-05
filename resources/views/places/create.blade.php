@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold mb-6">Új város létrehozása</h1>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500">
                        <div class="text-red-700">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('places.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700 font-bold mb-2">Város Név</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Város neve" />
                    </div>

                    <div class="mb-4">
                        <label for="county_id" class="block text-gray-700 font-bold mb-2">Megye</label>
                        <select id="county_id" name="county_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Válassz egy megyét --</option>
                            @foreach($counties as $county)
                                <option value="{{ $county->id }}" {{ old('county_id') == $county->id ? 'selected' : '' }}>{{ $county->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="postal_code" class="block text-gray-700 font-bold mb-2">Irányítószám</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="pl. 8000" maxlength="4" pattern="[0-9]{4}" />
                        <small style="color:#666; font-size:12px;">4 jegyű irányítószám (pl. 8000)</small>
                    </div>

                    <div style="display:flex; gap:16px;">
                        <button type="submit" style="padding:8px 16px; background:#2563eb; color:#fff; border:1px solid #1d4ed8; border-radius:6px; font-weight:600; cursor:pointer;">
                            Mentés
                        </button>
                        <a href="{{ route('places.index') }}" style="padding:8px 16px; background:#4b5563; color:#fff; border:1px solid #374151; border-radius:6px; font-weight:600; text-decoration:none; display:inline-block; cursor:pointer;">
                            Vissza
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
