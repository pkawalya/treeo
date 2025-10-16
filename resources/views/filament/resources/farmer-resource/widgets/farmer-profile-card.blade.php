<div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 rounded-xl overflow-hidden shadow-sm border border-green-100 dark:border-gray-700">
    <div class="relative overflow-hidden bg-gradient-to-r from-emerald-500 to-green-600 h-32">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
        <div class="absolute bottom-0 left-0 w-full h-16 bg-gradient-to-t from-white dark:from-gray-800 opacity-60"></div>
        
        @if($this->record->status === 'active')
            <div class="absolute top-4 right-4 px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-medium flex items-center">
                <span class="w-2 h-2 rounded-full bg-green-500 mr-1"></span>
                Active
            </div>
        @else
            <div class="absolute top-4 right-4 px-3 py-1 rounded-full bg-gray-100 text-gray-800 text-xs font-medium flex items-center">
                <span class="w-2 h-2 rounded-full bg-gray-500 mr-1"></span>
                Inactive
            </div>
        @endif
    </div>
    
    <div class="relative px-6 pb-6">
        <div class="flex flex-col md:flex-row">
            <!-- Farmer Avatar -->
            <div class="-mt-16 flex justify-center md:justify-start">
                <div class="w-24 h-24 rounded-full ring-4 ring-white dark:ring-gray-800 bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center shadow-lg">
                    <span class="text-3xl font-bold text-white">{{ substr($this->record->name, 0, 1) }}</span>
                </div>
            </div>
            
            <!-- Farmer Name and Quick Stats -->
            <div class="mt-4 md:mt-0 md:ml-6 flex-grow">
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->record->name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                            <x-heroicon-s-user-group class="w-4 h-4 mr-1" />
                            {{ $this->record->community->name ?? 'No Community' }}
                        </p>
                    </div>
                    <div class="mt-2 md:mt-0">
                        <a href="{{ route('filament.admin.resources.farmers.edit', $this->record) }}" class="inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button dark:focus:ring-offset-0 h-9 px-4 text-sm text-white shadow focus:ring-white border-transparent bg-emerald-600 hover:bg-emerald-500 focus:bg-emerald-700 focus:ring-offset-emerald-700 filament-page-button-action">
                            <x-heroicon-s-pencil class="w-4 h-4" />
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Expanded Information Section -->
        <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-3">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                        <x-heroicon-o-phone class="w-5 h-5 mr-2 text-gray-400" />
                        Contact
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white pl-7">{{ $this->record->phone ?? 'N/A' }}</dd>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white pl-7">{{ $this->record->email ?? 'No email' }}</dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                        <x-heroicon-o-map-pin class="w-5 h-5 mr-2 text-gray-400" />
                        Location
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white pl-7">{{ $this->record->village }}, {{ $this->record->parish }}</dd>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white pl-7">{{ $this->record->sub_county }}, {{ $this->record->district }}</dd>
                </div>

                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                        <x-heroicon-o-calendar-days class="w-5 h-5 mr-2 text-gray-400" />
                        Registration
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white pl-7">Joined on {{ $this->record->created_at->format('M d, Y') }}</dd>
                    @if($this->record->user)
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white pl-7">Managed by {{ $this->record->user->name }}</dd>
                    @endif
                </div>

                @if($this->record->latitude && $this->record->longitude)
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                        <x-heroicon-o-globe-alt class="w-5 h-5 mr-2 text-gray-400" />
                        Coordinates
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white pl-7">{{ number_format($this->record->latitude, 6) }}, {{ number_format($this->record->longitude, 6) }}</dd>
                </div>
                @endif
                
                @if($this->record->notes)
                <div class="sm:col-span-3">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                        <x-heroicon-o-pencil-square class="w-5 h-5 mr-2 text-gray-400" />
                        Notes
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white prose dark:prose-invert max-w-none pl-7">
                        {{ $this->record->notes }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
</div>

<style>
.bg-pattern {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.2'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
</style>
