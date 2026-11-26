<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_unit', 'nama_unit', 'tipe_unit', 'parent_id',
        'provinsi', 'kota_kab', 'alamat', 'telepon', 'email',
        'logo', 'kapasitas_wbp', 'status',
    ];

    /**
     * URL publik logo unit (untuk halaman web).
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }
        return asset('storage/' . ltrim($this->logo, '/'));
    }

    /**
     * Path absolut file logo di storage (untuk PDF/DomPDF).
     */
    public function getLogoPathAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }
        $path = storage_path('app/public/' . ltrim($this->logo, '/'));
        return file_exists($path) ? $path : null;
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // Relationships
    public function parent() { return $this->belongsTo(Unit::class, 'parent_id'); }
    public function children() { return $this->hasMany(Unit::class, 'parent_id'); }
    public function users() { return $this->hasMany(User::class); }
    public function wbps() { return $this->hasMany(Wbp::class); }
    public function blocks() { return $this->hasMany(Block::class); }
    public function foodPrograms() { return $this->hasMany(FoodProgram::class); }
    public function landPlots() { return $this->hasMany(LandPlot::class); }
    public function livestockBarns() { return $this->hasMany(LivestockBarn::class); }
    public function fishPonds() { return $this->hasMany(FishPond::class); }
    public function inventoryItems() { return $this->hasMany(InventoryItem::class); }
    public function dailyReports() { return $this->hasMany(DailyReport::class); }
}
