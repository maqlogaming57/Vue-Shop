<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        // Ambil data laporan dari model Report
        $sales = Report::getSalesReport();

        // Kirim data ke frontend
        return Inertia::render('Admin/Report/Index', [
            'sales' => $sales,
        ]);
    }
}
