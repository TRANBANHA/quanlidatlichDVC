<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NotificationMail;
use App\Models\HoSo;
use App\Models\Service;
use App\Models\ThongBao;
use App\Models\DonVi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HoSoController extends Controller
{
    /**
     * Danh sách hồ sơ được phân công
     */
    public function index(Request $request)
    {
        $currentUser = Auth::guard('admin')->user();
        
        $baseQuery = HoSo::with(['dichVu', 'nguoiDung', 'donVi', 'quanTriVien']);

        // Phân quyền xem hồ sơ
        if ($currentUser->isCanBo()) {
            // Cán bộ: Chỉ xem hồ sơ được phân công cho mình, sắp xếp theo số thứ tự
            $baseQuery->where('quan_tri_vien_id', $currentUser->id)
                      ->orderBy('ngay_hen', 'asc')
                      ->orderBy('so_thu_tu', 'asc');
        } elseif ($currentUser->isAdminPhuong()) {
            // Admin phường: Xem tất cả hồ sơ của phường, sắp xếp theo nhân viên
            $baseQuery->where('don_vi_id', $currentUser->don_vi_id)
                      ->orderBy('quan_tri_vien_id', 'asc')
                      ->orderBy('ngay_hen', 'asc')
                      ->orderBy('so_thu_tu', 'asc');
        } else {
            // Admin tổng: Phải chọn phường trước
            if ($request->filled('don_vi_id')) {
                $baseQuery->where('don_vi_id', $request->don_vi_id)
                          ->orderBy('ngay_hen', 'asc')
                          ->orderBy('so_thu_tu', 'asc');
            } else {
                // Chưa chọn phường, không hiển thị hồ sơ nào
                $baseQuery->whereRaw('1 = 0');
            }
        }

        // Lọc theo dịch vụ
        if ($request->filled('dich_vu_id')) {
            $baseQuery->where('dich_vu_id', $request->dich_vu_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('trang_thai')) {
            $baseQuery->where('trang_thai', $request->trang_thai);
        }

        // Lọc theo ngày
        if ($request->filled('tu_ngay')) {
            $baseQuery->whereDate('ngay_hen', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $baseQuery->whereDate('ngay_hen', '<=', $request->den_ngay);
        }

        // Tìm kiếm theo mã hồ sơ hoặc tên người dùng
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function($q) use ($search) {
                $q->where('ma_ho_so', 'like', "%{$search}%")
                  ->orWhereHas('nguoiDung', function($q2) use ($search) {
                      $q2->where('ten', 'like', "%{$search}%")
                         ->orWhere('cccd', 'like', "%{$search}%");
                  });
            });
        }

        // Lấy dữ liệu và sắp xếp
        $hoSos = $baseQuery->get();
        
        // Tách hồ sơ hôm nay
        $hoSoHomNay = $hoSos->filter(function($hoSo) {
            return $hoSo->ngay_hen && \Carbon\Carbon::parse($hoSo->ngay_hen)->isToday();
        })->sortBy(function($hoSo) {
            return [
                $hoSo->so_thu_tu ?? 9999,
                $hoSo->gio_hen ?? '23:59'
            ];
        });
        
        // Hồ sơ các ngày khác
        $hoSoCacNgayKhac = $hoSos->filter(function($hoSo) {
            return !$hoSo->ngay_hen || !\Carbon\Carbon::parse($hoSo->ngay_hen)->isToday();
        });

        // Group theo ngày hẹn, sau đó trong mỗi ngày group theo cán bộ (Admin phường) hoặc dịch vụ (Cán bộ)
        $groupedHoSos = [];
        $groupedHoSosHomNay = [];
        
        if ($currentUser->isAdmin()) {
            // Admin tổng: Group theo ngày
            $groupedHoSosHomNay = ['hom_nay' => $hoSoHomNay];
            $groupedHoSos = $hoSoCacNgayKhac->groupBy(function($hoSo) {
                return $hoSo->ngay_hen ? \Carbon\Carbon::parse($hoSo->ngay_hen)->format('Y-m-d') : 'khong_co_ngay';
            })->map(function($hoSoGroup) {
                return $hoSoGroup->sortBy(function($hoSo) {
                    return [
                        $hoSo->so_thu_tu ?? 9999,
                        $hoSo->gio_hen ?? '23:59'
                    ];
                });
            })->sortKeys();
        } elseif ($currentUser->isAdminPhuong()) {
            // Admin phường: Group theo ngày, trong mỗi ngày group theo cán bộ
            if ($hoSoHomNay->count() > 0) {
                $groupedHoSosHomNay = $hoSoHomNay->groupBy(function($hoSo) {
                    return $hoSo->quan_tri_vien_id ?? 'null';
                })->map(function($hoSoGroup) {
                    return $hoSoGroup->sortBy(function($hoSo) {
                        return [
                            $hoSo->so_thu_tu ?? 9999,
                            $hoSo->gio_hen ?? '23:59'
                        ];
                    });
                });
            }
            
            $groupedHoSos = $hoSoCacNgayKhac->groupBy(function($hoSo) {
                return $hoSo->ngay_hen ? \Carbon\Carbon::parse($hoSo->ngay_hen)->format('Y-m-d') : 'khong_co_ngay';
            })->map(function($hoSoGroup) {
                return $hoSoGroup->groupBy(function($hoSo) {
                    return $hoSo->quan_tri_vien_id ?? 'null';
                })->map(function($hoSoGroupByCanBo) {
                    return $hoSoGroupByCanBo->sortBy(function($hoSo) {
                        return [
                            $hoSo->so_thu_tu ?? 9999,
                            $hoSo->gio_hen ?? '23:59'
                        ];
                    });
                });
            })->sortKeys();
        } elseif ($currentUser->isCanBo()) {
            // Cán bộ: Group theo ngày, trong mỗi ngày group theo dịch vụ
            if ($hoSoHomNay->count() > 0) {
                $groupedHoSosHomNay = $hoSoHomNay->groupBy(function($hoSo) {
                    return $hoSo->dich_vu_id ?? 'null';
                })->map(function($hoSoGroup) {
                    return $hoSoGroup->sortBy(function($hoSo) {
                        return [
                            $hoSo->so_thu_tu ?? 9999,
                            $hoSo->gio_hen ?? '23:59'
                        ];
                    });
                });
            }
            
            $groupedHoSos = $hoSoCacNgayKhac->groupBy(function($hoSo) {
                return $hoSo->ngay_hen ? \Carbon\Carbon::parse($hoSo->ngay_hen)->format('Y-m-d') : 'khong_co_ngay';
            })->map(function($hoSoGroup) {
                return $hoSoGroup->groupBy(function($hoSo) {
                    return $hoSo->dich_vu_id ?? 'null';
                })->map(function($hoSoGroupByDichVu) {
                    return $hoSoGroupByDichVu->sortBy(function($hoSo) {
                        return [
                            $hoSo->so_thu_tu ?? 9999,
                            $hoSo->gio_hen ?? '23:59'
                        ];
                    });
                });
            })->sortKeys();
        } else {
            $groupedHoSosHomNay = ['hom_nay' => $hoSoHomNay];
            $groupedHoSos = ['khac' => $hoSoCacNgayKhac];
        }
        
        // Debug: Log số lượng group
        // Convert Collection thành array nếu cần
        $groupedHoSosArray = $groupedHoSos instanceof \Illuminate\Support\Collection 
            ? $groupedHoSos->toArray() 
            : $groupedHoSos;
        
        \Log::info('Grouped HoSo count', [
            'user_id' => $currentUser->id,
            'group_count' => count($groupedHoSos),
            'group_keys' => $groupedHoSos instanceof \Illuminate\Support\Collection 
                ? $groupedHoSos->keys()->toArray() 
                : array_keys($groupedHoSos),
            'group_sizes' => $groupedHoSos instanceof \Illuminate\Support\Collection
                ? $groupedHoSos->map(function($group) { return $group->count(); })->toArray()
                : array_map(function($group) { return $group->count(); }, $groupedHoSos)
        ]);

        // Thống kê nhanh (theo quyền)
        $statsQuery = HoSo::query();
        if ($currentUser->isCanBo()) {
            $statsQuery->where('quan_tri_vien_id', $currentUser->id);
        } elseif ($currentUser->isAdminPhuong()) {
            $statsQuery->where('don_vi_id', $currentUser->don_vi_id);
        } elseif ($currentUser->isAdmin() && $request->filled('don_vi_id')) {
            $statsQuery->where('don_vi_id', $request->don_vi_id);
        }
        
        // Áp dụng filter dịch vụ cho thống kê
        if ($request->filled('dich_vu_id')) {
            $statsQuery->where('dich_vu_id', $request->dich_vu_id);
        }
        
        $stats = [
            'tong' => $statsQuery->count(),
            'da_tiep_nhan' => (clone $statsQuery)->where('trang_thai', HoSo::STATUS_RECEIVED)->count(),
            'dang_xu_ly' => (clone $statsQuery)->where('trang_thai', HoSo::STATUS_PROCESSING)->count(),
            'can_bo_sung' => (clone $statsQuery)->where('trang_thai', HoSo::STATUS_NEED_SUPPLEMENT)->count(),
            'hoan_tat' => (clone $statsQuery)->where('trang_thai', HoSo::STATUS_COMPLETED)->count(),
        ];

        // Lấy danh sách phường cho Admin tổng
        $donVis = [];
        if ($currentUser->isAdmin()) {
            $donVis = DonVi::orderBy('ten_don_vi')->get();
        }

        // Lấy danh sách cán bộ của phường để phân công (cho Admin phường và Admin tổng)
        $canBoList = [];
        if ($currentUser->isAdminPhuong()) {
            $canBoList = \App\Models\Admin::where('don_vi_id', $currentUser->don_vi_id)
                ->where('quyen', \App\Models\Admin::CAN_BO)
                ->orderBy('ho_ten')
                ->get();
        } elseif ($currentUser->isAdmin() && $request->filled('don_vi_id')) {
            $canBoList = \App\Models\Admin::where('don_vi_id', $request->don_vi_id)
                ->where('quyen', \App\Models\Admin::CAN_BO)
                ->orderBy('ho_ten')
                ->get();
        }

        // Lấy danh sách cán bộ để hiển thị tên trong group (cho Admin phường và Admin tổng)
        $canBoMap = [];
        $canBoStats = []; // Thống kê số hồ sơ đã xử lý của từng cán bộ
        if ($currentUser->isAdminPhuong() || ($currentUser->isAdmin() && $request->filled('don_vi_id'))) {
            $donViId = $currentUser->isAdminPhuong() ? $currentUser->don_vi_id : $request->don_vi_id;
            
            // Lấy tất cả cán bộ của phường
            $canBoMap = \App\Models\Admin::where('don_vi_id', $donViId)
                ->where('quyen', \App\Models\Admin::CAN_BO)
                ->pluck('ho_ten', 'id')
                ->toArray();
            
            // Bổ sung: Lấy thêm các cán bộ có trong hồ sơ nhưng có thể không còn trong phường
            // (để đảm bảo hiển thị đầy đủ tên cán bộ)
            $quanTriVienIds = $hoSos->pluck('quan_tri_vien_id')->filter()->unique();
            $missingCanBoIds = $quanTriVienIds->diff(array_keys($canBoMap));
            
            if ($missingCanBoIds->isNotEmpty()) {
                $missingCanBos = \App\Models\Admin::whereIn('id', $missingCanBoIds)
                    ->where('quyen', \App\Models\Admin::CAN_BO)
                    ->pluck('ho_ten', 'id')
                    ->toArray();
                $canBoMap = array_merge($canBoMap, $missingCanBos);
            }
            
            // Tính thống kê số hồ sơ đã xử lý (Hoàn tất) cho từng cán bộ
            // Áp dụng các filter tương tự như query chính (nếu có)
            $statsQuery = HoSo::where('don_vi_id', $donViId)
                ->where('trang_thai', HoSo::STATUS_COMPLETED)
                ->whereNotNull('quan_tri_vien_id');
            
            // Áp dụng filter dịch vụ (nếu có)
            if ($request->filled('dich_vu_id')) {
                $statsQuery->where('dich_vu_id', $request->dich_vu_id);
            }
            
            // Áp dụng filter ngày (nếu có)
            if ($request->filled('tu_ngay')) {
                $statsQuery->whereDate('ngay_hen', '>=', $request->tu_ngay);
            }
            if ($request->filled('den_ngay')) {
                $statsQuery->whereDate('ngay_hen', '<=', $request->den_ngay);
            }
            
            // Đếm số hồ sơ đã xử lý theo từng cán bộ
            $canBoStats = $statsQuery->selectRaw('quan_tri_vien_id, COUNT(*) as so_ho_so_da_xu_ly')
                ->groupBy('quan_tri_vien_id')
                ->pluck('so_ho_so_da_xu_ly', 'quan_tri_vien_id')
                ->toArray();
        }
        
        // Debug: Log canBoMap để kiểm tra
        \Log::info('canBoMap for display', [
            'user_id' => $currentUser->id,
            'user_type' => $currentUser->isCanBo() ? 'can_bo' : ($currentUser->isAdminPhuong() ? 'admin_phuong' : 'admin'),
            'don_vi_id' => $currentUser->isAdminPhuong() ? $currentUser->don_vi_id : ($request->don_vi_id ?? null),
            'canBoMap' => $canBoMap,
            'canBoMap_keys' => array_keys($canBoMap),
            'quan_tri_vien_ids_in_hoso' => $hoSos->pluck('quan_tri_vien_id')->filter()->unique()->toArray(),
            'grouped_keys' => $groupedHoSos instanceof \Illuminate\Support\Collection 
                ? $groupedHoSos->keys()->toArray() 
                : array_keys($groupedHoSos),
            'ho_so_with_quan_tri_vien_7' => $hoSos->where('quan_tri_vien_id', 7)->count()
        ]);

        // Lấy danh sách dịch vụ để hiển thị filter
        $services = Service::orderBy('ten_dich_vu')->get();
        
        // Tính số lượng hồ sơ theo dịch vụ (cho Admin phường và Cán bộ)
        $serviceCounts = [];
        if ($currentUser->isAdminPhuong() || $currentUser->isCanBo()) {
            // Tạo query riêng để đếm (không áp dụng filter dịch vụ, trạng thái, ngày, search)
            $countQuery = HoSo::query();
            
            // Áp dụng quyền tương tự như query chính
            if ($currentUser->isCanBo()) {
                $countQuery->where('quan_tri_vien_id', $currentUser->id);
            } elseif ($currentUser->isAdminPhuong()) {
                $countQuery->where('don_vi_id', $currentUser->don_vi_id);
            }
            
            // Đếm theo dịch vụ (chỉ đếm hồ sơ không bị hủy)
            $counts = (clone $countQuery)
                ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED)
                ->whereNotNull('dich_vu_id') // Chỉ đếm hồ sơ có dịch vụ
                ->selectRaw('dich_vu_id, COUNT(*) as count')
                ->groupBy('dich_vu_id')
                ->pluck('count', 'dich_vu_id')
                ->toArray();
            
            // Gán số lượng cho từng dịch vụ
            foreach ($services as $service) {
                $serviceCounts[$service->id] = $counts[$service->id] ?? 0;
            }
            
            // Tổng số hồ sơ (không bị hủy và có dịch vụ)
            $serviceCounts['all'] = (clone $countQuery)
                ->where('trang_thai', '!=', HoSo::STATUS_CANCELLED)
                ->whereNotNull('dich_vu_id')
                ->count();
        }

        return view('backend.ho-so.index', compact('hoSos', 'groupedHoSos', 'groupedHoSosHomNay', 'hoSoHomNay', 'stats', 'currentUser', 'donVis', 'canBoList', 'services', 'serviceCounts', 'canBoMap', 'canBoStats'));
    }

    /**
     * Xem chi tiết hồ sơ
     */
    public function show($id)
    {
        $currentUser = Auth::guard('admin')->user();
        
        $hoSo = HoSo::with(['dichVu', 'nguoiDung', 'donVi', 'quanTriVien', 'hoSoFields', 'rating'])
            ->findOrFail($id);

        // Kiểm tra quyền xem
        if ($currentUser->isCanBo() && $hoSo->quan_tri_vien_id != $currentUser->id) {
            abort(403, 'Bạn không có quyền xem hồ sơ này.');
        }
        if ($currentUser->isAdminPhuong() && $hoSo->don_vi_id != $currentUser->don_vi_id) {
            abort(403, 'Bạn không có quyền xem hồ sơ này.');
        }

        // Kiểm tra ngày hẹn - không cho xem nếu chưa đến ngày
        if ($hoSo->ngay_hen) {
            $ngayHen = \Carbon\Carbon::parse($hoSo->ngay_hen)->startOfDay();
            $ngayHienTai = \Carbon\Carbon::now()->startOfDay();
            
            if ($ngayHienTai->lt($ngayHen)) {
                abort(403, 'Chưa đến ngày xử lý hồ sơ. Ngày hẹn: ' . $ngayHen->format('d/m/Y'));
            }
        }

        return view('backend.ho-so.show', compact('hoSo'));
    }

    /**
     * Cập nhật trạng thái hồ sơ
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai' => 'required|in:' . implode(',', HoSo::STATUS_OPTIONS),
            'ghi_chu_xu_ly' => 'nullable|string|max:1000',
        ], [
            'trang_thai.required' => 'Vui lòng chọn trạng thái.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $currentUser = Auth::guard('admin')->user();
        $hoSo = HoSo::findOrFail($id);

        // CHỈ CÁN BỘ mới được cập nhật trạng thái
        if (!$currentUser->isCanBo()) {
            return back()->withErrors(['error' => 'Chỉ cán bộ mới được cập nhật trạng thái hồ sơ.']);
        }

        // Kiểm tra quyền cập nhật - chỉ cán bộ được phân công mới được cập nhật
        if ($hoSo->quan_tri_vien_id != $currentUser->id) {
            return back()->withErrors(['error' => 'Bạn chỉ có thể cập nhật hồ sơ được phân công cho mình.']);
        }

        // Kiểm tra ngày hẹn - chỉ được xử lý khi đến ngày hẹn
        if ($hoSo->ngay_hen) {
            $ngayHen = \Carbon\Carbon::parse($hoSo->ngay_hen)->startOfDay();
            $ngayHienTai = \Carbon\Carbon::now()->startOfDay();
            
            if ($ngayHienTai->lt($ngayHen)) {
                return back()->withErrors(['error' => 'Chưa đến ngày xử lý hồ sơ. Ngày hẹn: ' . $ngayHen->format('d/m/Y')]);
            }
        }

        $oldStatus = $hoSo->trang_thai;
        $hoSo->trang_thai = $request->trang_thai;
        
        // Thêm ghi chú xử lý vào ghi_chu hiện tại
        if ($request->filled('ghi_chu_xu_ly')) {
            $timestamp = now()->format('d/m/Y H:i');
            $newNote = "[{$timestamp}] {$currentUser->ho_ten}: {$request->ghi_chu_xu_ly}";
            $hoSo->ghi_chu = $hoSo->ghi_chu ? $hoSo->ghi_chu . "\n" . $newNote : $newNote;
        }

        $hoSo->save();

        // Gửi thông báo cho người dùng
        if ($oldStatus != $request->trang_thai) {
            $message = $this->getStatusChangeMessage($request->trang_thai, $hoSo->ma_ho_so);
            
            $thongBao = ThongBao::create([
                'ho_so_id' => $hoSo->id,
                'nguoi_dung_id' => $hoSo->nguoi_dung_id,
                'dich_vu_id' => $hoSo->dich_vu_id,
                'ngay_hen' => $hoSo->ngay_hen,
                'message' => $message,
                'is_read' => false, // Mặc định chưa đọc
            ]);

            // Load relationships và gửi email thông báo cho người dùng
            try {
                $thongBao->load(['NguoiDung', 'hoSo', 'dichVu']);
                if ($thongBao->NguoiDung && $thongBao->NguoiDung->email) {
                    Mail::to($thongBao->NguoiDung->email)->send(new NotificationMail($thongBao));
                }
            } catch (\Exception $e) {
                \Log::error('Lỗi gửi email thông báo: ' . $e->getMessage());
                // Không throw exception để không ảnh hưởng đến flow chính
            }
        }

        return back()->with('success', 'Cập nhật trạng thái hồ sơ thành công!');
    }

    /**
     * Phân công hồ sơ cho cán bộ (Admin phường)
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'quan_tri_vien_id' => 'nullable|exists:quan_tri_vien,id',
        ]);

        $currentUser = Auth::guard('admin')->user();
        
        if (!$currentUser->isAdminPhuong() && !$currentUser->isAdmin()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền phân công hồ sơ.'], 403);
            }
            return back()->withErrors(['error' => 'Bạn không có quyền phân công hồ sơ.']);
        }

        $hoSo = HoSo::findOrFail($id);

        if ($currentUser->isAdminPhuong() && $hoSo->don_vi_id != $currentUser->don_vi_id) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Bạn chỉ có thể phân công hồ sơ của phường mình.'], 403);
            }
            return back()->withErrors(['error' => 'Bạn chỉ có thể phân công hồ sơ của phường mình.']);
        }

        // Kiểm tra cán bộ có thuộc phường không (nếu có chọn cán bộ)
        if ($request->quan_tri_vien_id) {
            $canBo = \App\Models\Admin::find($request->quan_tri_vien_id);
            if ($canBo && $canBo->don_vi_id != $hoSo->don_vi_id) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Cán bộ không thuộc phường của hồ sơ này.'], 400);
                }
                return back()->withErrors(['error' => 'Cán bộ không thuộc phường của hồ sơ này.']);
            }
        }

        $hoSo->quan_tri_vien_id = $request->quan_tri_vien_id ?: null;
        $hoSo->save();

        if ($request->ajax()) {
            $canBoName = $hoSo->quanTriVien ? $hoSo->quanTriVien->ho_ten : 'Chưa phân công';
            return response()->json([
                'success' => true, 
                'message' => 'Phân công hồ sơ thành công!',
                'can_bo_name' => $canBoName
            ]);
        }

        return back()->with('success', 'Phân công hồ sơ thành công!');
    }

    /**
     * Hủy hồ sơ (Admin)
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'ly_do_huy' => 'required|string|max:500',
        ]);

        $currentUser = Auth::guard('admin')->user();
        $hoSo = HoSo::findOrFail($id);

        if (!$hoSo->canBeCancelled()) {
            return back()->withErrors(['error' => 'Không thể hủy hồ sơ đã hoàn tất hoặc đã hủy.']);
        }

        // Kiểm tra quyền
        if ($currentUser->isCanBo()) {
            return back()->withErrors(['error' => 'Cán bộ không có quyền hủy hồ sơ.']);
        }
        if ($currentUser->isAdminPhuong() && $hoSo->don_vi_id != $currentUser->don_vi_id) {
            return back()->withErrors(['error' => 'Bạn chỉ có thể hủy hồ sơ của phường mình.']);
        }

        $hoSo->trang_thai = HoSo::STATUS_CANCELLED;
        $hoSo->ly_do_huy = $request->ly_do_huy;
        $hoSo->cancelled_at = now();
        $hoSo->save();

        // Gửi thông báo
        $thongBao = ThongBao::create([
            'ho_so_id' => $hoSo->id,
            'nguoi_dung_id' => $hoSo->nguoi_dung_id,
            'dich_vu_id' => $hoSo->dich_vu_id,
            'ngay_hen' => $hoSo->ngay_hen,
            'message' => "Hồ sơ {$hoSo->ma_ho_so} đã bị hủy. Lý do: {$request->ly_do_huy}",
        ]);

        // Load relationships và gửi email thông báo cho người dùng
        try {
            $thongBao->load(['NguoiDung', 'hoSo', 'dichVu']);
            if ($thongBao->NguoiDung && $thongBao->NguoiDung->email) {
                Mail::to($thongBao->NguoiDung->email)->send(new NotificationMail($thongBao));
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi email thông báo: ' . $e->getMessage());
            // Không throw exception để không ảnh hưởng đến flow chính
        }

        return back()->with('success', 'Hủy hồ sơ thành công!');
    }

    /**
     * Lấy message thông báo theo trạng thái
     */
    private function getStatusChangeMessage($status, $maHoSo)
    {
        $messages = [
            HoSo::STATUS_RECEIVED => "Hồ sơ {$maHoSo} đã được tiếp nhận. Chúng tôi sẽ xử lý trong thời gian sớm nhất.",
            HoSo::STATUS_PROCESSING => "Hồ sơ {$maHoSo} đang được xử lý. Vui lòng theo dõi thông báo tiếp theo.",
            HoSo::STATUS_NEED_SUPPLEMENT => "Hồ sơ {$maHoSo} cần bổ sung thêm giấy tờ. Vui lòng kiểm tra chi tiết và liên hệ với chúng tôi.",
            HoSo::STATUS_COMPLETED => "Hồ sơ {$maHoSo} đã hoàn tất! Vui lòng đến nhận kết quả theo lịch hẹn. Đánh giá dịch vụ của chúng tôi nhé!",
            HoSo::STATUS_CANCELLED => "Hồ sơ {$maHoSo} đã bị hủy.",
        ];

        return $messages[$status] ?? "Trạng thái hồ sơ {$maHoSo} đã được cập nhật.";
    }
}
