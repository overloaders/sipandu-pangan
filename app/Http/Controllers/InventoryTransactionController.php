<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\InventoryItem;
use App\Traits\UnitScope;
use Illuminate\Http\Request;
use App\Http\Requests\Web\StoreInventoryTransactionRequest;

class InventoryTransactionController extends Controller
{
    use UnitScope;

    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'refUnit', 'creator']);
        $user = auth()->user();

        // inventory_transactions has no unit_id column — scope via the item's unit
        if ($user && !$user->hasRole('super-admin') && $user->unit_id) {
            $query->whereHas('item', fn($q) => $q->where('unit_id', $user->unit_id));
        } elseif ($user && !$user->hasRole('super-admin')) {
            $query->whereRaw('1 = 0');
        }

        if ($request->item_id) $query->where('item_id', $request->item_id);
        if ($request->tipe_transaksi) $query->where('tipe_transaksi', $request->tipe_transaksi);
        if ($request->from_date) $query->where('tanggal_transaksi', '>=', $request->from_date);
        if ($request->to_date) $query->where('tanggal_transaksi', '<=', $request->to_date);
        $transactions = $query->orderByDesc('tanggal_transaksi')->paginate(15)->withQueryString();
        $itemsQuery = InventoryItem::query()->orderBy('nama_item');
        if ($user && !$user->hasRole('super-admin') && $user->unit_id) {
            $itemsQuery->where('unit_id', $user->unit_id);
        }
        $items = $itemsQuery->get();
        return view('inventory.transactions', compact('transactions', 'items'));
    }

    public function store(StoreInventoryTransactionRequest $request)
    {
        $data = $request->validated();
        $item = InventoryItem::findOrFail($data['item_id']);
        $this->authorizeItem($item);
        $data['harga_satuan'] = $item->harga_beli_rata;
        $data['total_nilai'] = $data['jumlah'] * $data['harga_satuan'];
        $data['created_by_user_id'] = auth()->id();
        $data['kode_transaksi'] = 'TRX-' . now()->format('Ymd-His');
        $this->applyStockDelta($item, $data['tipe_transaksi'], $data['jumlah']);
        InventoryTransaction::create($data);
        return redirect()->route('inventory-transactions.index')->with('success', 'Transaksi berhasil dicatat');
    }

    public function update(StoreInventoryTransactionRequest $request, InventoryTransaction $transaction)
    {
        $this->authorizeItem($transaction->item);
        $data = $request->validated();
        $newItem = InventoryItem::findOrFail($data['item_id']);
        $this->authorizeItem($newItem);

        // Reverse old effect, then apply new effect
        $this->applyStockDelta($transaction->item, $transaction->tipe_transaksi, -$transaction->jumlah);
        $data['harga_satuan'] = $newItem->harga_beli_rata;
        $data['total_nilai'] = $data['jumlah'] * $data['harga_satuan'];
        $this->applyStockDelta($newItem, $data['tipe_transaksi'], $data['jumlah']);

        $transaction->update($data);
        return redirect()->route('inventory-transactions.index')->with('success', 'Transaksi berhasil diupdate');
    }

    public function destroy(InventoryTransaction $transaction)
    {
        $this->authorizeItem($transaction->item);
        $this->applyStockDelta($transaction->item, $transaction->tipe_transaksi, -$transaction->jumlah);
        $transaction->delete();
        return back()->with('success', 'Transaksi berhasil dihapus');
    }

    /**
     * Non-super-admin may only touch items from their own unit.
     */
    private function authorizeItem(InventoryItem $item): void
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('super-admin') && $user->unit_id && $item->unit_id != $user->unit_id) {
            abort(403, 'Anda tidak memiliki akses ke item ini.');
        }
    }

    /**
     * Apply stock delta: 'masuk' increases stock, every other type decreases it.
     */
    private function applyStockDelta(InventoryItem $item, string $tipe, float $jumlah): void
    {
        if ($tipe === 'masuk') {
            $item->increment('stok_saat_ini', $jumlah);
        } else {
            $item->decrement('stok_saat_ini', $jumlah);
        }
    }
}
