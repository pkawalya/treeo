<x-filament::widget>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
              integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
              crossorigin=""/>
        <style>
            #farmerMap {
                width: 100%;
                height: 500px;
            }
            .loading-spinner {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    @endpush

    <x-filament::card>
        <div class="p-4 bg-white rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">
                Farmer Locations
            </h2>
            
            @php
                $center = $this->getViewData()['center'];
                $farmers = $this->getViewData()['farmers'];
            @endphp
            
            <!-- Map Container -->
            <div 
                id="farmerMap" 
                class="w-full h-[500px] rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center"
                data-center="{{ json_encode($center) }}"
                data-farmers="{{ json_encode($farmers) }}"
            >
                <div class="text-center p-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary-500 mx-auto"></div>
                    <p class="mt-2 text-gray-600">Loading map...</p>
                </div>
            </div>
            
            <div id="mapError" class="hidden text-red-500 mt-2">
                <p>Unable to load the map. Please check your internet connection and refresh the page.</p>
                <p class="text-sm text-gray-500">If the problem persists, contact support.</p>
            </div>
        </div>
    </x-filament:card>



    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                crossorigin=""></script>
        <script>
        function initializeMap() {
            console.log('Initializing map...');
            const mapElement = document.getElementById('farmerMap');
            if (!mapElement) {
                console.error('Map container not found');
                return;
            }

            // Show loading state
            const loadingElement = mapElement.querySelector('.text-center');
            const errorElement = document.getElementById('mapError');

            // Ensure Leaflet is loaded
            if (typeof L === 'undefined') {
                console.error('Leaflet not loaded');
                if (errorElement) {
                    errorElement.textContent = 'Map library failed to load. Please refresh the page.';
                    errorElement.classList.remove('hidden');
                }
                if (loadingElement) loadingElement.style.display = 'none';
                return;
            }

            try {
                // Get data from PHP
                const center = @js($center);
                const farmers = @js($farmers);
                
                // Log detailed farmer data for debugging
                console.log('Map center:', center);
                console.log('Number of farmers:', farmers ? farmers.length : 0);
                console.log('Farmers data:', JSON.parse(JSON.stringify(farmers || [])));

                if (!center) {
                    console.warn('No center coordinates available, using default');
                    center = { lat: 1.3733, lng: 32.2903 }; // Default to Uganda coordinates
                }
                
                if (!farmers || !Array.isArray(farmers) || farmers.length === 0) {
                    console.warn('No farmer data available');
                    if (loadingElement) loadingElement.style.display = 'none';
                    if (errorElement) {
                        errorElement.textContent = 'No farmer location data available.';
                        errorElement.classList.remove('hidden');
                    }
                    return;
                }
                
                // Filter out any invalid farmer entries
                const validFarmers = farmers.filter(f => 
                    f && 
                    typeof f.latitude !== 'undefined' && 
                    typeof f.longitude !== 'undefined' &&
                    !isNaN(parseFloat(f.latitude)) && 
                    !isNaN(parseFloat(f.longitude))
                );
                
                console.log('Valid farmers with coordinates:', validFarmers.length);

                // Initialize the map
                const map = L.map(mapElement, {
                    zoomControl: true,
                    scrollWheelZoom: true,
                    doubleClickZoom: true,
                    touchZoom: true,
                    boxZoom: true,
                    preferCanvas: true
                }).setView([center.lat, center.lng], 8);

                // Store map instance globally
                window.mapInstance = map;

                // Add OpenStreetMap tiles with error handling
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    maxZoom: 19,
                    errorTileUrl: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
                }).addTo(map);

                // Handle tile loading errors
                map.on('tileerror', function() {
                    console.error('Tile loading error');
                    if (errorElement) {
                        errorElement.textContent = 'Error loading map tiles. Please check your internet connection.';
                        errorElement.classList.remove('hidden');
                    }
                });

                // Add markers for valid farmers
                const markers = [];
                let hasValidMarkers = validFarmers.length > 0;

                validFarmers.forEach((farmer, index) => {
                    const lat = parseFloat(farmer.latitude);
                    const lng = parseFloat(farmer.longitude);
                    
                    console.log(`Farmer ${index + 1}:`, {
                        name: farmer.name,
                        lat: lat,
                        lng: lng,
                        community: farmer.community,
                        trees_planted: farmer.trees_planted
                    });

                    const marker = L.marker([lat, lng], {
                        title: farmer.name || 'Unnamed Farmer'
                    })
                    .addTo(map)
                    .bindPopup(`
                        <div class="p-2 min-w-[200px]">
                            <h3 class="font-bold text-base">${escapeHtml(farmer.name || 'Unnamed Farmer')}</h3>
                            <div class="mt-1 text-sm">
                                <p class="text-gray-700">📍 ${escapeHtml(farmer.community || 'No Community')}</p>
                                <p class="text-green-700 font-medium">🌳 ${farmer.trees_planted || 0} trees planted</p>
                                <p class="text-xs text-gray-500 mt-1">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</p>
                            </div>
                        </div>
                    `);
                    
                    markers.push(marker);
                });

                console.log(`Added ${markers.length} markers to the map`);

                // Adjust view to show all markers if we have any
                if (hasValidMarkers) {
                    const group = L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                } else {
                    // If no valid markers, just show the center point
                    map.setView([center.lat, center.lng], 8);
                }

                // Fix map sizing and remove loading indicator
                const resizeMap = () => {
                    map.invalidateSize();
                    if (loadingElement) loadingElement.style.display = 'none';
                };

                // Initial resize
                setTimeout(resizeMap, 100);

                // Also resize when the tab becomes visible
                document.addEventListener('visibilitychange', () => {
                    if (!document.hidden) {
                        setTimeout(resizeMap, 300);
                    }
                });

            } catch (error) {
                console.error('Map initialization error:', error);
                if (loadingElement) loadingElement.style.display = 'none';
                if (errorElement) {
                    errorElement.textContent = 'Error initializing the map: ' + error.message;
                    errorElement.classList.remove('hidden');
                }
            }
        }

        // Simple HTML escaping
        function escapeHtml(unsafe) {
            if (!unsafe) return '';
            return String(unsafe)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Initialize map when Livewire is loaded
        document.addEventListener('livewire:load', function() {
            // Small delay to ensure DOM is ready
            setTimeout(initializeMap, 100);
        });

        // Also initialize if Livewire is already loaded
        if (window.livewire) {
            setTimeout(initializeMap, 100);
        }
        </script>
    @endpush
</x-filament::widget>
