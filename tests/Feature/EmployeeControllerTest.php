<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function can_import_employee_data_from_excel()
    {
        $content = [
            ['kode_perusahaan', 'kode_divisi', 'nama_lengkap', 'email', 'no_telepon', 'jabatan', 'divisi', 'tanggal_masuk', 'status', 'gaji_pokok', 'tunjangan', 'potongan'],
            ['SDP', 'IT', 'John Doe', 'john@example.com', '08123456789', 'Developer', 'IT', '2024-03-26', 'Aktif', '8000000', '1000000', '200000']
        ];

        // Create test file
        $csvContent = "";
        foreach ($content as $row) {
            $csvContent .= implode(',', $row) . "\n";
        }
        
        $file = UploadedFile::fake()->createWithContent(
            'employees.xlsx',
            $csvContent
        );

        // Test import endpoint
        $response = $this->postJson('/api/employees/import', [
            'file' => $file
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Data karyawan berhasil diimpor.']);

        // Verify data was imported
        $this->assertDatabaseHas('employees', [
            'nama_lengkap' => 'John Doe',
            'email' => 'john@example.com'
        ]);
    }

    /** @test */
    public function can_export_employee_data_to_excel()
    {
        // Create test employee
        Employee::create([
            'kode_karyawan' => 'SDP-IT-001-2024',
            'kode_perusahaan' => 'SDP',
            'kode_divisi' => 'IT',
            'nama_lengkap' => 'John Doe',
            'email' => 'john@example.com',
            'no_telepon' => '08123456789',
            'jabatan' => 'Developer',
            'divisi' => 'IT',
            'tanggal_masuk' => '2024-03-26',
            'status' => 'Aktif'
        ]);

        Excel::fake();

        // Test export endpoint
        $response = $this->getJson('/api/employees/export');

        $response->assertStatus(200);
        Excel::assertDownloaded('employees.xlsx');
    }
}