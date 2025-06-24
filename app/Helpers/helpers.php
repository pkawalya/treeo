<?php

use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;

if (!function_exists('goods_receipt_print_url')) {
    /**
     * Generate a print URL for a goods receipt
     *
     * @param GoodsReceipt $goodsReceipt
     * @return string
     */
    function goods_receipt_print_url(GoodsReceipt $goodsReceipt): string
    {
        return route('goods-receipts.print', $goodsReceipt);
    }
}

if (!function_exists('purchase_order_print_url')) {
    /**
     * Generate a print URL for a purchase order
     * 
     * @param PurchaseOrder $purchaseOrder
     * @return string
     */
    function purchase_order_print_url(PurchaseOrder $purchaseOrder): string
    {
        return route('purchase-orders.print', $purchaseOrder);
    }
}
