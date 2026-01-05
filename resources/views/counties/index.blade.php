@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500">
                <div class="text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500">
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-3xl font-bold">Megyék</h1>
                    @if ($isAuthenticated)
                        <a href="{{ route('counties.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Új megye
                        </a>
                    @endif
                </div>

                <!-- Search & Export -->
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <form method="GET" action="{{ route('counties.index') }}" class="w-full md:w-1/2">
                        <input type="text" name="needle" placeholder="Keresés..." value="{{ request('needle') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                    </form>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('counties.export.csv') }}" class="px-4 py-2 rounded font-semibold" style="background:#16a34a; color:#fff; border:1px solid #0f6a32; display:inline-block; text-decoration:none;">
                            CSV Export
                        </a>
                        <a href="{{ route('counties.export.pdf') }}" class="px-4 py-2 rounded font-semibold" style="background:#dc2626; color:#fff; border:1px solid #991b1b; display:inline-block; text-decoration:none;">
                            PDF Export
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <table class="w-full border-collapse table-fixed" style="table-layout:fixed;">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3" style="text-align:center; vertical-align:middle;">ID</th>
                        <th class="px-6 py-3" style="text-align:center; vertical-align:middle;">Név</th>
                        <th class="px-6 py-3" style="text-align:center; vertical-align:middle;">Műveletek</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entities as $county)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4" style="text-align:center; vertical-align:middle;">{{ $county->id }}</td>
                            <td class="px-6 py-4" style="text-align:center; vertical-align:middle;">{{ $county->name }}</td>
                            <td class="px-6 py-4 space-x-2" style="text-align:center; vertical-align:middle; white-space:nowrap;">
                                <a href="{{ route('counties.show', $county->id) }}" class="text-blue-600 hover:underline">Nézet</a>
                                @if ($isAuthenticated)
                                    <a href="{{ route('counties.edit', $county->id) }}" class="text-yellow-600 hover:underline">Módosítás</a>
                                    <form method="POST" action="{{ route('counties.destroy', $county->id) }}" style="display:inline;" onsubmit="return confirm('Biztosan törlöd?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Törlés</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Nincs megjelenítendő adat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
