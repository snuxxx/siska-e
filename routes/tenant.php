<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserGlobalController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftTemplateController;
use App\Http\Controllers\ShiftRequestController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LoginController;



/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::middleware(['api'])->group(function () {
        Route::get('/', function () {
            return "berhasil :)";
        });
        Route::apiResource('users', UserGlobalController::class);
        Route::apiResource('employees', EmployeeController::class);
        Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
        Route::get('employee/export', [EmployeeController::class, 'export'])->name('employees.export');
        Route::apiResource('shifts', ShiftController::class);
        Route::apiResource('shift-templates', ShiftTemplateController::class);
        Route::apiResource('shift-requests', ShiftRequestController::class);
        Route::apiResource('leave-requests', LeaveRequestController::class);    
        Route::get('leaves-requests/export', [LeaveRequestController::class, 'exportToExcel'])->name('leave-requests.export');
        Route::get('leaves-requests/export-pdf', [LeaveRequestController::class, 'exportToPDF'])->name('leave-requests.export-pdf');
        Route::apiResource('attendances', AttendanceController::class);
        Route::prefix('attendances')->group(function () {
            Route::post('check-in', [AttendanceController::class, 'checkIn'])->name('attendances.check-in');
            Route::post('check-out', [AttendanceController::class, 'checkOut'])->name('attendances.check-out');
        });
    });
});
