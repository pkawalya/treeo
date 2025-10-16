<x-filament::page
    :widget-data="['record' => $record]"
>
    <div class="flex flex-col gap-y-6">
        @if ($heading = $this->getHeading())
            <h1 class="text-2xl font-bold tracking-tight md:text-3xl filament-header">
                {{ $heading }}
            </h1>
        @endif

        <!-- Header Widgets -->
        @if (count($this->getHeaderWidgets()))
            <div class="grid grid-cols-1 gap-6">
                @foreach ($this->getHeaderWidgets() as $widget)
                    @if ($widget::canView())
                        {{ $widget }}
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Content Section -->
        <div class="grid grid-cols-1 gap-6">
            <!-- Main Content -->
            @if (method_exists($this, 'getViewData'))
                {{ $this->getViewData() }}
            @endif
        </div>

        <!-- Footer Widgets -->
        @if (count($this->getFooterWidgets()))
            <div class="grid grid-cols-1 gap-6">
                @foreach ($this->getFooterWidgets() as $widget)
                    @if ($widget::canView())
                        {{ $widget }}
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</x-filament::page>

@pushOnce('styles')
<style>
    /* Custom styles for farmer view page */
    .filament-header {
        @apply text-emerald-700 dark:text-emerald-400;
    }
    
    .filament-tables-table-container {
        @apply rounded-xl overflow-hidden shadow-sm;
    }
    
    .filament-tables-header-cell {
        @apply bg-emerald-50 dark:bg-gray-800 text-emerald-900 dark:text-emerald-400;
    }
    
    .filament-stats-overview-stat-description {
        @apply text-emerald-600 dark:text-emerald-400;
    }
    
    /* Animated gradient background for stats */
    .filament-stats-overview-stat-card {
        @apply relative overflow-hidden;
    }
    
    .filament-stats-overview-stat-card::before {
        content: '';
        @apply absolute inset-0 bg-gradient-to-br from-white/5 to-white/20 dark:from-black/5 dark:to-black/20 opacity-0 transition-opacity duration-300;
    }
    
    .filament-stats-overview-stat-card:hover::before {
        @apply opacity-100;
    }
</style>
@endPushOnce
