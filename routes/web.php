<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIPANDU PANGAN Admin Panel
|--------------------------------------------------------------------------
*/

// Manual Book (publik, tanpa login)
Route::view('/manual', 'manual')->name('manual');

// Auth Routes
Route::get('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [App\Http\Controllers\DashboardController::class, 'stats'])->name('dashboard.stats');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // WBP
    Route::resource('wbp', App\Http\Controllers\WbpController::class);
    Route::post('/wbp/{wbp}/status', [App\Http\Controllers\WbpController::class, 'updateStatus'])->name('wbp.status');
    Route::post('/wbp/{wbp}/documents', [App\Http\Controllers\WbpController::class, 'storeDocument'])->name('wbp.store-document');
    Route::put('/wbp/{wbp}/documents/{document}', [App\Http\Controllers\WbpController::class, 'updateDocument'])->name('wbp.update-document');
    Route::delete('/wbp/{wbp}/documents/{document}', [App\Http\Controllers\WbpController::class, 'destroyDocument'])->name('wbp.destroy-document');
    Route::post('/wbp/{wbp}/family-members', [App\Http\Controllers\WbpController::class, 'storeFamilyMember'])->name('wbp.store-family-member');
    Route::put('/wbp/{wbp}/family-members/{familyMember}', [App\Http\Controllers\WbpController::class, 'updateFamilyMember'])->name('wbp.update-family-member');
    Route::delete('/wbp/{wbp}/family-members/{familyMember}', [App\Http\Controllers\WbpController::class, 'destroyFamilyMember'])->name('wbp.destroy-family-member');

    // Blocks & Rooms
    Route::resource('blocks', App\Http\Controllers\BlockController::class);
    Route::post('/blocks/{block}/status', [App\Http\Controllers\BlockController::class, 'updateStatus'])->name('blocks.status');
    Route::resource('blocks.rooms', App\Http\Controllers\RoomController::class);
    Route::post('/blocks/{block}/rooms/{room}/status', [App\Http\Controllers\RoomController::class, 'updateStatus'])->name('rooms.status');

    // Food Programs
    Route::resource('food-programs', App\Http\Controllers\FoodProgramController::class)->parameters(['food-programs' => 'foodProgram']);
    Route::post('/food-programs/{foodProgram}/status', [App\Http\Controllers\FoodProgramController::class, 'updateStatus'])->name('food-programs.status');
    Route::post('/food-programs/{foodProgram}/participants', [App\Http\Controllers\ProgramParticipantController::class, 'store'])->name('food-programs.participants.store');
    Route::delete('/food-programs/{foodProgram}/participants/{participant}', [App\Http\Controllers\ProgramParticipantController::class, 'destroy'])->name('food-programs.participants.destroy');

    // Farming
    Route::resource('farming-plans', App\Http\Controllers\FarmingPlanController::class)->parameters(['farming-plans' => 'farmingPlan']);
    Route::post('/farming-plans/{farmingPlan}/status', [App\Http\Controllers\FarmingPlanController::class, 'updateStatus'])->name('farming-plans.status');
    Route::post('/farming-plans/{farmingPlan}/activity', [App\Http\Controllers\FarmingPlanController::class, 'storeActivity'])->name('farming-plans.store-activity');
    Route::post('/farming-plans/{farmingPlan}/harvest', [App\Http\Controllers\FarmingPlanController::class, 'storeHarvest'])->name('farming-plans.store-harvest');
    Route::post('/farming-plans/{farmingPlan}/participants', [App\Http\Controllers\FarmingPlanParticipantController::class, 'store'])->name('farming-plans.participants.store');
    Route::delete('/farming-plans/{farmingPlan}/participants/{participant}', [App\Http\Controllers\FarmingPlanParticipantController::class, 'destroy'])->name('farming-plans.participants.destroy');
    Route::resource('harvests', App\Http\Controllers\HarvestController::class);

    // Livestock
    Route::get('/livestock', fn() => redirect()->route('livestock-barns.index'))->name('livestock');
    Route::resource('livestock-barns', App\Http\Controllers\LivestockBarnController::class)->parameters(['livestock-barns' => 'livestockBarn']);
    Route::post('/livestock-barns/{livestockBarn}/status', [App\Http\Controllers\LivestockBarnController::class, 'updateStatus'])->name('livestock-barns.status');
    Route::post('/livestock-barns/{livestockBarn}/health-record', [App\Http\Controllers\LivestockBarnController::class, 'storeHealthRecord'])->name('livestock-barns.store-health-record');
    Route::post('/livestock-barns/{livestockBarn}/inventory', [App\Http\Controllers\LivestockBarnController::class, 'storeInventory'])->name('livestock-barns.store-inventory');
    Route::post('/livestock-barns/{livestockBarn}/participants', [App\Http\Controllers\LivestockBarnParticipantController::class, 'store'])->name('livestock-barns.participants.store');
    Route::delete('/livestock-barns/{livestockBarn}/participants/{participant}', [App\Http\Controllers\LivestockBarnParticipantController::class, 'destroy'])->name('livestock-barns.participants.destroy');

    // Fishing
    Route::get('/fishing', fn() => redirect()->route('fish-ponds.index'))->name('fishing');
    Route::resource('fish-ponds', App\Http\Controllers\FishPondController::class)->parameters(['fish-ponds' => 'fishPond']);
    Route::post('/fish-ponds/{fishPond}/status', [App\Http\Controllers\FishPondController::class, 'updateStatus'])->name('fish-ponds.status');
    Route::get('/fish-ponds/{fishPond}/water-quality', [App\Http\Controllers\FishPondController::class, 'waterQuality'])->name('fish-ponds.water-quality');
    Route::post('/fish-ponds/{fishPond}/water-quality', [App\Http\Controllers\FishPondController::class, 'storeWaterQuality'])->name('fish-ponds.store-water-quality');
    Route::post('/fish-ponds/{fishPond}/fish-stock', [App\Http\Controllers\FishPondController::class, 'storeFishStock'])->name('fish-ponds.store-fish-stock');
    Route::post('/fish-ponds/{fishPond}/participants', [App\Http\Controllers\FishPondParticipantController::class, 'store'])->name('fish-ponds.participants.store');
    Route::delete('/fish-ponds/{fishPond}/participants/{participant}', [App\Http\Controllers\FishPondParticipantController::class, 'destroy'])->name('fish-ponds.participants.destroy');

    // Inventory
    Route::resource('inventory', App\Http\Controllers\InventoryController::class);
    Route::post('/inventory/{inventory}/status', [App\Http\Controllers\InventoryController::class, 'updateStatus'])->name('inventory.status');
    Route::get('/inventory-transactions', [App\Http\Controllers\InventoryTransactionController::class, 'index'])->name('inventory-transactions.index');
    Route::post('/inventory-transactions', [App\Http\Controllers\InventoryTransactionController::class, 'store'])->name('inventory-transactions.store');
    Route::put('/inventory-transactions/{transaction}', [App\Http\Controllers\InventoryTransactionController::class, 'update'])->name('inventory-transactions.update');
    Route::delete('/inventory-transactions/{transaction}', [App\Http\Controllers\InventoryTransactionController::class, 'destroy'])->name('inventory-transactions.destroy');

    // Financial (admin+keuangan)
    Route::resource('financial', App\Http\Controllers\FinancialController::class)->middleware('role:admin,super-admin,keuangan');
    Route::post('/financial/{transaction}/status', [App\Http\Controllers\FinancialController::class, 'updateStatus'])->name('financial.status')->middleware('role:admin,super-admin,keuangan');

    // Daily Reports
    Route::resource('daily-reports', App\Http\Controllers\DailyReportController::class)->parameters(['daily-reports' => 'dailyReport']);
    Route::post('/daily-reports/{dailyReport}/status', [App\Http\Controllers\DailyReportController::class, 'updateStatus'])->name('daily-reports.status');
    Route::post('/daily-reports/{dailyReport}/details', [App\Http\Controllers\DailyReportController::class, 'storeDetail'])->name('daily-reports.store-detail');
    Route::put('/daily-reports/{dailyReport}/details/{detail}', [App\Http\Controllers\DailyReportController::class, 'updateDetail'])->name('daily-reports.update-detail');
    Route::delete('/daily-reports/{dailyReport}/details/{detail}', [App\Http\Controllers\DailyReportController::class, 'destroyDetail'])->name('daily-reports.destroy-detail');
    Route::post('/daily-reports/{dailyReport}/kirim', [App\Http\Controllers\DailyReportController::class, 'kirim'])->name('daily-reports.kirim');
    Route::post('/daily-reports/{dailyReport}/confirm', [App\Http\Controllers\DailyReportController::class, 'confirm'])->name('daily-reports.confirm')->middleware('role:admin,super-admin,supervisor');
    Route::post('/daily-reports/{dailyReport}/reject', [App\Http\Controllers\DailyReportController::class, 'reject'])->name('daily-reports.reject')->middleware('role:admin,super-admin,supervisor');

    // Land Plots
    Route::resource('land-plots', App\Http\Controllers\LandPlotController::class)->parameters(['land-plots' => 'landPlot']);
    Route::post('/land-plots/{landPlot}/status', [App\Http\Controllers\LandPlotController::class, 'updateStatus'])->name('land-plots.status');

    // Distribution
    Route::resource('distributions', App\Http\Controllers\DistributionController::class);
    Route::post('/distributions/{distribution}/status', [App\Http\Controllers\DistributionController::class, 'updateStatus'])->name('distributions.status');
    Route::post('/distributions/{distribution}/items', [App\Http\Controllers\DistributionController::class, 'storeItem'])->name('distributions.store-item');
    Route::put('/distributions/{distribution}/items/{item}', [App\Http\Controllers\DistributionController::class, 'updateItem'])->name('distributions.update-item');
    Route::delete('/distributions/{distribution}/items/{item}', [App\Http\Controllers\DistributionController::class, 'destroyItem'])->name('distributions.destroy-item');
    Route::get('/distributions/export', [App\Http\Controllers\DistributionController::class, 'index'])->name('distributions.export');

    // Nutrition (supervisor+)
    Route::resource('nutrition', App\Http\Controllers\NutritionController::class)->middleware('role:admin,super-admin,supervisor');
    Route::post('/nutrition/{nutrition}/status', [App\Http\Controllers\NutritionController::class, 'updateStatus'])->name('nutrition.status')->middleware('role:admin,super-admin,supervisor');
    Route::post('/nutrition/{nutrition}/components', [App\Http\Controllers\NutritionController::class, 'storeComponent'])->name('nutrition.store-component');
    Route::put('/nutrition/{nutrition}/components/{component}', [App\Http\Controllers\NutritionController::class, 'updateComponent'])->name('nutrition.update-component');
    Route::delete('/nutrition/{nutrition}/components/{component}', [App\Http\Controllers\NutritionController::class, 'destroyComponent'])->name('nutrition.destroy-component');

    // Processing (supervisor+)
    Route::resource('processing', App\Http\Controllers\ProcessingController::class)->middleware('role:admin,super-admin,supervisor');
    Route::post('/processing/{processing}/status', [App\Http\Controllers\ProcessingController::class, 'updateStatus'])->name('processing.status')->middleware('role:admin,super-admin,supervisor');
    Route::post('/processing/{processing}/items', [App\Http\Controllers\ProcessingController::class, 'storeItem'])->name('processing.store-item');
    Route::put('/processing/{processing}/items/{item}', [App\Http\Controllers\ProcessingController::class, 'updateItem'])->name('processing.update-item');
    Route::delete('/processing/{processing}/items/{item}', [App\Http\Controllers\ProcessingController::class, 'destroyItem'])->name('processing.destroy-item');

    // Training (supervisor+)
    Route::resource('training', App\Http\Controllers\TrainingController::class)->middleware('role:admin,super-admin,supervisor');
    Route::get('/training/{program}/status', function (\App\Models\TrainingProgram $program) {
        return redirect()->route('training.show', $program->id)->with('error', 'Gunakan form di halaman detail untuk mengubah status.');
    })->name('training.update-status-redirect');
    Route::post('/training/{program}/status', [App\Http\Controllers\TrainingController::class, 'updateStatus'])->name('training.update-status')->middleware('role:admin,super-admin,supervisor');
    Route::get('/training/{program}/participants', [App\Http\Controllers\TrainingController::class, 'participants'])->name('training.participants')->middleware('role:admin,super-admin,supervisor');
    Route::post('/training/{program}/participants', [App\Http\Controllers\TrainingController::class, 'storeParticipant'])->name('training.add-participant')->middleware('role:admin,super-admin,supervisor');
    Route::delete('/training/{program}/participants/{participant}', [App\Http\Controllers\TrainingController::class, 'destroyParticipant'])->name('training.remove-participant')->middleware('role:admin,super-admin,supervisor');
    Route::put('/training/{program}/participants/{participant}/nilai', [App\Http\Controllers\TrainingController::class, 'updateNilai'])->name('training.update-nilai')->middleware('role:admin,super-admin,supervisor');
    Route::post('/training/{program}/participants/{participant}/issue-cert', [App\Http\Controllers\TrainingController::class, 'issueParticipantCert'])->name('training.issue-participant-cert')->middleware('role:admin,super-admin,supervisor');
    Route::get('/training/{program}/certificates/{certificate}/print', [App\Http\Controllers\TrainingController::class, 'printCertificate'])->name('training.print-certificate')->middleware('role:admin,super-admin,supervisor');
    Route::get('/training/{program}/certifications', [App\Http\Controllers\TrainingController::class, 'certifications'])->name('training.certifications')->middleware('role:admin,super-admin,supervisor');
    Route::post('/training/{program}/certifications', [App\Http\Controllers\TrainingController::class, 'storeCertification'])->name('training.issue-certification')->middleware('role:admin,super-admin,supervisor');

    // Stock Opname
    Route::resource('stock-opname', App\Http\Controllers\StockOpnameController::class);
    Route::post('/stock-opname/{stock_opname}/status', [App\Http\Controllers\StockOpnameController::class, 'updateStatus'])->name('stock-opname.status');
    Route::post('/stock-opname/{stock_opname}/items', [App\Http\Controllers\StockOpnameController::class, 'storeItem'])->name('stock-opname.store-item');
    Route::put('/stock-opname/{stock_opname}/items/{item}', [App\Http\Controllers\StockOpnameController::class, 'updateItem'])->name('stock-opname.update-item');
    Route::delete('/stock-opname/{stock_opname}/items/{item}', [App\Http\Controllers\StockOpnameController::class, 'destroyItem'])->name('stock-opname.destroy-item');

    // Analytics (supervisor+)
    Route::resource('analytics', App\Http\Controllers\AnalyticsController::class)->middleware('role:admin,super-admin,supervisor');
    Route::get('/analytics-dashboard', [App\Http\Controllers\AnalyticsController::class, 'dashboard'])->name('analytics.dashboard')->middleware('role:admin,super-admin,supervisor');
    Route::post('/analytics/{analytic}/targets', [App\Http\Controllers\AnalyticsController::class, 'storeTarget'])->name('analytics.store-target')->middleware('role:admin,super-admin,supervisor');
    Route::put('/analytics/{analytic}/targets/{target}', [App\Http\Controllers\AnalyticsController::class, 'updateTarget'])->name('analytics.update-target')->middleware('role:admin,super-admin,supervisor');
    Route::delete('/analytics/{analytic}/targets/{target}', [App\Http\Controllers\AnalyticsController::class, 'destroyTarget'])->name('analytics.destroy-target')->middleware('role:admin,super-admin,supervisor');

    // Compliance (supervisor+)
    Route::resource('compliance', App\Http\Controllers\ComplianceController::class)->middleware('role:admin,super-admin,supervisor');
    Route::post('/compliance/{compliance}/status', [App\Http\Controllers\ComplianceController::class, 'updateStatus'])->name('compliance.status')->middleware('role:admin,super-admin,supervisor');
    Route::get('/compliance/{record}/details', [App\Http\Controllers\ComplianceController::class, 'details'])->name('compliance.details')->middleware('role:admin,super-admin,supervisor');
    Route::post('/compliance/{record}/details', [App\Http\Controllers\ComplianceController::class, 'storeDetail'])->name('compliance.add-detail')->middleware('role:admin,super-admin,supervisor');
    Route::put('/compliance/{record}/details/{detail}', [App\Http\Controllers\ComplianceController::class, 'updateDetail'])->name('compliance.update-detail')->middleware('role:admin,super-admin,supervisor');
    Route::delete('/compliance/{record}/details/{detail}', [App\Http\Controllers\ComplianceController::class, 'destroyDetail'])->name('compliance.remove-detail')->middleware('role:admin,super-admin,supervisor');

    // Master Data Routes
    Route::prefix('master')->name('master.')->middleware('role:admin,super-admin,supervisor')->group(function () {
        Route::resource('units', App\Http\Controllers\Master\UnitController::class);
        Route::resource('livestock-types', App\Http\Controllers\Master\LivestockTypeController::class)->parameters(['livestock-types' => 'livestockType']);
        Route::resource('crop-types', App\Http\Controllers\Master\CropTypeController::class)->parameters(['crop-types' => 'cropType']);
        Route::resource('inventory-categories', App\Http\Controllers\Master\InventoryCategoryController::class)->parameters(['inventory-categories' => 'inventoryCategory']);
        Route::resource('financial-categories', App\Http\Controllers\Master\FinancialCategoryController::class)->parameters(['financial-categories' => 'financialCategory']);
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:admin,super-admin')->group(function () {
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);

        // Slow Query Monitoring (hanya super-admin — memuat SQL mentah)
        Route::get('slow-queries', [App\Http\Controllers\Admin\SlowQueryController::class, 'index'])
            ->name('slow-queries.index')
            ->middleware('role:super-admin');
        
        // Audit Trail
        Route::get('audit-trail', [App\Http\Controllers\Admin\AuditTrailController::class, 'index'])->name('audit-trail.index');
        Route::get('audit-trail/statistics', [App\Http\Controllers\Admin\AuditTrailController::class, 'statistics'])->name('audit-trail.statistics');
        Route::get('audit-trail/export', [App\Http\Controllers\Admin\AuditTrailController::class, 'export'])->name('audit-trail.export');
        Route::get('audit-trail/model-history/{subjectType}/{subjectId}/pdf', [App\Http\Controllers\Admin\AuditTrailController::class, 'modelHistoryPdf'])->name('audit-trail.model-history-pdf');
        Route::post('audit-trail/cleanup', [App\Http\Controllers\Admin\AuditTrailController::class, 'cleanup'])->name('audit-trail.cleanup');
        Route::get('audit-trail/{activity}', [App\Http\Controllers\Admin\AuditTrailController::class, 'show'])->name('audit-trail.show');
        Route::get('audit-trail/{subjectType}/{subjectId}', [App\Http\Controllers\Admin\AuditTrailController::class, 'modelHistory'])->name('audit-trail.model-history');
    });

    // Reports (PDF Export)
    Route::prefix('reports')->name('reports.')->middleware('role:admin,super-admin,supervisor')->group(function () {
        Route::get('/summary', [App\Http\Controllers\ReportController::class, 'summary'])->name('summary');
        Route::get('/wbp', [App\Http\Controllers\ReportController::class, 'wbp'])->name('wbp');
        Route::get('/food-programs', [App\Http\Controllers\ReportController::class, 'foodPrograms'])->name('food-programs');
        Route::get('/farming', [App\Http\Controllers\ReportController::class, 'farming'])->name('farming');
        Route::get('/harvest', [App\Http\Controllers\ReportController::class, 'harvest'])->name('harvest');
        Route::get('/livestock', [App\Http\Controllers\ReportController::class, 'livestock'])->name('livestock');
        Route::get('/fishery', [App\Http\Controllers\ReportController::class, 'fishery'])->name('fishery');
        Route::get('/inventory', [App\Http\Controllers\ReportController::class, 'inventory'])->name('inventory');
        Route::get('/financial', [App\Http\Controllers\ReportController::class, 'financial'])->name('financial');
        Route::get('/daily-reports', [App\Http\Controllers\ReportController::class, 'dailyReports'])->name('daily-reports');
        Route::get('/distributions', [App\Http\Controllers\ReportController::class, 'distributions'])->name('distributions');
        Route::get('/nutrition', [App\Http\Controllers\ReportController::class, 'nutrition'])->name('nutrition');
        Route::get('/training', [App\Http\Controllers\ReportController::class, 'training'])->name('training');
        Route::get('/compliance', [App\Http\Controllers\ReportController::class, 'compliance'])->name('compliance');
        Route::get('/analytics', [App\Http\Controllers\ReportController::class, 'analytics'])->name('analytics');
        Route::get('/processing', [App\Http\Controllers\ReportController::class, 'processing'])->name('processing');
        Route::get('/stock-opname', [App\Http\Controllers\ReportController::class, 'stockOpname'])->name('stock-opname');
        Route::get('/land-plots', [App\Http\Controllers\ReportController::class, 'landPlots'])->name('land-plots');
        Route::get('/blocks', [App\Http\Controllers\ReportController::class, 'blocks'])->name('blocks');
        Route::get('/master-units', [App\Http\Controllers\ReportController::class, 'masterUnits'])->name('master-units');
        Route::get('/master-crop-types', [App\Http\Controllers\ReportController::class, 'masterCropTypes'])->name('master-crop-types');
        Route::get('/master-financial-categories', [App\Http\Controllers\ReportController::class, 'masterFinancialCategories'])->name('master-financial-categories');
        Route::get('/master-inventory-categories', [App\Http\Controllers\ReportController::class, 'masterInventoryCategories'])->name('master-inventory-categories');
        Route::get('/master-livestock-types', [App\Http\Controllers\ReportController::class, 'masterLivestockTypes'])->name('master-livestock-types');

        // Pusat Laporan: index + preview dengan filter
        // HARUS didefinisikan SEBELUM route detail (/{param}) agar tidak tertimpa
        Route::get('/', [App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/{report}/preview', [App\Http\Controllers\ReportController::class, 'preview'])->name('preview');

        // Detail PDF Export routes (after preview to avoid route conflicts)
        Route::get('/stock-opname/{opname}', [App\Http\Controllers\ReportController::class, 'stockOpnameDetail'])->name('stock-opname-detail');
        Route::get('/land-plots/{landPlot}', [App\Http\Controllers\ReportController::class, 'landPlotDetail'])->name('land-plots-detail');
        Route::get('/analytics/{analytic}', [App\Http\Controllers\ReportController::class, 'analyticsDetail'])->name('analytics-detail');
        Route::get('/daily-reports/{report}', [App\Http\Controllers\ReportController::class, 'dailyReportDetail'])->name('daily-reports-detail');
        Route::get('/distributions/{distribution}', [App\Http\Controllers\ReportController::class, 'distributionDetail'])->name('distributions-detail');
        Route::get('/financial/{transaction}', [App\Http\Controllers\ReportController::class, 'financialDetail'])->name('financial-detail');
        Route::get('/harvest/{harvest}', [App\Http\Controllers\ReportController::class, 'harvestDetail'])->name('harvest-detail');
        Route::get('/inventory/{item}', [App\Http\Controllers\ReportController::class, 'inventoryDetail'])->name('inventory-detail');
        Route::get('/wbp/{wbp}', [App\Http\Controllers\ReportController::class, 'wbpDetail'])->name('wbp-detail');
        Route::get('/food-programs/{program}', [App\Http\Controllers\ReportController::class, 'foodProgramDetail'])->name('food-programs-detail');

        // Detail PDF Exports
        Route::get('/farming-plan/{farmingPlan}', [App\Http\Controllers\ReportController::class, 'farmingPlanDetail'])->name('farming-plan');
        Route::get('/livestock-barn/{livestockBarn}', [App\Http\Controllers\ReportController::class, 'livestockBarnDetail'])->name('livestock-barn');
        Route::get('/fish-pond/{fishPond}', [App\Http\Controllers\ReportController::class, 'fishPondDetail'])->name('fish-pond');
        Route::get('/processing/{batch}', [App\Http\Controllers\ReportController::class, 'processingDetail'])->name('processing-detail');
        Route::get('/training/{program}', [App\Http\Controllers\ReportController::class, 'trainingDetail'])->name('training-detail');
        Route::get('/compliance/{record}', [App\Http\Controllers\ReportController::class, 'complianceDetail'])->name('compliance-detail');
        Route::get('/nutrition/{plan}', [App\Http\Controllers\ReportController::class, 'nutritionDetail'])->name('nutrition-detail');
        Route::get('/rooms/{room}', [App\Http\Controllers\ReportController::class, 'roomDetail'])->name('room-detail');
    });
});
