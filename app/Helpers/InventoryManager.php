<?php

namespace App\Helpers;

use App\Models\Inventory;

class InventoryManager
{
    /**
     * $type = 'in' for purchase (CashIn),
     * $type = 'out' for sale (CashOut).
     */
    public static function updateInventory(string $currency, float $quantity, float $rate, string $type): void
    {
        $inv = Inventory::firstOrNew(['currency' => $currency]);

        $oldQty = $inv->total_quantity ?? 0;
        $oldRate = $inv->average_rate ?? 0;
        $oldValue = $oldQty * $oldRate;

        if ($type === 'in') {
            // New total quantity & value
            $newQty = $oldQty + $quantity;
            $newValue = $oldValue + ($quantity * $rate);
        } else {
            // Sale: reduce quantity/value
            $newQty = max(0, $oldQty - $quantity);
            $newValue = max(0, $oldValue - ($quantity * $rate));
        }

        $inv->total_quantity = $newQty;
        // avoid division by zero
        $inv->average_rate = $newQty > 0 ? ($newValue / $newQty) : 0;
        $inv->average_price = $newQty * $inv->average_rate;
        $inv->save();
    }
}
