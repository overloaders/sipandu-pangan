<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\UnitController;
use App\Http\Controllers\Api\V1\WbpController;
use App\Http\Controllers\Api\V1\BlockController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FoodProgramController;
use App\Http\Controllers\Api\V1\LandPlotController;
use App\Http\Controllers\Api\V1\FarmingPlanController;
use App\Http\Controllers\Api\V1\CropTypeController;
use App\Http\Controllers\Api\V1\HarvestController;
use App\Http\Controllers\Api\V1\LivestockBarnController;
use App\Http\Controllers\Api\V1\LivestockTypeController;
use App\Http\Controllers\Api\V1\LivestockInventoryController;
use App\Http\Controllers\Api\V1\FeedSupplyController;
use App\Http\Controllers\Api\V1\FeedLogController;
use App\Http\Controllers\Api\V1\LivestockHealthController;
use App\Http\Controllers\Api\V1\FishPondController;
use App\Http\Controllers\Api\V1\FishStockController;
use App\Http\Controllers\Api\V1\WaterQualityController;
use App\Http\Controllers\Api\V1\FishHarvestController;
use App\Http\Controllers\Api\V1\InventoryItemController;
use App\Http\Controllers\Api\V1\InventoryCategoryController;
use App\Http\Controllers\Api\V1\InventoryTransactionController;
use App\Http\Controllers\Api\V1\DistributionController;
use App\Http\Controllers\Api\V1\NutritionPlanController;
use App\Http\Controllers\Api\V1\ProcessingBatchController;
use App\Http\Controllers\Api\V1\FinancialTransactionController;
use App\Http\Controllers\Api\V1\FinancialCategoryController;
use App\Http\Controllers\Api\V1\BudgetController;
use App\Http\Controllers\Api\V1\TrainingProgramController;
use App\Http\Controllers\Api\V1\ComplianceRecordController;
use App\Http\Controllers\Api\V1\DailyReportController;
use App\Http\Controllers\Api\V1\AnalyticsIndicatorController;
use App\Http\Controllers\Api\V1\ApiClientController;
use App\Http\Controllers\Api\V1\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SIPANDU PANGAN
|--------------------------------------------------------------------------
| Versioned REST API: /api/v1/*
| Auth: Laravel Sanctum (Bearer Token)
*/

Route::prefix('v1')->name('api.')->group(function () {

    // ===== PUBLIC ROUTES =====
    // Rate limiting for authentication endpoints
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
    });
    Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');

    // ===== AUTHENTICATED ROUTES =====
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
        Route::put('/auth/profile', [AuthController::class, 'updateProfile'])->name('auth.profile');
        Route::put('/auth/password', [AuthController::class, 'changePassword'])->name('auth.password');
        Route::post('/auth/client-token', [AuthController::class, 'generateClientToken'])->name('auth.client-token');
        Route::post('/auth/refresh', [AuthController::class, 'refreshToken'])->name('auth.refresh');
        Route::delete('/auth/revoke', [AuthController::class, 'revokeToken'])->name('auth.revoke');

        // ── F-1: User & Role Management ──
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);

        // ── F-2: Registrasi WBP ──
        Route::apiResource('wbp', WbpController::class);
        Route::get('/wbp/search', [WbpController::class, 'search'])->name('wbp.search');
        Route::apiResource('wbp/{wbp}/documents', \App\Http\Controllers\Api\V1\WbpDocumentController::class);
        Route::apiResource('wbp/{wbp}/family-members', \App\Http\Controllers\Api\V1\WbpFamilyMemberController::class);

        // ── F-3: Unit Kerja ──
        Route::apiResource('units', UnitController::class);

        // ── F-4: Blok & Kamar ──
        Route::apiResource('blocks', BlockController::class);
        Route::apiResource('blocks/{block}/rooms', RoomController::class);

        // ── F-5: Laporan Harian ──
        Route::apiResource('daily-reports', DailyReportController::class);

        // ── F-6: Dashboard ──
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/statistics', [DashboardController::class, 'statistics'])->name('dashboard.statistics');
        Route::get('/dashboard/farming-summary', [DashboardController::class, 'farmingSummary'])->name('dashboard.farming-summary');
        Route::get('/dashboard/livestock-summary', [DashboardController::class, 'livestockSummary'])->name('dashboard.livestock-summary');
        Route::get('/dashboard/fishery-summary', [DashboardController::class, 'fisherySummary'])->name('dashboard.fishery-summary');

        // ── P-1: Program Ketahanan Pangan ──
        Route::apiResource('food-programs', FoodProgramController::class);
        Route::apiResource('food-programs/{foodProgram}/participants', \App\Http\Controllers\Api\V1\ProgramParticipantController::class);

        // ── P-2: Manajemen Lahan ──
        Route::apiResource('land-plots', LandPlotController::class);
        Route::apiResource('crop-types', CropTypeController::class);

        // ── P-3: Rencana Kerja Pertanian ──
        Route::apiResource('farming-plans', FarmingPlanController::class);
        Route::apiResource('farming-plans/{farmingPlan}/activities', \App\Http\Controllers\Api\V1\FarmingActivityController::class);

        // ── Harvest ──
        Route::apiResource('harvests', HarvestController::class);

        // ── P-4: Peternakan ──
        Route::apiResource('livestock-barns', LivestockBarnController::class);
        Route::apiResource('livestock-types', LivestockTypeController::class);
        Route::apiResource('livestock-inventories', LivestockInventoryController::class);

        // ── Pakan ──
        Route::apiResource('feed-supplies', FeedSupplyController::class);
        Route::apiResource('feed-logs', FeedLogController::class);

        // ── Kesehatan Ternak ──
        Route::apiResource('livestock-health-records', LivestockHealthController::class);

        // ── P-5: Perikanan ──
        Route::apiResource('fish-ponds', FishPondController::class);
        Route::apiResource('fish-stocks', FishStockController::class);
        Route::apiResource('water-quality-records', WaterQualityController::class);
        Route::apiResource('fish-harvests', FishHarvestController::class);

        // ── P-7: Inventaris & Stok ──
        Route::apiResource('inventory-categories', InventoryCategoryController::class);
        Route::apiResource('inventory-items', InventoryItemController::class);
        Route::apiResource('inventory-transactions', InventoryTransactionController::class);

        // ── P-8: Distribusi ──
        Route::apiResource('distributions', DistributionController::class);

        // ── P-9: Nutrisi ──
        Route::apiResource('nutrition-plans', NutritionPlanController::class);

        // ── P-10: Pengolahan ──
        Route::apiResource('processing-batches', ProcessingBatchController::class);

        // ── P-11: Keuangan ──
        Route::apiResource('financial-categories', FinancialCategoryController::class);
        Route::apiResource('budgets', BudgetController::class);
        Route::apiResource('financial-transactions', FinancialTransactionController::class);

        // ── P-12: Pelatihan ──
        Route::apiResource('training-programs', TrainingProgramController::class);

        // ── P-13: Analitik ──
        Route::apiResource('analytics-indicators', AnalyticsIndicatorController::class);

        // ── P-14: Sertifikasi & Komplians ──
        Route::apiResource('compliance-records', ComplianceRecordController::class);

        // ── Admin: API Client Management ──
        Route::middleware('ability:admin')->group(function () {
            Route::apiResource('api-clients', ApiClientController::class);
        });
    });
});
