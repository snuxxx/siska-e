<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SalaryComponent;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition()
    {
        return [
            'kode_perusahaan' => 'SDP',
            'kode_divisi' => 'IT',
            'kode_karyawan' => function () {
                return Employee::generateEmployeeCode('SDP', 'IT');
            },
            'nama_lengkap' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'no_telepon' => $this->faker->phoneNumber,
            'jabatan' => 'Developer',
            'divisi' => 'IT',
            'tanggal_masuk' => $this->faker->date(),
            'status' => 'Aktif',
        ];
    }
}

class SalaryComponentFactory extends Factory
{
    protected $model = SalaryComponent::class;

    public function definition()
    {
        return [
            'gaji_pokok' => $this->faker->numberBetween(4000000, 10000000),
            'tunjangan' => $this->faker->numberBetween(500000, 2000000),
            'potongan' => $this->faker->numberBetween(100000, 500000),
        ];
    }
}