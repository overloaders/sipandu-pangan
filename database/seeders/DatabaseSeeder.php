<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Unit;
use App\Models\Wbp;
use App\Models\FoodProgram;
use App\Models\LandPlot;
use App\Models\CropType;
use App\Models\LivestockBarn;
use App\Models\LivestockType;
use App\Models\FishPond;
use App\Models\Block;
use App\Models\Room;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\FeedSupply;
use App\Models\FinancialCategory;
use App\Models\AnalyticsIndicator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ──
        $roles = Role::insert([
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Akses penuh ke semua modul', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administrator', 'slug' => 'admin', 'description' => 'Kelola data dan pengguna', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supervisor', 'slug' => 'supervisor', 'description' => 'Supervisi kegiatan lapangan', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Operator', 'slug' => 'operator', 'description' => 'Input data harian', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Keuangan', 'slug' => 'keuangan', 'description' => 'Kelola keuangan dan anggaran', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Units ──
        $pusat = Unit::create(['kode_unit' => 'PUSAT', 'nama_unit' => 'Ditjen Pemasyarakatan', 'tipe_unit' => 'pusat', 'status' => 'aktif']);
        $kanwil = Unit::create(['kode_unit' => 'KANWIL-JKT', 'nama_unit' => 'Kanwil DKI Jakarta', 'tipe_unit' => 'kanwil', 'parent_id' => $pusat->id, 'provinsi' => 'DKI Jakarta', 'status' => 'aktif']);
        $lapas = Unit::create(['kode_unit' => 'LPAS-01', 'nama_unit' => 'Lapas Narkotika Kelas I Jakarta', 'tipe_unit' => 'lapas', 'parent_id' => $kanwil->id, 'provinsi' => 'DKI Jakarta', 'kota_kab' => 'Jakarta Timur', 'kapasitas_wbp' => '2000', 'status' => 'aktif']);

        // ── Admin User ──
        $admin = User::create([
            'name' => 'Administrator SIPANDU PANGAN',
            'email' => 'admin@sipandupangan.id',
            'password' => Hash::make('Password123!'),
            'nip' => '198501012010011001',
            'unit_id' => $lapas->id,
            'is_active' => true,
        ]);
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $operatorRole = Role::where('slug', 'operator')->first();
        $admin->roles()->attach($superAdminRole);

        $operator = User::create([
            'name' => 'Operator Utama',
            'email' => 'operator@sipandupangan.id',
            'password' => Hash::make('Operator123!'),
            'unit_id' => $lapas->id,
            'is_active' => true,
        ]);
        $operator->roles()->attach($operatorRole);

        // ── Blocks & Rooms ──
        $blokA = Block::create(['kode_blok' => 'A', 'nama_blok' => 'Blok A - Umum', 'unit_id' => $lapas->id, 'jenis_blok' => 'umum', 'kapasitas' => 100, 'status' => 'aktif']);
        $blokB = Block::create(['kode_blok' => 'B', 'nama_blok' => 'Blok B - Khusus', 'unit_id' => $lapas->id, 'jenis_blok' => 'khusus', 'kapasitas' => 80, 'status' => 'aktif']);

        for ($i = 1; $i <= 5; $i++) {
            Room::create(['block_id' => $blokA->id, 'kode_kamar' => "A-{$i}", 'nama_kamar' => "Kamar A-{$i}", 'kapasitas' => 20, 'status' => 'tersedia']);
            Room::create(['block_id' => $blokB->id, 'kode_kamar' => "B-{$i}", 'nama_kamar' => "Kamar B-{$i}", 'kapasitas' => 16, 'status' => 'tersedia']);
        }

        // ── Crop Types ──
        $crops = [
            ['kode_tanaman' => 'PADI', 'nama_tanaman' => 'Padi', 'kategori' => 'padi', 'masa_tanam_hari' => 120, 'harga_jual_per_kg' => 7500],
            ['kode_tanaman' => 'CABAI', 'nama_tanaman' => 'Cabai Merah', 'kategori' => 'sayuran', 'masa_tanam_hari' => 90, 'harga_jual_per_kg' => 25000],
            ['kode_tanaman' => 'KANGKUNG', 'nama_tanaman' => 'Kangkung', 'kategori' => 'sayuran', 'masa_tanam_hari' => 30, 'harga_jual_per_kg' => 5000],
            ['kode_tanaman' => 'JAGUNG', 'nama_tanaman' => 'Jagung', 'kategori' => 'padi', 'masa_tanam_hari' => 100, 'harga_jual_per_kg' => 6000],
            ['kode_tanaman' => 'KACANG', 'nama_tanaman' => 'Kacang Panjang', 'kategori' => 'sayuran', 'masa_tanam_hari' => 60, 'harga_jual_per_kg' => 8000],
            ['kode_tanaman' => 'SINGKONG', 'nama_tanaman' => 'Singkong', 'kategori' => 'umbi', 'masa_tanam_hari' => 270, 'harga_jual_per_kg' => 4000],
            ['kode_tanaman' => 'TERONG', 'nama_tanaman' => 'Terong', 'kategori' => 'sayuran', 'masa_tanam_hari' => 75, 'harga_jual_per_kg' => 7000],
        ];
        foreach ($crops as $c) CropType::create($c);

        // ── Livestock Types ──
        $livestockTypes = [
            ['kode_jenis' => 'AYAM', 'nama_jenis' => 'Ayam Potong', 'kategori' => 'unggas', 'masa_pemeliharaan_hari' => 45],
            ['kode_jenis' => 'BEBEK', 'nama_jenis' => 'Bebek Petelur', 'kategori' => 'unggas', 'masa_pemeliharaan_hari' => 120],
            ['kode_jenis' => 'KAMBING', 'nama_jenis' => 'Kambing Etawa', 'kategori' => 'ruminansia', 'masa_pemeliharaan_hari' => 365],
            ['kode_jenis' => 'SAPI', 'nama_jenis' => 'Sapi Potong', 'kategori' => 'ruminansia', 'masa_pemeliharaan_hari' => 540],
            ['kode_jenis' => 'KELinci', 'nama_jenis' => 'Kelinci', 'kategori' => 'lainnya', 'masa_pemeliharaan_hari' => 120],
        ];
        foreach ($livestockTypes as $lt) LivestockType::create($lt);

        // ── Inventory Categories ──
        $invCategories = [
            ['kode_kategori' => 'PANGAN-BH', 'nama_kategori' => 'Bahan Pangan Basah', 'tipe' => 'pangan', 'satuan_standar' => 'kg'],
            ['kode_kategori' => 'PANGAN-KR', 'nama_kategori' => 'Bahan Pangan Kering', 'tipe' => 'pangan', 'satuan_standar' => 'kg'],
            ['kode_kategori' => 'BMBK', 'nama_kategori' => 'Bahan Bumbu & Masakan', 'tipe' => 'bahan_baku', 'satuan_standar' => 'kg'],
            ['kode_kategori' => 'PAKAN', 'nama_kategori' => 'Pakan Ternak', 'tipe' => 'non_pangan', 'satuan_standar' => 'kg'],
            ['kode_kategori' => 'OBT', 'nama_kategori' => 'Obat & Vitamin', 'tipe' => 'obat', 'satuan_standar' => 'pcs'],
        ];
        foreach ($invCategories as $ic) InventoryCategory::create($ic);

        // ── Financial Categories ──
        $finIncome = FinancialCategory::create(['kode_kategori_keu' => 'Pemasukan', 'nama_kategori' => 'Pemasukan', 'tipe' => 'pemasukan', 'parent_type' => 'induk']);
        FinancialCategory::create(['kode_kategori_keu' => 'PJL-PNGN', 'nama_kategori' => 'Penjualan Pangan', 'tipe' => 'pemasukan', 'parent_type' => 'sub', 'parent_id' => $finIncome->id]);
        FinancialCategory::create(['kode_kategori_keu' => 'PNBP', 'nama_kategori' => 'PNBP', 'tipe' => 'pemasukan', 'parent_type' => 'sub', 'parent_id' => $finIncome->id]);

        $finExpense = FinancialCategory::create(['kode_kategori_keu' => 'Pengeluaran', 'nama_kategori' => 'Pengeluaran', 'tipe' => 'pengeluaran', 'parent_type' => 'induk']);
        FinancialCategory::create(['kode_kategori_keu' => 'BELI-BH', 'nama_kategori' => 'Pembelian Bahan', 'tipe' => 'pengeluaran', 'parent_type' => 'sub', 'parent_id' => $finExpense->id]);
        FinancialCategory::create(['kode_kategori_keu' => 'OPR', 'nama_kategori' => 'Biaya Operasional', 'tipe' => 'pengeluaran', 'parent_type' => 'sub', 'parent_id' => $finExpense->id]);
        FinancialCategory::create(['kode_kategori_keu' => 'GAJI', 'nama_kategori' => 'Honor Petugas', 'tipe' => 'pengeluaran', 'parent_type' => 'sub', 'parent_id' => $finExpense->id]);

        // ── Analytics Indicators ──
        $indicators = [
            ['kode_indikator' => 'PROD-PNGN', 'nama_indikator' => 'Produktivitas Pangan (kg/ha/bulan)', 'kategori' => 'produktivitas', 'satuan' => 'kg/ha/bulan'],
            ['kode_indikator' => 'EFF-PAKAN', 'nama_indikator' => 'Efisiensi Pakan (FCR)', 'kategori' => 'efisiensi', 'satuan' => 'ratio'],
            ['kode_indikator' => 'CONV-WBP', 'nama_indikator' => 'Kontribusi Pangan ke Konsumsi WBP (%)', 'kategori' => 'kualitas_pangan', 'satuan' => '%'],
            ['kode_indikator' => 'PDP-PROG', 'nama_indikator' => 'Total Pendapatan Program (Rp)', 'kategori' => 'keuangan', 'satuan' => 'Rp'],
            ['kode_indikator' => 'TRN-SELESAI', 'nama_indikator' => 'Jumlah WBP Lulus Pelatihan', 'kategori' => 'sdm', 'satuan' => 'orang'],
        ];
        foreach ($indicators as $ind) {
            AnalyticsIndicator::create(array_merge($ind, ['is_active' => true]));
        }

        // ── Call RealisticDataSeeder for bulk data ──
        $this->call(RealisticDataSeeder::class);

        echo "✅ Database seeded successfully!\n";
        echo "📧 Admin: admin@sipandupangan.id / Password123!\n";
        echo "📧 Operator: operator@sipandupangan.id / Operator123!\n";
    }
}
