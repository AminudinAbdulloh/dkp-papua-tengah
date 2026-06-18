<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class VisitorFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $path = trim(uri_string(), '/');
        
        // Jangan catat statistik jika mengakses halaman portal atau area admin
        if ($path === '' || $path === 'admin' || str_starts_with($path, 'admin/')) {
            return;
        }

        // Jangan catat statistik untuk file statis / aset (deteksi via ekstensi file)
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext !== '') {
            $ignoredExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'map', 'woff', 'woff2', 'ttf', 'eot', 'json', 'txt', 'xml', 'pdf', 'zip', 'rar'];
            if (in_array(strtolower($ext), $ignoredExtensions, true)) {
                return;
            }
        }

        $agent = $request->getUserAgent();
        
        // Mencegah bot menambah jumlah statistik
        if ($agent->isRobot()) {
            return;
        }

        if (!\App\Models\VisitorModel::tableReady()) {
            return;
        }

        $visitorModel = new \App\Models\VisitorModel();

        // Membaca cookie visitor_token
        helper('cookie');
        $cookieToken = get_cookie('visitor_token');

        if (empty($cookieToken)) {
            // Generate token unik baru
            $cookieToken = bin2hex(random_bytes(16));
            
            // Set cookie visitor_token berdurasi 1 tahun (365 hari = 31.536.000 detik)
            set_cookie('visitor_token', $cookieToken, 31536000);

            // Simpan visitor baru ke tabel visitors
            $visitorModel->insert([
                'cookie_token' => $cookieToken,
                'ip_address'   => $request->getIPAddress(),
                'user_agent'   => $agent->getAgentString(),
                'today_views'  => 1,
                'total_views'  => 1,
            ]);
        } else {
            // Cari visitor di database berdasarkan cookie_token
            $visitor = $visitorModel->where('cookie_token', $cookieToken)->first();

            if (!$visitor) {
                // Jika tidak ditemukan (misal DB dikosongkan), buat record baru
                $visitorModel->insert([
                    'cookie_token' => $cookieToken,
                    'ip_address'   => $request->getIPAddress(),
                    'user_agent'   => $agent->getAgentString(),
                    'today_views'  => 1,
                    'total_views'  => 1,
                ]);
            } else {
                // Cek apakah kunjungan terakhir (updated_at) di hari yang sama dengan hari ini
                $lastUpdated = $visitor['updated_at'] ?? $visitor['created_at'] ?? null;
                $lastVisitDate = $lastUpdated ? date('Y-m-d', strtotime($lastUpdated)) : '';
                $todayDate = date('Y-m-d');

                if ($lastVisitDate === $todayDate) {
                    // Jika hari yang sama: increment today_views dan total_views
                    $visitorModel->update($visitor['id'], [
                        'today_views' => (int)$visitor['today_views'] + 1,
                        'total_views' => (int)$visitor['total_views'] + 1,
                        'ip_address'  => $request->getIPAddress(),
                        'user_agent'  => $agent->getAgentString(),
                    ]);
                } else {
                    // Jika hari yang berbeda: set today_views = 1, increment total_views
                    $visitorModel->update($visitor['id'], [
                        'today_views' => 1,
                        'total_views' => (int)$visitor['total_views'] + 1,
                        'ip_address'  => $request->getIPAddress(),
                        'user_agent'  => $agent->getAgentString(),
                    ]);
                }
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
