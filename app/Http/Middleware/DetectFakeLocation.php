<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetectFakeLocation
{
    /**
     * Handle the incoming request and detect fake GPS locations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Validasi awal: pastikan parameter latitude dan longitude tersedia
        if (!$request->has(['latitude', 'longitude'])) {
            return response()->json(['message' => 'Lokasi tidak ditemukan.'], 400);
        }

        // Validasi latitude dan longitude dalam rentang yang valid
        if (!$this->isValidCoordinates($request->latitude, $request->longitude)) {
            return response()->json(['message' => 'Koordinat GPS tidak valid.'], 400);
        }

        // Deteksi aplikasi mock location
        if ($this->isMockLocation($request)) {
            return response()->json(['message' => 'Lokasi terdeteksi palsu. Absensi ditolak.'], 400);
        }

        return $next($request);
    }

    /**
     * Validate the latitude and longitude range.
     *
     * @param  float  $latitude
     * @param  float  $longitude
     * @return bool
     */
    private function isValidCoordinates($latitude, $longitude)
    {
        return is_numeric($latitude) && is_numeric($longitude) &&
            $latitude >= -90 && $latitude <= 90 &&
            $longitude >= -180 && $longitude <= 180;
    }

    /**
     * Detect fake GPS or mock location usage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function isMockLocation(Request $request)
    {
        // 1. Cek pola lokasi yang tidak masuk akal
        if ($this->isUnrealisticCoordinates($request->latitude, $request->longitude)) {
            return true;
        }

        // 2. Periksa adanya aplikasi mock location (deteksi dari header)
        if ($this->hasMockLocationHeaders($request)) {
            return true;
        }

        // 3. Validasi metadata sensor perangkat (jika data tersedia)
        if (!$this->validateDeviceSensors($request)) {
            return true;
        }

        return false;
    }

    /**
     * Check for unrealistic GPS coordinates.
     *
     * @param  float  $latitude
     * @param  float  $longitude
     * @return bool
     */
    private function isUnrealisticCoordinates($latitude, $longitude)
    {
        // Tambahkan validasi jika lokasi berada jauh dari area kerja
        $officeLatitude = -6.200000; // Contoh lokasi kantor
        $officeLongitude = 106.816666;
        $distance = $this->calculateDistance($latitude, $longitude, $officeLatitude, $officeLongitude);

        // Jika jarak terlalu jauh (contoh: 100 km dari kantor)
        return $distance > 100000; // Dalam meter
    }

    /**
     * Calculate the distance between two GPS coordinates (Haversine Formula).
     *
     * @param  float  $lat1
     * @param  float  $lon1
     * @param  float  $lat2
     * @param  float  $lon2
     * @return float
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Jarak dalam meter
    }

    /**
     * Check for headers that indicate mock location usage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function hasMockLocationHeaders(Request $request)
    {
        $mockHeaders = ['x-mock-location', 'x-fake-gps', 'x-latitude-debug'];

        foreach ($mockHeaders as $header) {
            if ($request->hasHeader($header)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate device sensor data if available.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    private function validateDeviceSensors(Request $request)
    {
        // Validasi opsional: Cek data sensor perangkat jika tersedia
        $sensorData = $request->header('x-device-sensors'); // Contoh header custom

        if ($sensorData) {
            $sensors = json_decode($sensorData, true);

            // Pastikan akselerometer atau magnetometer tersedia (contoh data sederhana)
            if (isset($sensors['accelerometer']) && isset($sensors['magnetometer'])) {
                return true;
            }

            // Sensor tidak valid
            return false;
        }

        // Jika tidak ada data sensor, asumsikan data valid (opsional)
        return true;
    }
}
