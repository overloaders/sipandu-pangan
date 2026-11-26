<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RealisticDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🗑️ Clearing existing data...');
        $tables = ['feed_logs', 'feed_supplies', 'stock_opname_items', 'stock_opnames', 'distribution_items', 'distributions', 'fish_harvests', 'water_quality_records', 'fish_stock', 'farming_activities', 'harvests', 'training_participants', 'compliance_details', 'analytics_targets', 'livestock_health_records', 'livestock_inventory', 'processing_batches', 'nutrition_plans', 'training_programs', 'compliance_records', 'financial_transactions', 'daily_reports', 'inventory_items', 'fish_ponds', 'livestock_barns', 'land_plots', 'farming_plans', 'wbp'];
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $t) {
            try { DB::table($t)->truncate(); } catch (\Exception $e) {}
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        $this->command->info('🌱 Seeding realistic sample data...');

        $userId = 1;
        $unitIds = DB::table('units')->pluck('id')->toArray();
        $blockIds = DB::table('blocks')->pluck('id')->toArray();
        $roomIds = DB::table('rooms')->pluck('id')->toArray();
        $cropTypeIds = DB::table('crop_types')->pluck('id')->toArray();
        $livestockTypeIds = DB::table('livestock_types')->pluck('id')->toArray();
        $invCategoryIds = DB::table('inventory_categories')->pluck('id')->toArray();
        $finCategoryIds = DB::table('financial_categories')->pluck('id')->toArray();

        $foodProgramIds = $this->seedFoodPrograms($unitIds, $userId);
        $wbpIds = $this->seedWBP($unitIds, $blockIds, $roomIds);
        $landPlotIds = $this->seedLandPlots($unitIds);
        $barnIds = $this->seedLivestockBarns($unitIds);
        $pondIds = $this->seedFishPonds($unitIds);
        $invItemIds = $this->seedInventoryItems($unitIds, $invCategoryIds);
        $feedIds = $this->seedFeedSupplies();

        $this->seedFarmingPlans($landPlotIds, $cropTypeIds, $wbpIds, $foodProgramIds);
        $this->seedLivestockInventory($barnIds, $livestockTypeIds);
        $this->seedFishStock($pondIds);
        $this->seedWaterQuality($pondIds, $wbpIds);
        $this->seedDailyReports($unitIds, $userId);
        $this->seedFinancialTransactions($unitIds, $finCategoryIds);
        $this->seedDistributions($unitIds, $invItemIds, $userId);
        $this->seedNutritionPlans($unitIds);
        $this->seedProcessingBatches($unitIds, $wbpIds);
        $this->seedTrainingPrograms($unitIds, $wbpIds);
        $this->seedComplianceRecords($unitIds, $userId);
        $this->seedAnalyticsTargets();
        $this->seedStockOpnames($unitIds, $wbpIds, $invItemIds);
        $this->seedFeedLogs($barnIds, $feedIds, $wbpIds);

        $this->command->info('✅ All realistic data seeded successfully!');
    }

    private function seedFoodPrograms(array $unitIds, int $userId): array
    {
        $this->command->info('  🌾 Seeding 10 food programs...');
        $ids = [];
        $programs = [
            ['PGR-001', 'Program Ketahanan Pangan Lapas', 'pertanian', 'Program utama ketahanan pangan berbasis padi'],
            ['PGR-002', 'Program Sayuran Organik', 'pertanian', 'Program budidaya sayuran organik'],
            ['PGR-003', 'Program Hortikultura', 'pertanian', 'Program pengembangan hortikultura'],
            ['PGR-004', 'Program Palawija', 'pertanian', 'Program tanaman palawija'],
            ['PGR-005', 'Program Peternakan Ayam', 'peternakan', 'Program pengembangan peternakan'],
            ['PGR-006', 'Program Peternakan Kambing', 'peternakan', 'Program peternakan kambing etawa'],
            ['PGR-007', 'Program Perikanan Lele', 'perikanan', 'Program budidaya ikan lele'],
            ['PGR-008', 'Program Perikanan Nila', 'perikanan', 'Program budidaya ikan nila'],
            ['PGR-009', 'Program Pengolahan Pangan', 'pengolahan', 'Program pengolahan hasil pertanian'],
            ['PGR-010', 'Program Integrated Farming', 'integrated', 'Program pertanian terpadu'],
        ];
        foreach ($programs as $p) {
            $ids[] = DB::table('food_programs')->insertGetId([
                'kode_program' => $p[0],
                'nama_program' => $p[1],
                'deskripsi' => $p[3],
                'jenis_program' => $p[2],
                'status' => ['aktif', 'aktif', 'aktif', 'selesai', 'perencanaan'][rand(0, 4)],
                'tanggal_mulai' => Carbon::now()->subMonths(rand(1, 6))->toDateString(),
                'tanggal_rencana_selesai' => Carbon::now()->addMonths(rand(3, 12))->toDateString(),
                'unit_id' => $unitIds[array_rand($unitIds)],
                'created_by' => $userId,
                'anggaran' => rand(10000000, 200000000),
                'realisasi_anggaran' => rand(5000000, 200000000),
                'target_peserta' => rand(20, 200),
                'keterangan' => $p[3],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $ids;
    }

    private function seedWBP(array $unitIds, array $blockIds, array $roomIds): array
    {
        $this->command->info('  📋 Seeding 120 WBP records...');
        $ids = [];
        $namaDepan = ['Ahmad', 'Budi', 'Dedi', 'Eko', 'Fajar', 'Gunawan', 'Hadi', 'Irfan', 'Joko', 'Krisna', 'Lukman', 'Made', 'Nando', 'Omar', 'Pandu', 'Rizky', 'Surya', 'Tono', 'Ucok', 'Vino', 'Wahyu', 'Yusuf', 'Zaki', 'Adi', 'Bayu', 'Cahya', 'Dani', 'Edi', 'Fadil', 'Gilang', 'Hari', 'Imam', 'Joni', 'Kurniawan', 'Luthfi', 'Maulana', 'Nanda', 'Octa', 'Prasetyo', 'Rendra', 'Sandi', 'Taufik', 'Umam', 'Vikri', 'Wawan', 'Yoga', 'Zulfikar', 'Ari', 'Bagas', 'Cakra'];
        $namaBelakang = ['Pratama', 'Saputra', 'Setiawan', 'Wibowo', 'Santoso', 'Hidayat', 'Rahman', 'Kurniawan', 'Susanto', 'Purnomo', 'Utomo', 'Gunawan', 'Handoko', 'Salim', 'Maulana', 'Firmansyah', 'Aditya', 'Nugroho', 'Bachtiar', 'Fauzi'];
        $agama = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha'];
        $pendidikan = ['SD', 'SMP', 'SMA', 'SMK'];
        $pidana = ['pidana_umum', 'pidana_khusus', 'narkotika'];
        $statuses = ['aktif', 'aktif', 'aktif', 'aktif', 'aktif', 'aktif', 'bebas', 'pindah'];

        for ($i = 1; $i <= 120; $i++) {
            $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
            $tglMasuk = Carbon::now()->subMonths(rand(1, 60));
            $masaHukuman = rand(30, 3650);
            $status = $statuses[array_rand($statuses)];

            $ids[] = DB::table('wbp')->insertGetId([
                'nomor_induk' => 'WBP-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama_lengkap' => $nama,
                'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
                'tanggal_lahir' => Carbon::now()->subYears(rand(20, 55))->toDateString(),
                'tempat_lahir' => ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Palembang', 'Yogyakarta'][array_rand(['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Palembang', 'Yogyakarta'])],
                'nik' => rand(1000000000000000, 9999999999999999),
                'agama' => $agama[array_rand($agama)],
                'pendidikan_terakhir' => $pendidikan[array_rand($pendidikan)],
                'pekerjaan_sebelum' => ['Buruh', 'Tani', 'Pedagang', 'Supir', 'Karyawan Swasta', 'Tukang', 'Nelayan', 'Wiraswasta'][array_rand(['Buruh', 'Tani', 'Pedagang', 'Supir', 'Karyawan Swasta', 'Tukang', 'Nelayan', 'Wiraswasta'])],
                'alamat_asal' => 'Jl. ' . ['Merdeka', 'Sudirman', 'Thamrin', 'Gatot Subroto', 'Diponegoro'][rand(0, 4)] . ' No. ' . rand(1, 100),
                'kota_asal' => ['Jakarta', 'Surabaya', 'Bandung', 'Medan'][rand(0, 3)],
                'provinsi_asal' => ['DKI Jakarta', 'Jawa Timur', 'Jawa Barat', 'Sumatera Utara'][rand(0, 3)],
                'nomor_hp' => '08' . rand(1000000000, 9999999999),
                'status' => $status,
                'jenis_pidana' => $pidana[array_rand($pidana)],
                'kasus' => 'Pidana umum',
                'tanggal_masuk' => $tglMasuk->toDateString(),
                'masa_hukuman_hari' => $masaHukuman,
                'masa_hukuman_selesai' => $tglMasuk->copy()->addDays($masaHukuman)->toDateString(),
                'unit_id' => $unitIds[array_rand($unitIds)],
                'block_id' => $blockIds[array_rand($blockIds)],
                'room_id' => $roomIds[array_rand($roomIds)],
                'tamping' => 'T-0' . rand(1, 9),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $ids;
    }

    private function seedLandPlots(array $unitIds): array
    {
        $this->command->info('  🌾 Seeding 15 land plots...');
        $ids = [];
        
        for ($i = 1; $i <= 15; $i++) {
            $ids[] = DB::table('land_plots')->insertGetId([
                'kode_lahan' => 'LAH-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_lahan' => 'Lahan ' . ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'][$i % 8] . '-' . $i,
                'unit_id' => $unitIds[array_rand($unitIds)],
                'luas_m2' => rand(500, 5000),
                'jenis_tanah' => ['tanah_liat', 'tanah_berpasir', 'tanah_laterit', 'tanah_gambut', 'campuran'][rand(0, 4)],
                'sistem_irigasi' => ['tadah_hujan', 'irigasi_tetes', 'parit', 'pompa_air', 'sungai', 'sumur'][rand(0, 5)],
                'status' => ['tersedia', 'ditanami', 'istirahat', 'renovasi'][rand(0, 3)],
                'latitude' => -6.2 + (rand(-50, 50) / 1000),
                'longitude' => 106.8 + (rand(-50, 50) / 1000),
                'keterangan' => 'Lahan untuk program ketahanan pangan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $ids;
    }

    private function seedLivestockBarns(array $unitIds): array
    {
        $this->command->info('  🐄 Seeding 10 livestock barns...');
        $ids = [];
        $jenis = ['ayam', 'bebek', 'kambing', 'sapi', 'kelinci'];
        for ($i = 1; $i <= 10; $i++) {
            $kap = rand(50, 500);
            $ids[] = DB::table('livestock_barns')->insertGetId([
                'kode_kandang' => 'KDG-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_kandang' => 'Kandang ' . $jenis[array_rand($jenis)] . ' ' . $i,
                'unit_id' => $unitIds[array_rand($unitIds)],
                'jenis_ternak' => $jenis[array_rand($jenis)],
                'kapasitas' => $kap,
                'terisi' => rand(0, $kap),
                'luas_m2' => rand(20, 200),
                'tipe_kandang' => ['kandang_baterai', 'kandang_umbaran', 'kandang_koloni', 'semi_ekstensif'][rand(0, 3)],
                'status' => 'aktif',
                'keterangan' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $ids;
    }

    private function seedFishPonds(array $unitIds): array
    {
        $this->command->info('  🐟 Seeding 12 fish ponds...');
        $ids = [];
        for ($i = 1; $i <= 12; $i++) {
            $ids[] = DB::table('fish_ponds')->insertGetId([
                'kode_kolam' => 'KLM-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_kolam' => 'Kolam ' . ['Lele', 'Nila', 'Gurame', 'Bandeng', 'Patin'][rand(0, 4)] . ' ' . $i,
                'unit_id' => $unitIds[array_rand($unitIds)],
                'luas_m2' => rand(20, 200),
                'kedalaman_m' => rand(8, 20) / 10,
                'volume_m3' => rand(10, 100),
                'tipe_kolam' => ['tanah', 'beton', 'terpal', 'semen'][rand(0, 3)],
                'sumber_air' => ['sungai', 'sumur', 'hujan', 'pompa', 'PDAM'][rand(0, 4)],
                'status' => ['aktif', 'aktif', 'aktif', 'renovasi'][rand(0, 3)],
                'keterangan' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $ids;
    }

    private function seedInventoryItems(array $unitIds, array $catIds): array
    {
        $this->command->info('  📦 Seeding 40 inventory items...');
        $ids = [];
        $items = [
            ['Beras Premium', 1, 'kg', 500, 100],
            ['Beras Medium', 1, 'kg', 800, 150],
            ['Minyak Goreng', 1, 'liter', 300, 50],
            ['Gula Pasir', 1, 'kg', 200, 50],
            ['Telur Ayam', 1, 'kg', 150, 30],
            ['Tepung Terigu', 1, 'kg', 100, 30],
            ['Ikan Segar', 1, 'kg', 80, 20],
            ['Sayur Kangkung', 1, 'kg', 60, 10],
            ['Sayur Bayam', 1, 'kg', 50, 10],
            ['Cabai Merah', 1, 'kg', 40, 10],
            ['Pakan Ayam', 2, 'kg', 1000, 200],
            ['Pakan Lele', 2, 'kg', 500, 100],
            ['Pakan Nila', 2, 'kg', 400, 80],
            ['Vitamin Ayam', 3, 'botol', 50, 10],
            ['Obat Cacing', 3, 'sachet', 100, 20],
            ['Vaksin ND', 3, 'dosis', 200, 50],
            ['Handscoop', 4, 'pcs', 30, 10],
            ['Sarung Tangan', 4, 'pcs', 50, 20],
            ['Cangkul', 4, 'pcs', 25, 10],
            ['Sekop', 4, 'pcs', 15, 5],
            ['Pompa Air', 4, 'unit', 5, 2],
            ['Selang Irigasi', 4, 'meter', 500, 100],
            ['Bibit Padi', 5, 'kg', 300, 50],
            ['Bibit Jagung', 5, 'kg', 200, 30],
            ['Bibit Sayur', 5, 'sachet', 100, 20],
            ['Pupuk Urea', 5, 'kg', 400, 100],
            ['Pupuk NPK', 5, 'kg', 300, 80],
            ['Pupuk Kandang', 5, 'kg', 600, 150],
            ['Benih Ikan Lele', 5, 'ekor', 5000, 1000],
            ['Benih Ikan Nila', 5, 'ekor', 3000, 500],
            ['Terasi', 1, 'kg', 30, 5],
            ['Kecap Manis', 1, 'liter', 40, 10],
            ['Garam', 1, 'kg', 80, 20],
            ['Sabun Cuci', 3, 'pcs', 60, 15],
            ['Deterjen', 3, 'kg', 50, 10],
            ['Kertas HVS', 4, 'rim', 20, 5],
            ['Pulpen', 4, 'pcs', 50, 10],
            ['Buku Tulis', 4, 'pcs', 100, 20],
            ['Kain Goni', 4, 'pcs', 30, 10],
            ['Karung Beras', 4, 'pcs', 200, 50],
        ];
        foreach ($items as $idx => $item) {
            $stok = $item[3] + rand(-50, 100);
            $ids[] = DB::table('inventory_items')->insertGetId([
                'kode_item' => 'INV-' . str_pad($idx + 1, 4, '0', STR_PAD_LEFT),
                'nama_item' => $item[0],
                'category_id' => $catIds[array_rand($catIds)],
                'unit_id' => $unitIds[array_rand($unitIds)],
                'satuan' => $item[2],
                'stok_saat_ini' => max($stok, 0),
                'stok_minimum' => $item[4],
                'stok_maksimum' => $item[3] * 2,
                'harga_beli_rata' => rand(5000, 50000),
                'harga_jual' => rand(8000, 80000),
                'tipe_pangan' => ['basah', 'kering', 'olahan', 'bumbu', 'bahan_baku'][rand(0, 4)],
                'asal' => ['produksi_sendiri', 'pembelian', 'hibah', 'konversi'][rand(0, 3)],
                'status' => 'tersedia',
                'lokasi_simpan' => 'Gudang Utama',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return $ids;
    }

    private function seedFeedSupplies(): array
    {
        $this->command->info('  🌾 Seeding 10 feed supplies...');
        $ids = [];
        $feeds = [
            ['FP-001', 'Paging layer mash', 'konsentrat', 'kg', 2000, 200, 12000],
            ['FP-002', 'Pakan Pelet Lele', 'konsentrat', 'kg', 1500, 150, 15000],
            ['FP-003', 'Pakan Pelet Nila', 'konsentrat', 'kg', 1200, 100, 14000],
            ['FP-004', 'Konsentrat Sapi', 'konsentrat', 'kg', 800, 100, 8000],
            ['FP-005', 'Hijauan Pakan', 'hijauan', 'kg', 5000, 500, 2000],
            ['FP-006', 'Pakan Ayam Starter', 'konsentrat', 'kg', 1000, 100, 13000],
            ['FP-007', 'Pakan Ayam Finisher', 'konsentrat', 'kg', 1500, 150, 14000],
            ['FP-008', 'Dedak Padi', 'kasar', 'kg', 2000, 200, 4000],
            ['FP-009', 'Bungkil Kedelai', 'kasar', 'kg', 800, 100, 9000],
            ['FP-010', 'Mineral Premix', 'premix', 'kg', 100, 20, 35000],
        ];
        foreach ($feeds as $f) {
            $ids[] = DB::table('feed_supplies')->insertGetId([
                'kode_pakan' => $f[0], 'nama_pakan' => $f[1], 'jenis_pakan' => $f[2],
                'satuan' => $f[3], 'stok' => $f[4], 'stok_minimum' => $f[5],
                'harga_per_satuan' => $f[6], 'supplier' => 'PT Pakan Nusantara',
                'tanggal_kadaluarsa' => Carbon::now()->addMonths(rand(3, 12))->toDateString(),
                'status' => 'tersedia', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        return $ids;
    }

    private function seedFarmingPlans(array $landIds, array $cropIds, array $wbpIds, array $foodProgramIds = []): void
    {
        $this->command->info('  🌾 Seeding 50 farming plans with activities & harvests...');
        $statuses = ['direncanakan', 'proses_tanam', 'tumbuh', 'siap_panen', 'panen', 'selesai'];
        $kegiatan = ['Pengolahan Tanah', 'Penanaman', 'Pemupukan', 'Penyiraman', 'Penyiangan', 'Pengendalian Hama', 'Pemeliharaan'];

        for ($i = 1; $i <= 50; $i++) {
            $tglTanam = Carbon::now()->subDays(rand(0, 180));
            $cropId = $cropIds[array_rand($cropIds)];
            $status = $statuses[array_rand($statuses)];
            $planId = DB::table('farming_plans')->insertGetId([
                'kode_rencana' => 'RP-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'food_program_id' => !empty($foodProgramIds) ? $foodProgramIds[array_rand($foodProgramIds)] : 1,
                'land_plot_id' => $landIds[array_rand($landIds)],
                'crop_type_id' => $cropId,
                'jumlah_tanaman' => rand(100, 5000),
                'tanggal_tanam_rencana' => $tglTanam->toDateString(),
                'tanggal_tanam_aktual' => $status !== 'perencanaan' ? $tglTanam->addDays(rand(0, 5))->toDateString() : null,
                'luas_tanam_m2' => rand(100, 2000),
                'metode_budi_daya' => ['konvensional', 'organik', 'hidroponik', 'aeroponik', 'mixed'][rand(0, 4)],
                'status' => $status,
                'estimasi_hasil_kg' => rand(50, 2000),
                'estimasi_pendapatan' => rand(500000, 20000000),
                'created_at' => now(), 'updated_at' => now(),
            ]);

            // Add 2-6 activities per plan
            $numActivities = rand(2, 6);
            for ($j = 0; $j < $numActivities; $j++) {
                DB::table('farming_activities')->insert([
                    'farming_plan_id' => $planId,
                    'jenis_kegiatan' => ['pengolahan_tanah', 'penanaman', 'pemupukan', 'penyiraman', 'penyiangan', 'pengendalian_hama', 'lainnya'][rand(0, 6)],
                    'tanggal_kegiatan' => $tglTanam->copy()->addDays(rand(0, 60))->toDateString(),
                    'pic_wbp_id' => $wbpIds[array_rand($wbpIds)],
                    'biaya' => rand(50000, 500000),
                    'keterangan' => 'Kegiatan rutin',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            // Add harvest for completed/selesai plans
            if ($status === 'selesai' || ($status === 'siap_panen' && rand(0, 1))) {
                $berat = rand(50, 2000);
                $harga = rand(3000, 15000);
                DB::table('harvests')->insert([
                    'kode_panen' => 'PAN-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'farming_plan_id' => $planId,
                    'tanggal_panen' => $tglTanam->copy()->addDays(rand(60, 120))->toDateString(),
                    'total_berat_kg' => $berat,
                    'total_pendapatan' => $berat * $harga,
                    'kualitas' => ['A', 'B', 'C', 'D'][rand(0, 3)],
                    'harga_jual_per_kg' => $harga,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedLivestockInventory(array $barnIds, array $typeIds): void
    {
        $this->command->info('  🐄 Seeding 35 livestock inventory records...');
        for ($i = 1; $i <= 35; $i++) {
            $jumlah = rand(5, 200);
            DB::table('livestock_inventory')->insert([
                'kode_ternak' => 'TRN-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'barn_id' => $barnIds[array_rand($barnIds)],
                'livestock_type_id' => $typeIds[array_rand($typeIds)],
                'jumlah' => $jumlah,
                'berat_rata_kg' => rand(5, 150) / 10,
                'tanggal_masuk' => Carbon::now()->subDays(rand(1, 365))->toDateString(),
                'asal' => ['pembelian', 'breeding', 'hibah', 'konversi'][rand(0, 3)],
                'harga_pokok' => rand(50000, 500000),
                'status' => ['hidup', 'hidup', 'hidup', 'dipanen', 'mati'][rand(0, 4)],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedFishStock(array $pondIds): void
    {
        $this->command->info('  🐟 Seeding 25 fish stock records...');
        $ikan = ['Lele', 'Nila', 'Gurame', 'Bandeng', 'Patin', 'Mas', 'Bawal'];
        for ($i = 1; $i <= 25; $i++) {
            $jumlah = rand(50, 5000);
            DB::table('fish_stock')->insert([
                'kode_stok_ikan' => 'SIK-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'pond_id' => $pondIds[array_rand($pondIds)],
                'jenis_ikan' => $ikan[array_rand($ikan)],
                'kategori' => ['benih', 'pembesaran', 'siap_panen'][rand(0, 2)],
                'jumlah' => $jumlah,
                'ukuran_rata_cm' => rand(5, 30),
                'berat_rata_gram' => rand(10, 800),
                'tanggal_tebur' => Carbon::now()->subDays(rand(1, 180))->toDateString(),
                'harga_benih' => rand(500, 5000),
                'total_biaya_pakan' => rand(500000, 5000000),
                'status' => ['hidup', 'hidup', 'hidup', 'dipanen'][rand(0, 3)],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedWaterQuality(array $pondIds, array $wbpIds): void
    {
        $this->command->info('  💧 Seeding 60 water quality records...');
        for ($i = 1; $i <= 60; $i++) {
            $kondisi = ['baik', 'baik', 'baik', 'cukup', 'buruk'][rand(0, 4)];
            DB::table('water_quality_records')->insert([
                'pond_id' => $pondIds[array_rand($pondIds)],
                'tanggal_pemeriksaan' => Carbon::now()->subDays(rand(0, 90))->toDateString(),
                'jam_pemeriksaan' => sprintf('%02d:%02d', rand(6, 17), rand(0, 59)),
                'suhu_celsius' => rand(240, 320) / 10,
                'ph' => rand(55, 90) / 10,
                'dissolved_oxygen_mgl' => rand(20, 80) / 10,
                'amonia_mgl' => rand(0, 80) / 100,
                'nitrit_mgl' => rand(0, 30) / 100,
                'nitrat_mgl' => rand(0, 50) / 10,
                'kekeruhan_ntu' => rand(0, 50),
                'salinitas_ppt' => rand(0, 30),
                'kondisi_air' => in_array($kondisi, ['baik', 'kurang_baik', 'buruk']) ? $kondisi : 'baik',
                'tindakan_perlukan' => $kondisi === 'baik' ? null : 'Lakukan penggantian air',
                'pemeriksa_id' => $wbpIds[array_rand($wbpIds)],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedDailyReports(array $unitIds, int $userId = 1): void
    {
        $this->command->info('  📝 Seeding 30 daily reports...');
        for ($i = 1; $i <= 30; $i++) {
            $tgl = Carbon::now()->subDays($i);
            DB::table('daily_reports')->insert([
                'kode_laporan' => 'LRH-' . $tgl->format('Ymd') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'unit_id' => $unitIds[array_rand($unitIds)],
                'tanggal_laporan' => $tgl->toDateString(),
                'dilaporkan_oleh' => $userId,
                'status' => rand(0, 3) > 0 ? 'dikonfirmasi' : 'draft',
                'total_wbp_aktif' => rand(80, 120),
                'total_wbp_baru_masuk' => rand(0, 3),
                'total_wbp_keluar' => rand(0, 2),
                'total_panen_hari_ini_kg' => rand(0, 200),
                'total_pakan_hari_ini_kg' => rand(10, 100),
                'total_pendapatan_hari_ini' => rand(0, 5000000),
                'total_pengeluaran_hari_ini' => rand(0, 2000000),
                'ringkasan_kegiatan' => 'Aktivitas rutin pertanian dan peternakan berjalan lancar. Panen sayuran kangkung dan bayam.',
                'catatan_khusus' => null,
                'masalah_ditemukan' => null,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedFinancialTransactions(array $unitIds, array $catIds): void
    {
        $this->command->info('  💰 Seeding 80 financial transactions...');
        for ($i = 1; $i <= 80; $i++) {
            $tipe = rand(0, 3) > 1 ? 'pemasukan' : 'pengeluaran';
            $tgl = Carbon::now()->subDays(rand(0, 180));
            DB::table('financial_transactions')->insert([
                'kode_transaksi_keu' => 'KEU-' . $tgl->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'tipe' => $tipe,
                'category_id' => $catIds[array_rand($catIds)],
                'jumlah' => rand(50000, 10000000),
                'deskripsi' => $tipe === 'pemasukan' ? 'Penjualan hasil panen' : 'Pembelian pupuk',
                'sumber_dana' => ['anggaran', 'penjualan_pangan', 'pnbp', 'hibah', 'sumbangan', 'lainnya'][rand(0, 5)],
                'metode_pembayaran' => ['tunai', 'transfer', 'qris', 'lainnya'][rand(0, 3)],
                'unit_id' => $unitIds[array_rand($unitIds)],
                'status' => ['disetujui', 'disetujui', 'disetujui', 'draft'][rand(0, 3)],
                'tanggal_transaksi' => $tgl->toDateString(),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedDistributions(array $unitIds, array $itemIds, int $userId): void
    {
        $this->command->info('  🚚 Seeding 25 distributions...');
        $tipes = ['konsumsi_wbp', 'penjualan_lokal', 'donasi', 'penjualan_eksternal'];
        for ($i = 1; $i <= 25; $i++) {
            $tgl = Carbon::now()->subDays(rand(0, 90));
            $distId = DB::table('distributions')->insertGetId([
                'kode_distribusi' => 'DIS-' . $tgl->format('Ymd') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'tipe_distribusi' => $tipes[array_rand($tipes)],
                'unit_id' => $unitIds[array_rand($unitIds)],
                'tanggal_distribusi' => $tgl->toDateString(),
                'total_nilai' => rand(100000, 5000000),
                'jumlah_penerima' => rand(5, 100),
                'penerima_nama' => ['Masyarakat Desa A', 'Panti Asuhan XYZ', 'Koperasi Unit Layanan', 'Pedagang Pasar'][rand(0, 3)],
                'penerima_kontak' => '08' . rand(1000000000, 9999999999),
                'status' => ['diterima', 'dikirim', 'draft'][rand(0, 2)],
                'created_by_user_id' => $userId,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            // Add 1-3 items per distribution
            $numItems = rand(1, 3);
            for ($j = 0; $j < $numItems; $j++) {
                $qty = rand(5, 100);
                $harga = rand(5000, 50000);
                DB::table('distribution_items')->insert([
                    'distribution_id' => $distId,
                    'item_id' => $itemIds[array_rand($itemIds)],
                    'jumlah' => $qty,
                    'harga_satuan' => $harga,
                    'subtotal' => $qty * $harga,
                    'satuan' => 'kg',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedNutritionPlans(array $unitIds): void
    {
        $this->command->info('  🍽️ Seeding 20 nutrition plans...');
        $menus = ['Nasi Ayam Bakar', 'Nasi Ikan Goreng', 'Nasi Sayur Sop', 'Bubur Ayam', 'Nasi Rendang', 'Nasi Tempe Tahu', 'Nasi Capcay', 'Mie Ayam', 'Nasi Pecel', 'Gado-gado'];
        $waktus = ['sarapan', 'makan_siang', 'makan_sore', 'snack_pagi', 'snack_sore'];
        for ($i = 1; $i <= 20; $i++) {
            DB::table('nutrition_plans')->insert([
                'kode_rencana_gizi' => 'GZI-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama_menu' => $menus[array_rand($menus)],
                'unit_id' => $unitIds[array_rand($unitIds)],
                'waktu_makan' => $waktus[array_rand($waktus)],
                'berlaku_mulai' => Carbon::now()->subDays(rand(0, 30))->toDateString(),
                'berlaku_selesai' => Carbon::now()->addDays(rand(30, 90))->toDateString(),
                'kalori_target_kcal' => rand(2000, 3500),
                'protein_target_gr' => rand(50, 100),
                'karbohidrat_target_gr' => rand(200, 400),
                'lemak_target_gr' => rand(40, 80),
                'serat_target_gr' => rand(15, 35),
                'tipe_porsi' => ['standar', 'diet_khusus', 'vegan', 'bayi', 'lansia'][rand(0, 4)],
                'estimasi_biaya_per_porsi' => rand(8000, 25000),
                'sumber_bahan' => ['produksi_sendiri', 'campuran', 'pembelian'][rand(0, 2)],
                'deskripsi' => 'Menu bergizi seimbang untuk WBP',
                'status' => ['aktif', 'aktif', 'draft', 'nonaktif'][rand(0, 3)],
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedProcessingBatches(array $unitIds, array $wbpIds): void
    {
        $this->command->info('  🍳 Seeding 15 processing batches...');
        $produk = ['Tepung Beras', 'Keripik Singkong', 'Abon Sapi', 'Rendang Kemasan', 'Sambal Botol', 'Kecap Manis', 'Minyak Kelapa', 'Dodol Durian', 'Kerupuk Udang', 'Mie Basah'];
        $proses = ['Penggilingan', 'Pengeringan', 'Pengemasan', 'Fermentasi', 'Pemasakan', 'Pengolahan Basah'];
        for ($i = 1; $i <= 15; $i++) {
            $tgl = Carbon::now()->subDays(rand(0, 60));
            DB::table('processing_batches')->insert([
                'kode_batch' => 'BAT-' . $tgl->format('Ymd') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'nama_produk' => $produk[array_rand($produk)],
                'unit_id' => $unitIds[array_rand($unitIds)],
                'jenis_pengolahan' => ['pengeringan', 'penggilingan', 'pengepakan', 'fermentasi', 'pengolahan_umum'][rand(0, 4)],
                'tanggal_produksi' => $tgl->toDateString(),
                'tanggal_kadaluarsa' => $tgl->copy()->addMonths(rand(1, 6))->toDateString(),
                'jumlah_produksi' => rand(10, 500),
                'satuan_output' => ['kg', 'pcs', 'liter', 'box'][rand(0, 3)],
                'biaya_produksi' => rand(500000, 10000000),
                'harga_jual_satuan' => rand(15000, 100000),
                'status' => ['selesai', 'selesai', 'proses', 'draft'][rand(0, 3)],
                'pic_wbp_id' => $wbpIds[array_rand($wbpIds)],
                'keterangan' => 'Produksi batch rutin',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedTrainingPrograms(array $unitIds, array $wbpIds): void
    {
        $this->command->info('  🎓 Seeding 12 training programs...');
        $nama = ['Pertanian Organik', 'Budidaya Lele', 'Pengolahan Pangan', 'Peternakan Ayam', 'Kewirausahaan', 'Pertanian Hidroponik', 'Teknisi Pertanian', 'Manajemen Keuangan', 'Pengolahan Hasil Laut', 'Komputer Dasar', 'Tata Boga', 'Kraftangan'];
        $kats = ['Pertanian', 'Perikanan', 'Peternakan', 'Kewirausahaan', 'Teknologi', 'Keterampilan'];
        for ($i = 1; $i <= 12; $i++) {
            $tglMulai = Carbon::now()->subDays(rand(0, 180));
            $status = ['selesai', 'berlangsung', 'perencanaan'][rand(0, 2)];
            $progId = DB::table('training_programs')->insertGetId([
                'kode_pelatihan' => 'PLT-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'nama_pelatihan' => $nama[array_rand($nama)],
                'deskripsi' => 'Program pelatihan keterampilan untuk WBP',
                'kategori' => ['pertanian', 'peternakan', 'perikanan', 'pengolahan_pangan', 'keterampilan_umum', 'literasi', 'keagamaan'][rand(0, 6)],
                'unit_id' => $unitIds[array_rand($unitIds)],
                'tanggal_mulai' => $tglMulai->toDateString(),
                'tanggal_selesai' => $tglMulai->copy()->addDays(rand(7, 60))->toDateString(),
                'jam_pelatihan' => rand(4, 40),
                'kapasitas_peserta' => rand(10, 30),
                'biaya' => rand(1000000, 10000000),
                'lokasi' => 'Aula Lapas',
                'tipe' => ['offline', 'online', 'hybrid'][rand(0, 2)],
                'status' => $status,
                'sertifikat_diberikan' => rand(0, 1),
                'materi_pokok' => 'Materi dasar dan praktik',
                'created_at' => now(), 'updated_at' => now(),
            ]);

            // Add participants
            $numPeserta = rand(5, 15);
            $shuffledWbp = $wbpIds;
            shuffle($shuffledWbp);
            for ($j = 0; $j < min($numPeserta, count($shuffledWbp)); $j++) {
                DB::table('training_participants')->insert([
                    'training_program_id' => $progId,
                    'wbp_id' => $shuffledWbp[$j],
                    'tanggal_daftar' => $tglMulai->copy()->subDays(rand(1, 14))->toDateString(),
                    'status' => ['terdaftar', 'hadir', 'selesai'][rand(0, 2)],
                    'nilai_akhir' => rand(60, 100) / 10,
                    'lulus' => rand(0, 1),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedComplianceRecords(array $unitIds, int $userId): void
    {
        $this->command->info('  ✅ Seeding 10 compliance records...');
        $tipes = ['Audit Internal', 'Inspeksi Rutin', 'Evaluasi Kinerja', 'Pemeriksaan Kebersihan', 'Audit Keamanan'];
        for ($i = 1; $i <= 10; $i++) {
            $tgl = Carbon::now()->subDays(rand(0, 180));
            $compId = DB::table('compliance_records')->insertGetId([
                'kode_komplians' => 'KMP-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'judul' => $tipes[array_rand($tipes)] . ' Periode ' . $tgl->format('M Y'),
                'tipe' => ['food_safety', 'sertifikasi_produk', 'standar_operasional', 'lingkungan', 'keamanan_kerja'][rand(0, 4)],
                'unit_id' => $unitIds[array_rand($unitIds)],
                'tanggal_pemeriksaan' => $tgl->toDateString(),
                'tanggal_berlaku_sampai' => $tgl->copy()->addMonths(6)->toDateString(),
                'hasil' => ['lulus', 'bersyarat', 'tidak_lulus'][rand(0, 2)],
                'nilai_skor' => rand(50, 100),
                'tingkat_risiko' => ['rendah', 'sedang', 'tinggi'][rand(0, 2)],
                'inspektur_user_id' => $userId,
                'status' => ['draft', 'aktif', 'perlu_perbaikan'][rand(0, 2)],
                'temuan' => 'Beberapa area perlu perbaikan kebersihan dan pengelolaan limbah.',
                'rekomendasi' => 'Lakukan perbaikan area dapur dan tempat penimbunan sampah.',
                'created_at' => now(), 'updated_at' => now(),
            ]);

            // Add details
            $items = ['Kebersihan Dapur', 'Pengelolaan Sampah', 'Kualitas Air', 'Penyimpanan Pangan', 'Kebersihan Kandang'];
            $numDetails = rand(2, 4);
            for ($j = 0; $j < $numDetails; $j++) {
                DB::table('compliance_details')->insert([
                    'compliance_id' => $compId,
                    'item_pemeriksaan' => $items[array_rand($items)],
                    'status_pemeriksaan' => ['memenuhi', 'tidak_memenuhi', 'sebagian', 'tidak_diterapkan'][rand(0, 3)],
                    'catatan' => 'Pemeriksaan rutin',
                    'bobot' => rand(10, 30),
                    'nilai' => rand(60, 100),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedAnalyticsTargets(): void
    {
        $this->command->info('  📊 Seeding analytics targets...');
        $indicators = DB::table('analytics_indicators')->pluck('id')->toArray();
        if (empty($indicators)) return;

        $units = DB::table('units')->pluck('id')->toArray();
        $used = [];
        for ($i = 1; $i <= 20; $i++) {
            $indId = $indicators[array_rand($indicators)];
            $unitId = $units[array_rand($units)];
            $bulan = rand(1, 12);
            $key = "{$indId}-{$unitId}-2026";
            if (in_array($key, $used)) continue;
            $used[] = $key;
            $target = rand(50, 500);
            $realisasi = rand(0, (int)($target * 1.3));
            DB::table('analytics_targets')->insert([
                'indicator_id' => $indId,
                'unit_id' => $unitId,
                'periode_tahun' => 2026,
                'periode_bulan' => $bulan,
                'target_nilai' => $target,
                'realisasi_nilai' => $realisasi,
                'persentase_capaian' => $target > 0 ? round(($realisasi / $target) * 100, 2) : 0,
                'status_capaian' => $realisasi >= $target ? 'melebihi_target' : ($realisasi >= $target * 0.5 ? 'sesuai_target' : 'di_bawah_target'),
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedStockOpnames(array $unitIds, array $wbpIds, array $itemIds): void
    {
        $this->command->info('  📋 Seeding 10 stock opnames...');
        for ($i = 1; $i <= 10; $i++) {
            $tgl = Carbon::now()->subDays(rand(0, 60));
            $opnameId = DB::table('stock_opnames')->insertGetId([
                'kode_opname' => 'SO-' . $tgl->format('Ymd') . '-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'unit_id' => $unitIds[array_rand($unitIds)],
                'tanggal_opname' => $tgl->toDateString(),
                'jenis_opname' => ['rutin', 'insidental', 'penutupan_buku'][rand(0, 2)],
                'status' => ['selesai', 'selesai', 'proses', 'draft'][rand(0, 3)],
                'petugas_wbp_id' => $wbpIds[array_rand($wbpIds)],
                'total_item_checked' => rand(10, 30),
                'item_selisih' => rand(0, 5),
                'total_selisih_nilai' => rand(0, 500000),
                'created_at' => now(), 'updated_at' => now(),
            ]);

            // Add items
            $numItems = rand(5, 15);
            $shuffledItems = $itemIds;
            shuffle($shuffledItems);
            for ($j = 0; $j < min($numItems, count($shuffledItems)); $j++) {
                $stokSistem = rand(10, 500);
                $selisih = rand(-10, 10);
                DB::table('stock_opname_items')->insert([
                    'opname_id' => $opnameId,
                    'item_id' => $shuffledItems[$j],
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokSistem + $selisih,
                    'selisih' => $selisih,
                    'nilai_selisih' => abs($selisih) * rand(5000, 50000),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedFeedLogs(array $barnIds, array $feedIds, array $wbpIds): void
    {
        $this->command->info('  🌾 Seeding 40 feed logs...');
        for ($i = 1; $i <= 40; $i++) {
            DB::table('feed_logs')->insert([
                'barn_id' => $barnIds[array_rand($barnIds)],
                'feed_supply_id' => $feedIds[array_rand($feedIds)],
                'wbp_id' => $wbpIds[array_rand($wbpIds)],
                'tanggal_pemberian' => Carbon::now()->subDays(rand(0, 60))->toDateString(),
                'waktu_pemberian' => ['pagi', 'siang', 'sore', 'malam'][rand(0, 3)],
                'jumlah' => rand(5, 50),
                'satuan' => 'kg',
                'biaya' => rand(50000, 500000),
                'keterangan' => 'Pemberian pakan rutin',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
