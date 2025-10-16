<x-filament::widget>
    <x-filament::card class="overflow-hidden">
        <div class="p-4">
            <!-- Compact Header -->
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">
                    Quick Actions
                </h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ count($shortcuts) }} available
                </span>
            </div>

            <!-- Horizontal Scrolling Actions -->
            <div class="flex space-x-3 overflow-x-auto pb-2 no-scrollbar">
                @foreach($shortcuts as $shortcut)
                    <a 
                        href="{{ $shortcut['url'] }}"
                        @class([
                            'group flex flex-col items-center justify-center p-3 rounded-lg transition-all duration-150 text-center flex-shrink-0 whitespace-nowrap w-24',
                            'focus:outline-none focus:ring-2 focus:ring-offset-2',
                            'text-'.($shortcut['color'] ?? 'gray').'-700 bg-'.($shortcut['color'] ?? 'gray').'-50 hover:bg-'.($shortcut['color'] ?? 'gray').'-100',
                            'dark:text-'.($shortcut['color'] ?? 'gray').'-300 dark:bg-'.($shortcut['color'] ?? 'gray').'-900/50 dark:hover:bg-'.($shortcut['color'] ?? 'gray').'-900',
                            'focus:ring-'.($shortcut['color'] ?? 'primary').'-500',
                        ])
                    >
                        <x-filament::icon 
                            :icon="$shortcut['icon']" 
                            @class([
                                'w-6 h-6 mb-1 transition-colors',
                                'text-'.($shortcut['color'] ?? 'gray').'-500',
                                'dark:text-'.($shortcut['color'] ?? 'gray').'-400',
                            ])
                        />
                        <span class="text-xs font-medium transition-colors">
                            {{ $shortcut['label'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </x-filament::card>
</x-filament::widget>

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
</style>
