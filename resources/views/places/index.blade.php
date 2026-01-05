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
                    <h1 class="text-3xl font-bold">Városok</h1>
                    @if ($isAuthenticated)
                        <a href="{{ route('places.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Új város
                        </a>
                    @endif
                </div>

                <!-- Export -->
                <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
                    <a href="{{ route('places.export.csv') }}" style="padding:10px 16px; background:#16a34a; color:#fff; border:1px solid #0f6a32; border-radius:6px; font-weight:600; text-decoration:none; display:inline-block; cursor:pointer;">
                        📄 CSV Export
                    </a>
                    <a href="{{ route('places.export.pdf') }}" style="padding:10px 16px; background:#dc2626; color:#fff; border:1px solid #991b1b; border-radius:6px; font-weight:600; text-decoration:none; display:inline-block; cursor:pointer;">
                        📑 PDF Export
                    </a>
                </div>

                <!-- County Filter -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Megye kiválasztása:</label>
                    <select id="countySelect" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">-- Válassz egy megyét --</option>
                        @foreach($counties as $county)
                            <option value="{{ $county->id }}">{{ $county->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- ABC Filter (Initially hidden) -->
                <div id="letterButtons" style="display:none;" class="mb-4">
                    <h3 class="font-bold mb-2">Kezdőbetű szerint:</h3>
                    <div id="letters" class="flex flex-wrap gap-2"></div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" id="placesTableContainer" style="display:none;">
            <table class="w-full border-collapse table-fixed" style="table-layout:fixed;" id="placesTable">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3" style="text-align:center; vertical-align:middle;">Város</th>
                        <th class="px-6 py-3" style="text-align:center; vertical-align:middle;">Megye</th>
                        <th class="px-6 py-3" style="text-align:center; vertical-align:middle;">Irányítószám</th>
                        <th class="px-6 py-3" style="text-align:center; vertical-align:middle;">Műveletek</th>
                    </tr>
                </thead>
                <tbody id="placesTableBody">
                </tbody>
            </table>
        </div>

        <!-- No data message -->
        <div id="noDataMessage" class="bg-white p-6 rounded shadow text-center text-gray-500">
            Válassz egy megyét a városok listázásához.
        </div>
    </div>
</div>

<script>
    const countySelect = document.getElementById('countySelect');
    const letterButtons = document.getElementById('letterButtons');
    const letters = document.getElementById('letters');
    const placesTable = document.getElementById('placesTable');
    const placesTableBody = document.getElementById('placesTableBody');
    const placesTableContainer = document.getElementById('placesTableContainer');
    const noDataMessage = document.getElementById('noDataMessage');

    countySelect.addEventListener('change', function() {
        const countyId = this.value;
        if (!countyId) {
            letterButtons.style.display = 'none';
            placesTableContainer.style.display = 'none';
            noDataMessage.style.display = 'block';
            return;
        }

        // Fetch available letters
        fetch(`/places?county_id=${countyId}&letters_only=true`)
            .then(r => r.json())
            .then(data => {
                letters.innerHTML = '';
                if (data.length === 0) {
                    letterButtons.style.display = 'none';
                    placesTableContainer.style.display = 'none';
                    noDataMessage.style.display = 'block';
                    return;
                }

                data.forEach(letter => {
                    const btn = document.createElement('button');
                    btn.textContent = letter;
                    btn.type = 'button';
                    btn.style.background = '#2563eb';
                    btn.style.color = '#fff';
                    btn.style.border = '1px solid #1d4ed8';
                    btn.style.borderRadius = '6px';
                    btn.style.padding = '6px 10px';
                    btn.style.fontWeight = '600';
                    btn.style.cursor = 'pointer';
                    btn.onmouseenter = () => btn.style.background = '#1d4ed8';
                    btn.onmouseleave = () => btn.style.background = '#2563eb';
                    btn.onclick = () => fetchPlacesByLetter(countyId, letter);
                    letters.appendChild(btn);
                });

                letterButtons.style.display = 'block';
                noDataMessage.style.display = 'none';
            })
            .catch(err => {
                console.error('Hiba betűk lekérésekor:', err);
                letterButtons.style.display = 'none';
                placesTableContainer.style.display = 'none';
                noDataMessage.style.display = 'block';
            });
    });

    function fetchPlacesByLetter(countyId, letter) {
        fetch(`/places?county_id=${countyId}&letter=${encodeURIComponent(letter)}`)
            .then(r => r.json())
            .then(data => {
                placesTableBody.innerHTML = '';

                if (!data || data.length === 0) {
                    placesTableContainer.style.display = 'none';
                    noDataMessage.textContent = 'Nincs város ezzel a kezdőbetűvel.';
                    noDataMessage.style.display = 'block';
                    return;
                }

                data.forEach(place => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b hover:bg-gray-50';

                    const county = place.county ? place.county.name : 'N/A';
                    
                    // Backend returns postal_codes array, get first one
                    let postalCode = 'N/A';
                    if (place.postal_codes && place.postal_codes.length > 0) {
                        postalCode = place.postal_codes[0].postal_code;
                    }
                    
                    const operations = `
                        <a href="/places/${place.id}" class="text-blue-600 hover:underline">Nézet</a>
                        @if ($isAuthenticated)
                            <a href="/places/${place.id}/edit" class="text-yellow-600 hover:underline">Módosítás</a>
                        @endif
                    `;

                    tr.innerHTML = `
                        <td class="px-6 py-4" style="text-align:center; vertical-align:middle;">${place.name}</td>
                        <td class="px-6 py-4" style="text-align:center; vertical-align:middle;">${county}</td>
                        <td class="px-6 py-4" style="text-align:center; vertical-align:middle;">${postalCode}</td>
                        <td class="px-6 py-4 space-x-2" style="text-align:center; vertical-align:middle; white-space:nowrap;">${operations}</td>
                    `;
                    placesTableBody.appendChild(tr);
                    });

                    placesTableContainer.style.display = 'block';
                noDataMessage.style.display = 'none';
            })
            .catch(err => {
                console.error('Hiba városok lekérésekor:', err);
                placesTableContainer.style.display = 'none';
                noDataMessage.textContent = 'Hiba történt az adatok lekérésekor.';
                noDataMessage.style.display = 'block';
            });
    }
</script>
@endsection
