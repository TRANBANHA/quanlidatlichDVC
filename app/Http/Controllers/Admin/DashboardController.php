<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HoSo;
use App\Models\DonVi;
use App\Models\Service;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Thống kê tổng quan
        $totalHoSo = HoSo::count();
        $totalPending = HoSo::whereIn('trang_thai', [
            HoSo::STATUS_RECEIVED,
            HoSo::STATUS_PROCESSING,
            HoSo::STATUS_NEED_SUPPLEMENT,
        ])->count();
        $totalCompleted = HoSo::where('trang_thai', HoSo::STATUS_COMPLETED)->count();
        $totalRejected = HoSo::where('trang_thai', HoSo::STATUS_CANCELLED)->count();
        
        // Thống kê theo phường
        $hoSoTheoPhuong = DonVi::select('don_vi.ten_don_vi', DB::raw('count(ho_so.id) as total'))
            ->leftJoin('ho_so', 'don_vi.id', '=', 'ho_so.don_vi_id')
            ->groupBy('don_vi.id', 'don_vi.ten_don_vi')
            ->get();

        // Thống kê theo dịch vụ
        $hoSoTheoDichVu = Service::select('dich_vu.ten_dich_vu', DB::raw('count(ho_so.id) as total'))
            ->leftJoin('ho_so', 'dich_vu.id', '=', 'ho_so.dich_vu_id')
            ->groupBy('dich_vu.id', 'dich_vu.ten_dich_vu')
            ->get();

        // Thống kê theo thời gian (6 tháng gần nhất)
        $hoSoTheoThang = HoSo::select(
            DB::raw('MONTH(ngay_hen) as month'),
            DB::raw('YEAR(ngay_hen) as year'),
            DB::raw('count(*) as total')
        )
            ->whereDate('ngay_hen', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Thống kê đánh giá
        $avgRating = Rating::avg('diem');
        $ratingCounts = Rating::select('diem', DB::raw('count(*) as total'))
            ->groupBy('diem')
            ->get()
            ->pluck('total', 'diem')
            ->toArray();

        // Tỷ lệ xử lý đúng hạn
        $totalInTime = HoSo::where('trang_thai', HoSo::STATUS_COMPLETED)
            ->whereRaw('DATE(updated_at) <= ngay_hen')
            ->count();
        $percentageInTime = $totalCompleted > 0 ? ($totalInTime / $totalCompleted) * 100 : 0;

        return view('backend.index', compact(
            'totalHoSo',
            'totalPending',
            'totalCompleted',
            'totalRejected',
            'hoSoTheoPhuong',
            'hoSoTheoDichVu',
            'hoSoTheoThang',
            'avgRating',
            'ratingCounts',
            'percentageInTime'
        ));
    }

    public function exportExcel()
    {
        // Logic xuất báo cáo Excel sẽ thêm sau
    }

    public function exportPDF()
    {
        // Logic xuất báo cáo PDF sẽ thêm sau
    }

    public function getChartData(Request $request)
    {
        try {
            $type = $request->input('registrationType');
            $view = $request->input('view', 'month');
            
            $query = HoSo::select(
                DB::raw('COUNT(*) as total')
            );

            // Filter by type
            switch($type) {
                case 'birth_registration':
                    $query->where('dich_vu_id', 1); // ID cho dịch vụ khai sinh
                    break;
                case 'temp_residence_registration':
                    $query->where('dich_vu_id', 2); // ID cho dịch vụ tạm trú
                    break;
                case 'absence_registration':
                    $query->where('dich_vu_id', 3); // ID cho dịch vụ tạm vắng
                    break;
                case 'death_registration':
                    $query->where('dich_vu_id', 4); // ID cho dịch vụ khai tử
                    break;
                case 'citizens':
                    $query->where('dich_vu_id', 5); // ID cho dịch vụ công dân
                    break;
            }

            // Group by time period
            switch($view) {
                case 'month':
                    $query->addSelect(DB::raw('MONTH(ngay_hen) as period'))
                          ->whereYear('ngay_hen', date('Y'))
                          ->groupBy(DB::raw('MONTH(ngay_hen)'))
                          ->orderBy('period');
                    break;
                case 'quarter':
                    $query->addSelect(DB::raw('QUARTER(ngay_hen) as period'))
                          ->whereYear('ngay_hen', date('Y'))
                          ->groupBy(DB::raw('QUARTER(ngay_hen)'))
                          ->orderBy('period');
                    break;
                case 'year':
                    $query->addSelect(DB::raw('YEAR(ngay_hen) as period'))
                          ->whereDate('ngay_hen', '>=', Carbon::now()->subYears(5))
                          ->groupBy(DB::raw('YEAR(ngay_hen)'))
                          ->orderBy('period');
                    break;
            }

            $data = $query->orderBy('period')->get();

            // Format data for chart depending on view
            if ($view === 'month') {
                $labelsCount = 12;
                $chartData = array_fill(0, $labelsCount, 0);
                foreach ($data as $item) {
                    $idx = (int)$item->period - 1; // month 1..12 -> index 0..11
                    if ($idx >= 0 && $idx < $labelsCount) {
                        $chartData[$idx] = (int)$item->total;
                    }
                }
            } elseif ($view === 'quarter') {
                $labelsCount = 4;
                $chartData = array_fill(0, $labelsCount, 0);
                foreach ($data as $item) {
                    $idx = (int)$item->period - 1; // quarter 1..4 -> index 0..3
                    if ($idx >= 0 && $idx < $labelsCount) {
                        $chartData[$idx] = (int)$item->total;
                    }
                }
            } else { // year
                // Build last 5 years labels (e.g. 2021..2025)
                $currentYear = (int)date('Y');
                $startYear = $currentYear - 4;
                $labelsCount = 5;
                $chartData = array_fill(0, $labelsCount, 0);
                foreach ($data as $item) {
                    $year = (int)$item->period; // e.g. 2023
                    $idx = $year - $startYear; // map to 0..4
                    if ($idx >= 0 && $idx < $labelsCount) {
                        $chartData[$idx] = (int)$item->total;
                    }
                }
            }

            return response()->json($chartData);
            
        } catch(\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}