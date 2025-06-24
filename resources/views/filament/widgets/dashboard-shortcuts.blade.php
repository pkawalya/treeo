<x-filament::widget>
    <x-filament::card>
        <div class="p-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Quick Actions
                </h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ count($shortcuts) }} actions available
                </span>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-3">
                @foreach($shortcuts as $shortcut)
                    @php
                        $variant = $shortcut['variant'] ?? 'solid';
                        $isOutline = $variant === 'outline';
                        $bgColor = $isOutline ? 'bg-transparent' : "bg-{$shortcut['color']}-50 dark:bg-{$shortcut['color']}-900/20";
                        $textColor = $isOutline 
                            ? "text-{$shortcut['color']}-600 dark:text-{$shortcut['color']}-400" 
                            : "text-{$shortcut['color']}-700 dark:text-{$shortcut['color']}-300";
                        $hoverBg = $isOutline 
                            ? "hover:bg-{$shortcut['color']}-50 dark:hover:bg-{$shortcut['color']}-900/30" 
                            : "hover:bg-{$shortcut['color']}-100 dark:hover:bg-{$shortcut['color']}-900/40";
                        $borderColor = $isOutline 
                            ? "border border-{$shortcut['color']}-200 dark:border-{$shortcut['color']}-800" 
                            : "border border-transparent";
                    @endphp
                    
                    <a 
                        href="{{ $shortcut['url'] }}"
                        @class([
                            'group relative flex flex-col items-center justify-center p-3 rounded-lg transition-all',
                            'overflow-hidden',
                            $bgColor,
                            $textColor,
                            $hoverBg,
                            $borderColor,
                            'focus:outline-none focus:ring-2 focus:ring-offset-2',
                            'focus:ring-'.$shortcut['color'].'-500' => true,
                            'hover:shadow-sm',
                        ])
                        x-data="{ showTooltip: false }"
                        x-on:mouseenter="showTooltip = true"
                        x-on:mouseleave="showTooltip = false"
                    >
                        <!-- Tooltip -->
                        <div 
                            x-show="showTooltip"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute bottom-full mb-2 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded-md shadow-lg whitespace-nowrap"
                        >
                            {{ $shortcut['label'] }}
                            <div class="absolute w-2 h-2 -bottom-1 left-1/2 -ml-1 bg-gray-900 transform rotate-45"></div>
                        </div>
                        
                        <!-- Icon -->
                        <div class="p-2 rounded-full mb-2 {{ $isOutline ? 'bg-white/50 dark:bg-gray-800/50' : 'bg-white/20' }} group-hover:bg-white/30 transition-colors">
                            <x-filament::icon 
                                :icon="$shortcut['icon']" 
                                @class([
                                    'w-5 h-5',
                                    $textColor,
                                ])
                            />
                        </div>
                        
                        <!-- Label (hidden on small screens) -->
                        <span class="text-xs font-medium text-center line-clamp-1 hidden sm:block">
                            {{ $shortcut['label'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>
