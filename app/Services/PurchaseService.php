<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PurchaseService
{
    public function list(User $user): Collection
    {
        return Purchase::query()
            ->with(['user', 'cylinderSize', 'purchaseKg', 'deliveryTime'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function find(int $id): Purchase
    {
        return Purchase::with(['user', 'cylinderSize', 'purchaseKg', 'deliveryTime'])
            ->findOrFail($id);
    }

    public function create(User $user, array $data): Purchase
    {
        $data['user_id'] = $user->id;
        $data['invoice_no'] = $this->generateInvoiceNumber();

        return Purchase::create($data)->load(['user', 'cylinderSize', 'purchaseKg', 'deliveryTime']);
    }

    public function update(Purchase $purchase, array $data): Purchase
    {
        $purchase->fill($data);
        $purchase->save();

        return $purchase->load(['user', 'cylinderSize', 'purchaseKg', 'deliveryTime']);
    }

    public function delete(Purchase $purchase): void
    {
        $purchase->delete();
    }

    protected function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd');
        $random = strtoupper(Str::random(6));

        return $prefix . '-' . $random;
    }
}
