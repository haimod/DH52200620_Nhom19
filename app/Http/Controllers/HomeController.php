<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // Thêm (Request $request) để nhận dữ liệu từ form tìm kiếm
    public function index(Request $request)
    {
        // 1. Khởi tạo truy vấn
        $query = DB::table('thietbi');

        // 2. Xử lý tìm kiếm từ khóa (Nếu có nhập)
        if ($request->keyword) {
            $key = $request->keyword;
            $query->where(function($q) use ($key) {
                $q->where('tenTB', 'like', '%' . $key . '%')
                  ->orWhere('maTB', 'like', '%' . $key . '%');
            });
        }

        // 3. Xử lý lọc trạng thái (Nếu có chọn)
        if ($request->status && $request->status != 'all') {
            $query->where('tinhTrang', $request->status);
        }

        // 4. Lấy dữ liệu cuối cùng
        $thietbi = $query->get();

        // 5. Trả về View cùng với các biến cần thiết (để không bị lỗi Undefined variable)
        return view('index', [
            'thietbi' => $thietbi,
            'keyword' => $request->keyword, // Gửi lại từ khóa để hiện lên ô input
            'status'  => $request->status   // Gửi lại trạng thái để hiện lên dropdown
        ]);
    }
}