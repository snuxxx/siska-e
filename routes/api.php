<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserGlobalController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftTemplateController;
use App\Http\Controllers\ShiftRequestController;
use App\Http\Controllers\LeaveRequestController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['api'])->group(function () {
    Route::apiResource('tenants', TenantController::class);
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
});
