<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-white rounded-xl shadow">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Purchase Order #{{ $purchaseOrder->po_number }}</h3>
                    <p class="text-sm text-gray-500">
                        Vendor: {{ $purchaseOrder->vendor->name }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Order Date: {{ $purchaseOrder->order_date->format('M d, Y') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Expected Delivery: {{ $purchaseOrder->expected_delivery_date->format('M d, Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ 
                        match($purchaseOrder->status) {
                            'draft' => 'bg-gray-100 text-gray-800',
                            'sent' => 'bg-blue-100 text-blue-800',
                            'received' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800',
                        }
                    }}">
                        {{ ucfirst($purchaseOrder->status) }}
                    </div>
                    @if($purchaseOrder->approved_at)
                        <p class="text-sm text-gray-500 mt-2">
                            Approved by: {{ $purchaseOrder->approvedBy?->name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            On: {{ $purchaseOrder->approved_at->format('M d, Y H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl shadow">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Goods Receipt</h3>
            
            <form wire:submit="save" class="space-y-6">
                {{ $this->form }}

                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Received Items</h3>
                    {{ $this->table }}
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a 
                        href="{{ $this->getResource()::getUrl('view', ['record' => $purchaseOrder]) }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    >
                        Save Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (session('status')) 
        <div class="fixed bottom-4 right-4 p-4 bg-green-100 border border-green-200 rounded-lg shadow-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('status') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
