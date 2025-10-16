@php
    $hasCoordinates = $this->record && $this->record->latitude && $this->record->longitude;
@endphp

<x-filament::section>
    <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
        <div class="p-4 bg-emerald-500 text-white flex items-center justify-between">
            <div class="flex items-center">
                <x-heroicon-o-map-pin class="w-5 h-5 mr-2" />
                <h3 class="text-lg font-medium">Farmer Location</h3>
            </div>
            @if($hasCoordinates)
                <a href="https://www.google.com/maps/search/?api=1&query={{ $this->record->latitude }},{{ $this->record->longitude }}" 
                   target="_blank" 
                   class="text-sm bg-white text-emerald-600 px-3 py-1 rounded-full hover:bg-emerald-50 transition-colors flex items-center">
                    <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4 mr-1" />
                    Open in Google Maps
                </a>
            @endif
        </div>
        
        <div class="relative">
            @if($hasCoordinates)
                <div id="map" class="w-full h-64"></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialize the map
                        const map = L.map('map').setView([{{ $this->record->latitude }}, {{ $this->record->longitude }}], 13);
                        
                        // Add the OpenStreetMap tiles
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
                        }).addTo(map);
                        
                        // Add a marker for the farmer's location
                        const marker = L.marker([{{ $this->record->latitude }}, {{ $this->record->longitude }}]).addTo(map);
                        
                        // Add a popup with farmer information
                        marker.bindPopup(`
                            <strong>{{ $this->record->name }}</strong><br>
                            {{ $this->record->village }}, {{ $this->record->parish }}<br>
                            {{ $this->record->sub_county }}, {{ $this->record->district }}
                        `).openPopup();
                        
                        // Add a circle to show approximate area
                        L.circle([{{ $this->record->latitude }}, {{ $this->record->longitude }}], {
                            color: '#10B981',
                            fillColor: '#10B981',
                            fillOpacity: 0.2,
                            radius: 500
                        }).addTo(map);
                    });
                </script>
            @else
                <div class="flex flex-col items-center justify-center p-8 bg-gray-50 dark:bg-gray-900 h-64">
                    <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <x-heroicon-o-map class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">No Location Data</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-1">
                        This farmer doesn't have GPS coordinates recorded.<br>
                        Update the farmer profile to add location data.
                    </p>
                    <a href="{{ route('filament.admin.resources.farmers.edit', $this->record) }}" 
                       class="mt-4 inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:border-emerald-700 focus:ring focus:ring-emerald-200 disabled:opacity-25 transition">
                        <x-heroicon-o-pencil class="w-4 h-4 mr-1" />
                        Add Location
                    </a>
                </div>
            @endif
        </div>
        
        @if($hasCoordinates)
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Latitude</span>
                    <p class="font-medium">{{ number_format($this->record->latitude, 6) }}</p>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Longitude</span>
                    <p class="font-medium">{{ number_format($this->record->longitude, 6) }}</p>
                </div>
            </div>
        @endif
    </div>
</x-filament::section>

@pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endPushOnce

@pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endPushOnce
