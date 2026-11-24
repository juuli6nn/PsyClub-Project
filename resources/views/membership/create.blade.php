@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="border border-zinc-800 rounded-lg bg-zinc-950 p-6">
        <div class="flex items-center justify-between mb-4 pb-4 border-b border-zinc-800/80">
            <div>
                <h1 class="text-xl font-medium text-white">Membership Application</h1>
                <p class="text-xs text-zinc-400 mt-0.5">Complete the form below to submit your membership application.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 text-xs text-emerald-400 bg-emerald-950/40 border border-emerald-900/60 rounded p-3">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 text-xs text-red-400 bg-red-950/40 border border-red-900/60 rounded p-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('membership.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1">Membership Category:</label>
                <select name="membership_category_id" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">
                    <option value="">Choose a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('membership_category_id') == $category->id)>
                            {{ $category->name }}{{ $category->level ? ' - '.$category->level : '' }} (₱{{ number_format($category->fee, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1">Full Name:</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="e.g. Ana Cruz" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1">Email:</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="applicant@example.com" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1">Phone:</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0917-000-0000" class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1">Address:</label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Manila, Philippines" class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1.5">Location on Map (Click anywhere on the map to pin location):</label>
                <div id="map" class="w-full h-80 rounded-lg border border-zinc-800 bg-zinc-900 flex items-center justify-center text-xs text-zinc-500 overflow-hidden">
                    Loading Google Map... (Ensure GOOGLE_MAPS_KEY is configured in .env)
                </div>
                <input id="lat" type="hidden" name="latitude" value="{{ old('latitude', '14.5995') }}">
                <input id="lon" type="hidden" name="longitude" value="{{ old('longitude', '120.9842') }}">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="p-3 border border-zinc-800 rounded bg-zinc-900/50">
                    <div class="text-[10px] font-mono uppercase tracking-wider text-zinc-500 mb-1">Selected Coordinates</div>
                    <p id="coordinates" class="text-xs font-mono text-zinc-200">Click the map to choose a location.</p>
                </div>

                <div class="p-3 border border-zinc-800 rounded bg-zinc-900/50">
                    <div class="text-[10px] font-mono uppercase tracking-wider text-zinc-500 mb-1">Live OpenWeather Status</div>
                    <p id="weather" class="text-xs text-emerald-400 font-medium">Weather has not been loaded.</p>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="bg-white text-black text-xs font-medium px-4 py-2.5 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
                    Create Membership Application
                </button>
            </div>
        </form>
    </div>

    @if (isset($recentApplications) && $recentApplications->isNotEmpty())
        <div class="border border-zinc-800 rounded-lg bg-zinc-950 overflow-hidden">
            <div class="px-5 py-3 border-b border-zinc-800 flex items-center justify-between">
                <h2 class="text-xs font-medium text-zinc-300 uppercase tracking-wider font-mono">Recent Applications</h2>
                <span class="text-xs text-zinc-500">Synced with Admin TPS</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-zinc-800 bg-zinc-900/40 text-zinc-400">
                        <tr>
                            <th class="px-4 py-2.5 font-medium">Reference #</th>
                            <th class="px-4 py-2.5 font-medium">Applicant</th>
                            <th class="px-4 py-2.5 font-medium">Category</th>
                            <th class="px-4 py-2.5 font-medium">Coordinates</th>
                            <th class="px-4 py-2.5 font-medium">Email Queued At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/60 text-zinc-300">
                        @foreach ($recentApplications as $app)
                            <tr class="hover:bg-zinc-900/30">
                                <td class="px-4 py-2.5 font-mono text-white">{{ $app->reference_no }}</td>
                                <td class="px-4 py-2.5">
                                    <div class="text-white font-medium">{{ $app->full_name }}</div>
                                    <div class="text-zinc-500">{{ $app->email }}</div>
                                </td>
                                <td class="px-4 py-2.5">{{ $app->category ? $app->category->name : 'N/A' }}</td>
                                <td class="px-4 py-2.5 font-mono text-zinc-400">{{ $app->latitude }}, {{ $app->longitude }}</td>
                                <td class="px-4 py-2.5 text-emerald-400 font-mono">
                                    {{ $app->verification_sent_at ? $app->verification_sent_at->format('M d, H:i:s') : 'Pending' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let map, marker, leafletMap, leafletMarker;

function loadWeather(lat, lon) {
    document.getElementById('weather').textContent = 'Loading weather...';
    fetch(`/weather?lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`)
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(w => document.getElementById('weather').textContent = `${w.place}: ${w.temperature}°C, ${w.description}, humidity ${w.humidity}%`)
        .catch(() => document.getElementById('weather').textContent = 'Weather is currently unavailable.');
}

function setLocation(lat, lon) {
    document.getElementById('lat').value = lat.toFixed(7);
    document.getElementById('lon').value = lon.toFixed(7);
    document.getElementById('coordinates').textContent = `Latitude: ${lat.toFixed(5)}, Longitude: ${lon.toFixed(5)}`;
    if (marker) marker.setMap(null);
    if (typeof google !== 'undefined' && google.maps && map) {
        marker = new google.maps.Marker({position: {lat, lng: lon}, map});
    }
    if (leafletMap) {
        if (leafletMarker) leafletMarker.setLatLng([lat, lon]);
        else leafletMarker = L.marker([lat, lon]).addTo(leafletMap);
    }
    loadWeather(lat, lon);
}

function initMap() {
    const lat = Number(document.getElementById('lat').value);
    const lon = Number(document.getElementById('lon').value);
    if (leafletMap) { leafletMap.remove(); leafletMap = null; }
    map = new google.maps.Map(document.getElementById('map'), {center: {lat, lng: lon}, zoom: 13});
    map.addListener('click', event => setLocation(event.latLng.lat(), event.latLng.lng()));
    setLocation(lat, lon);
}

window.initMap = initMap;

document.addEventListener('DOMContentLoaded', () => {
    const initialLat = Number(document.getElementById('lat').value);
    const initialLon = Number(document.getElementById('lon').value);

    @if (! config('services.google_maps.key'))
        leafletMap = L.map('map').setView([initialLat, initialLon], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; OpenStreetMap contributors'}).addTo(leafletMap);
        leafletMap.on('click', e => setLocation(e.latlng.lat, e.latlng.lng));
    @endif

    setLocation(initialLat, initialLon);
});
</script>
@if (config('services.google_maps.key'))
<script async src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&loading=async&callback=initMap"></script>
@endif

@endsection
