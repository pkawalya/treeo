<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceipt;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    /**
     * Display the specified goods receipt.
     */
    public function show(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load([
            'purchaseOrder',
            'purchaseOrder.vendor',
            'items',
            'items.seedling',
            'items.purchaseOrderItem'
        ]);

        return view('goods-receipts.show', compact('goodsReceipt'));
    }

    /**
     * Print the specified goods receipt.
     */
    public function print(GoodsReceipt $goodsReceipt)
    {
        $goodsReceipt->load([
            'purchaseOrder',
            'purchaseOrder.vendor',
            'items',
            'items.seedling',
            'items.purchaseOrderItem'
        ]);

        $organization = [
            'name' => config('app.name', 'TreeO'),
            'address' => '123 Forest Drive, Kampala, Uganda',
            'phone' => '+256 700 000000',
            'email' => 'info@treeo.org',
            'logo' => asset('images/logo.png'),
        ];

        return view('goods-receipts.print', compact('goodsReceipt', 'organization'));
    }
}
