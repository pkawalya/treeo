<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRN #{{ $this->goodsReceipt->grn_number }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 10mm;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }
        .logo {
            max-width: 150px;
        }
        .document-info {
            text-align: right;
        }
        .document-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .document-number {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }
        .row {
            display: flex;
            margin-bottom: 5px;
        }
        .col {
            flex: 1;
            padding: 0 5px;
        }
        .col-2 { width: 16.666667%; }
        .col-3 { width: 25%; }
        .col-4 { width: 33.333333%; }
        .col-6 { width: 50%; }
        .col-8 { width: 66.666667%; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .mt-3 { margin-top: 15px; }
        .mb-3 { margin-bottom: 15px; }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .table-total {
            width: 100%;
            margin-left: auto;
            max-width: 300px;
        }
        .signature {
            margin-top: 50px;
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            padding-top: 5px;
            margin-left: auto;
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .container {
                padding: 0;
            }
            .header {
                margin-top: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <img src="{{ $this->organization['logo'] }}" alt="Logo" class="logo">
                <div class="mt-3">
                    <div class="text-bold">{{ $this->organization['name'] }}</div>
                    <div>{{ $this->organization['address'] }}</div>
                    <div>Phone: {{ $this->organization['phone'] }}</div>
                    <div>Email: {{ $this->organization['email'] }}</div>
                </div>
            </div>
            <div class="document-info">
                <div class="document-title">GOODS RECEIPT NOTE</div>
                <div class="document-number">GRN #{{ $this->goodsReceipt->grn_number }}</div>
                <div>Date: {{ \Carbon\Carbon::parse($this->goodsReceipt->receipt_date)->format('M d, Y') }}</div>
                <div>Status: <span class="text-bold">{{ strtoupper($this->goodsReceipt->status) }}</span></div>
            </div>
        </div>

        <!-- Vendor and PO Info -->
        <div class="row mb-3">
            <div class="col col-6">
                <div class="section">
                    <div class="section-title">Vendor Information</div>
                    <div class="text-bold">{{ $this->goodsReceipt->purchaseOrder->vendor->name ?? 'N/A' }}</div>
                    <div>{{ $this->goodsReceipt->purchaseOrder->vendor->address ?? '' }}</div>
                    <div>Phone: {{ $this->goodsReceipt->purchaseOrder->vendor->phone ?? '' }}</div>
                    <div>Email: {{ $this->goodsReceipt->purchaseOrder->vendor->email ?? '' }}</div>
                </div>
            </div>
            <div class="col col-6">
                <div class="section">
                    <div class="section-title">Reference</div>
                    <div>PO #: {{ $this->goodsReceipt->purchaseOrder->po_number ?? 'N/A' }}</div>
                    <div>PO Date: {{ $this->goodsReceipt->purchaseOrder->order_date ? \Carbon\Carbon::parse($this->goodsReceipt->purchaseOrder->order_date)->format('M d, Y') : 'N/A' }}</div>
                    <div>Received By: {{ $this->goodsReceipt->received_by }}</div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="section">
            <div class="section-title">Received Items</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Description</th>
                        <th class="text-right">Ordered</th>
                        <th class="text-right">Received</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                        <th>Batch #</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->seedling->name ?? 'N/A' }}</td>
                            <td>{{ $item->description ?? '' }}</td>
                            <td class="text-right">{{ number_format($item->quantity_ordered, 2) }}</td>
                            <td class="text-right">{{ number_format($item->quantity_received, 2) }}</td>
                            <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->total, 2) }}</td>
                            <td>{{ $item->batch_number }}</td>
                            <td>{{ ucfirst($item->condition) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-right text-bold">Total:</td>
                        <td class="text-right text-bold">{{ number_format($this->items->sum('total'), 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Notes -->
        @if($this->goodsReceipt->notes)
            <div class="section">
                <div class="section-title">Notes</div>
                <div>{{ $this->goodsReceipt->notes }}</div>
            </div>
        @endif

        <!-- Signatures -->
        <div class="row" style="margin-top: 50px;">
            <div class="col col-4">
                <div class="signature">
                    Received By
                </div>
            </div>
            <div class="col col-4">
                <div class="signature">
                    Inspected By
                </div>
            </div>
            <div class="col col-4">
                <div class="signature">
                    Authorized By
                </div>
            </div>
        </div>

        <!-- Print Button (visible only on screen) -->
        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()" style="padding: 8px 16px; background: #4f46e5; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Print GRN
            </button>
        </div>
    </div>

    <script>
        // Auto-print when the page loads (optional)
        // window.onload = function() { window.print(); };
    </script>
</body>
</html>
