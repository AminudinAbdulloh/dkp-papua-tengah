<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitorModel extends Model
{
    protected $table            = 'visitors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['ip_address', 'user_agent', 'cookie_token', 'today_views', 'total_views', 'updated_at', 'created_at'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public static function tableReady(): bool
    {
        try {
            return \Config\Database::connect()->tableExists('visitors');
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getTodayVisitors()
    {
        return $this->like('updated_at', date('Y-m-d'), 'after')->countAllResults();
    }

    public function get7DaysVisitors()
    {
        return $this->where('updated_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))->countAllResults();
    }

    public function getTotalVisitors()
    {
        return $this->countAllResults();
    }

    public function getTodayViews()
    {
        $result = $this->like('updated_at', date('Y-m-d'), 'after')
                       ->selectSum('today_views')
                       ->first();
        return $result ? (int)($result['today_views'] ?? 0) : 0;
    }

    public function getTotalViews()
    {
        $result = $this->selectSum('total_views')->first();
        return $result ? (int)($result['total_views'] ?? 0) : 0;
    }
}
