<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Debug: Log thông tin request
        \Log::info('CheckAuth Middleware', [
            'route' => $request->route()->getName(),
            'url' => $request->url(),
            'user_quyen' => Auth::guard('admin')->check() ? Auth::guard('admin')->user()->quyen : 'not_logged_in'
        ]);

        // Kiểm tra nếu user đang đăng nhập bằng guard admin
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            
            // Route chỉ dành cho Admin phường (quyen = 2) - chặn Admin tổng và Cán bộ
            $adminPhuongOnlyRoutes = [
                'payments.index',
                'payments.show',
                'service-phuong.*',
                'admin-phuong.*',
                'statistics.*',
            ];
            
            // Route chỉ dành cho Cán bộ (quyen = 0) - chặn Admin tổng và Admin phường
            $canBoOnlyRoutes = [
                // Không có routes riêng cho cán bộ
            ];
            
            // Route chỉ dành cho Admin tổng (quyen = 1) - chặn Admin phường và Cán bộ
            $adminTongOnlyRoutes = [
                'admin.settings.*',
            ];
            
            // Chặn Admin tổng và Cán bộ truy cập route của Admin phường
            if ($admin->quyen != 2) { // Không phải Admin phường
                foreach ($adminPhuongOnlyRoutes as $route) {
                    if ($request->routeIs($route)) {
                        abort(404, 'Bạn không có quyền truy cập vào trang này.');
                    }
                }
            }
            
            // Chặn Admin tổng và Admin phường truy cập route của Cán bộ
            if ($admin->quyen != 0) { // Không phải Cán bộ
                foreach ($canBoOnlyRoutes as $route) {
                    if ($request->routeIs($route)) {
                        abort(404, 'Bạn không có quyền truy cập vào trang này.');
                    }
                }
            }
            
            // Chặn Admin phường và Cán bộ truy cập route của Admin tổng
            if ($admin->quyen != 1) { // Không phải Admin tổng
                foreach ($adminTongOnlyRoutes as $route) {
                    if ($request->routeIs($route)) {
                        abort(404, 'Bạn không có quyền truy cập vào trang này.');
                    }
                }
            }
        }

        return $next($request);
    }


}
