@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        @if (! $user->profile_completed_at)
            <h1 class="text-xl font-medium text-white">Complete your profile</h1>
            <p class="text-xs text-zinc-400 mt-0.5">Your email is verified. Provide your contact and address details below to proceed to the dashboard.</p>
        @else
            <h1 class="text-xl font-medium text-white">Your contact details</h1>
            <p class="text-xs text-zinc-400 mt-0.5">{{ $user->name }} &bull; {{ $user->email }}</p>
        @endif
    </div>

    @if (session('success'))
        <div class="text-xs text-emerald-400 bg-emerald-950/40 border border-emerald-900/60 rounded p-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="text-xs text-red-400 bg-red-950/40 border border-red-900/60 rounded p-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" class="border border-zinc-800 rounded-lg bg-zinc-950 p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- Personal Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1">First name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" placeholder="e.g. Arianne" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1">Last name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" placeholder="e.g. Julian" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1">Verified email</label>
                <input type="email" value="{{ $user->email }}" readonly class="w-full bg-zinc-900/50 border border-zinc-800/80 rounded px-3 py-2 text-sm text-zinc-400 cursor-not-allowed">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-medium text-zinc-300">Phone</label>
                    <span class="text-[11px] text-zinc-500">Optional</span>
                </div>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+639170000000" class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
            </div>
        </div>

        <!-- Address Section (Manual or Auto-Filled by Map Pin) -->
        <div class="pt-4 border-t border-zinc-800/80 space-y-4">
            <div>
                <h2 class="text-sm font-medium text-white">Your address</h2>
                <p class="text-xs text-zinc-400 mt-0.5">Enter your address manually or place a map pin below to automatically fill in available address details.</p>
            </div>

            <div>
                <label class="block text-xs font-medium text-zinc-300 mb-1">House, street, and barangay</label>
                <input type="text" id="street_address" name="street_address" value="{{ old('street_address', $user->street_address) }}" placeholder="e.g. 151, Taft Avenue, Ermita" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1">City / municipality</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}" placeholder="e.g. Manila" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1">Province / region</label>
                    <input type="text" id="province" name="province" value="{{ old('province', $user->province) }}" placeholder="e.g. Metro Manila" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-zinc-300 mb-1">Country code</label>
                    <input type="text" id="country_code" name="country_code" value="{{ old('country_code', $user->country_code ?? 'PH') }}" required class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-500">
                    <p class="text-[11px] text-zinc-500 mt-1">Two-letter code, such as PH for the Philippines.</p>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-medium text-zinc-300">Postal code</label>
                        <span class="text-[11px] text-zinc-500">Optional</span>
                    </div>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" placeholder="e.g. 1000" class="w-full bg-zinc-900 border border-zinc-800 rounded px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:border-zinc-500">
                </div>
            </div>
        </div>

        <!-- Address Pin & Auto-Fill Map Section -->
        <div class="pt-4 border-t border-zinc-800/80 space-y-4">
            <div>
                <h2 class="text-sm font-medium text-white">Address pin</h2>
                <p class="text-xs text-zinc-400 mt-0.5">Click the map or drag the marker to update your address details. Weather updates automatically from your pin.</p>
            </div>

            <div class="relative rounded-lg overflow-hidden border border-zinc-800">
                <div id="profile-map" class="w-full h-72 bg-zinc-900 z-0"></div>
                <div id="map-loading-overlay" class="hidden absolute inset-0 bg-black/70 z-20 flex items-center justify-center p-4">
                    <div class="border border-zinc-700 rounded-lg bg-zinc-950 px-6 py-5 text-center max-w-xs shadow-xl">
                        <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                        <div class="text-sm font-medium text-white">Updating your address</div>
                        <p class="text-xs text-zinc-400 mt-1">Please wait while we find the details for your pin.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Pin latitude</label>
                    <input type="text" id="pin_lat" name="latitude" value="{{ old('latitude', $user->latitude ?? '14.5995000') }}" readonly class="w-full bg-zinc-900/60 border border-zinc-800 rounded px-3 py-2 text-xs font-mono text-zinc-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Pin longitude</label>
                    <input type="text" id="pin_lon" name="longitude" value="{{ old('longitude', $user->longitude ?? '120.9842000') }}" readonly class="w-full bg-zinc-900/60 border border-zinc-800 rounded px-3 py-2 text-xs font-mono text-zinc-300">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <button type="button" id="remove-pin-btn" class="text-sky-400 hover:underline underline-offset-4 cursor-pointer">Remove pin</button>
                <span id="pin-status-text" class="text-zinc-400">Click anywhere on the map to auto-fill your address.</span>
            </div>

            <div class="p-3.5 border border-zinc-800 rounded-lg bg-zinc-900/40 flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium text-white">Weather at your address</div>
                    <p id="profile-weather" class="text-xs text-emerald-400 mt-0.5">Loading live weather...</p>
                </div>
                <span class="font-mono text-[10px] text-zinc-500 uppercase">OpenWeather</span>
            </div>
        </div>

        <div class="pt-4 border-t border-zinc-800/80 flex items-center justify-end gap-2">
            @if ($user->profile_completed_at)
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-2 rounded border border-zinc-800 bg-zinc-900 text-xs font-medium text-zinc-200 hover:bg-zinc-800 hover:text-white transition-colors">
                    Cancel
                </a>
            @endif
            <button type="submit" class="bg-white text-black text-xs font-medium px-4 py-2 rounded hover:bg-zinc-200 transition-colors cursor-pointer">
                Save profile
            </button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const latInput = document.getElementById('pin_lat');
    const lonInput = document.getElementById('pin_lon');
    const streetInput = document.getElementById('street_address');
    const cityInput = document.getElementById('city');
    const provinceInput = document.getElementById('province');
    const countryInput = document.getElementById('country_code');
    const postalInput = document.getElementById('postal_code');
    const overlay = document.getElementById('map-loading-overlay');
    const statusText = document.getElementById('pin-status-text');
    const weatherEl = document.getElementById('profile-weather');
    const startLat = Number(latInput.value) || 14.5995;
    const startLon = Number(lonInput.value) || 120.9842;
    const map = L.map('profile-map').setView([startLat, startLon], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; OpenStreetMap contributors'}).addTo(map);
    let marker = L.marker([startLat, startLon], {draggable: true}).addTo(map);

    function loadProfileWeather(lat, lon) {
        weatherEl.textContent = 'Loading weather...';
        fetch(`/weather?lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`)
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(w => weatherEl.textContent = `${w.place}: ${w.temperature}°C, ${w.description}, humidity ${w.humidity}%`)
            .catch(() => weatherEl.textContent = 'Weather is temporarily unavailable.');
    }

    function reverseGeocodeAndFill(lat, lon) {
        latInput.value = lat.toFixed(7);
        lonInput.value = lon.toFixed(7);
        overlay.classList.remove('hidden');
        statusText.textContent = 'Finding address details for this pin...';
        loadProfileWeather(lat, lon);
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lon)}`, {headers: {'Accept-Language': 'en'}})
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(data => {
                const addr = data.address || {};
                const streetParts = [addr.house_number, addr.road || addr.pedestrian || addr.suburb, addr.neighbourhood || addr.quarter || addr.village].filter(Boolean);
                if (streetParts.length > 0) streetInput.value = streetParts.join(', ');
                else if (data.name) streetInput.value = data.name;
                const cityVal = addr.city || addr.town || addr.municipality || addr.county || '';
                if (cityVal) cityInput.value = cityVal;
                const provVal = addr.state || addr.region || addr.province || cityVal || '';
                if (provVal) provinceInput.value = provVal;
                if (addr.country_code) countryInput.value = addr.country_code.toUpperCase();
                if (addr.postcode) postalInput.value = addr.postcode;
                statusText.textContent = 'Address fields automatically updated from your pin.';
            })
            .catch(() => statusText.textContent = 'Pin updated. You may edit any missing street details manually.')
            .finally(() => overlay.classList.add('hidden'));
    }

    map.on('click', e => {
        const {lat, lng} = e.latlng;
        if (!marker) {
            marker = L.marker([lat, lng], {draggable: true}).addTo(map);
            marker.on('dragend', ev => { const pos = ev.target.getLatLng(); reverseGeocodeAndFill(pos.lat, pos.lng); });
        } else marker.setLatLng([lat, lng]);
        reverseGeocodeAndFill(lat, lng);
    });
    marker.on('dragend', e => { const pos = e.target.getLatLng(); reverseGeocodeAndFill(pos.lat, pos.lng); });
    document.getElementById('remove-pin-btn').addEventListener('click', () => {
        if (marker) { map.removeLayer(marker); marker = null; }
        statusText.textContent = 'Pin removed. Click the map to place a new pin.';
    });
    loadProfileWeather(startLat, startLon);
});
</script>

@endsection
