<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
    public function permintaanSIK()
    {
        $requests = [
            (object)[
                'id' => 1,
                'user_name' => 'Akmal Hidayah',
                'vendor_name' => 'PT. Biringkassi Raya',
                'tanggal' => '07-04-2025 06:11',
                'handled_by' => 'Budi Kurniawan',
                'status' => 'Perlu Revisi',
                'progress' => 33,
                'current_step' => 4,
            ],
            (object)[
                'id' => 2,
                'user_name' => 'Irwan Mahardika',
                'vendor_name' => 'PT. Topabbiring',
                'tanggal' => '07-04-2025 07:30',
                'handled_by' => 'Nur Fadillah',
                'status' => 'Selesai',
                'progress' => 100,
                'current_step' => 12,
            ],
        ];
    
        return view('admin.permintaansik', compact('requests'));
    }
    
    
}

