<x-filament::widget>
    <x-filament::card>
        <div wire:ignore>
            <div class="p-4 bg-white rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">
                    Farmer Locations
                </h2>
                
                @php
                    $center = $this->getViewData()['center'];
                    $farmers = $this->getViewData()['farmers'];
                    $communitiesCount = $this->getViewData()['communitiesCount'] ?? 0;
                @endphp
                
                @if($farmers->isNotEmpty())
                    <!-- Map container with fixed height -->
                    <div class="relative rounded-lg overflow-hidden" style="height: 400px;">
                        <!-- Loading indicator -->
                        <div id="map-loader" class="absolute inset-0 flex items-center justify-center bg-gray-100 z-10">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-primary-600 mx-auto"></div>
                                <p class="mt-2 text-gray-600">Loading map...</p>
                            </div>
                        </div>
                        
                        <!-- Map will be rendered here -->
                        <div id="map" class="w-full h-full z-0"></div>
                        
                        <!-- Fallback static map (hidden by default) -->
                        <div id="static-map" class="w-full h-full absolute inset-0 z-5 hidden">
                            @if(isset($center['lat']) && isset($center['lng']))
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <p class="text-gray-500">Interactive map failed to load. Please refresh the page.</p>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <p class="text-gray-500">Map location data unavailable</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Farmer count summary -->
                    <div class="mt-3 text-sm text-gray-600">
                        Showing {{ count($farmers) }} farmers across {{ $communitiesCount }} communities.
                    </div>
                @else
                    <div class="py-4 text-center text-gray-500">
                        No farmers with location data available.
                    </div>
                @endif
            </div>
        </div>
    </x-filament::card>

    <!-- Load Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    
    <!-- Inline CSS for map -->
    <style>
        .leaflet-container {
            height: 100%;
            width: 100%;
        }
    </style>

    <!-- Load Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
        // Use Livewire's load event as recommended for Filament widgets
        document.addEventListener('livewire:load', function() {
            // Wait a moment to ensure DOM is fully ready
            setTimeout(function() {
                initializeMap();
            }, 100);
        });
        
        function initializeMap() {
            // Get elements
            const mapElement = document.getElementById('map');
            const loaderElement = document.getElementById('map-loader');
            const staticMapElement = document.getElementById('static-map');
            
            // Get data from PHP
            const farmers = @json($farmers);
            const center = @json($center);
            
            // Validate required elements and data
            if (!mapElement) {
                console.error('Map element not found');
                showFallback(loaderElement, staticMapElement);
                return;
            }
            
            if (!farmers || farmers.length === 0) {
                console.error('No farmer data available');
                showFallback(loaderElement, staticMapElement);
                return;
            }
            
            if (!center || !center.lat || !center.lng) {
                console.error('Invalid center coordinates');
                showFallback(loaderElement, staticMapElement);
                return;
            }
            
            try {
                // Initialize the map
                const map = L.map(mapElement).setView([center.lat, center.lng], 8);
                
                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                
                // Create marker cluster group
                const markers = L.markerClusterGroup();
                let validMarkers = 0;
                
                // Add markers for each farmer with coordinates
                farmers.forEach(function(farmer) {
                    if (farmer.latitude && farmer.longitude) {
                        const marker = L.marker([farmer.latitude, farmer.longitude]);
                        marker.bindPopup(`
                            <strong>${farmer.name}</strong><br>
                            Community: ${farmer.community}<br>
                            Trees Planted: ${farmer.trees_planted}
                        `);
                        markers.addLayer(marker);
                        validMarkers++;
                    }
                });
                
                // Add markers to map
                map.addLayer(markers);
                
                // Fit bounds if we have markers
                if (validMarkers > 0) {
                    map.fitBounds(markers.getBounds().pad(0.1));
                }
                
                // Hide loader when map is ready
                if (loaderElement) {
                    loaderElement.style.display = 'none';
                }
                
                console.log('Map initialized successfully with', validMarkers, 'markers');
                
            } catch (error) {
                console.error('Map initialization failed:', error);
                showFallback(loaderElement, staticMapElement);
            }
        }
        
        function showFallback(loaderElement, staticMapElement) {
            // Hide loader
            if (loaderElement) {
                loaderElement.style.display = 'none';
            }
            
            // Show static fallback
            if (staticMapElement) {
                staticMapElement.classList.remove('hidden');
            }
        }
    </script>
</x-filament::widget>
