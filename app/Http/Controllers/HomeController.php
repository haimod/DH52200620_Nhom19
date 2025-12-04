<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;// dùng thư viện Request
use App\Models\ThietBi; // 

class HomeController extends Controller
{
    public function index(Request $request)
    {
       
        // trả về 1 đối tượng có truy vấn
       // $query = ThietBi::query();
        $query = ThietBi::with('muonMoiNhat.phieuMuon.user', 'lichDatTruoc');
        // Xử lý tìm kiếm từ khóa
        // $request-> là 1 biến hệ thống như _GET[''] hay _POST  nó k phân biệt cách gửi phương thức giống PHP
        // kiểm tra xem có nhận đc keyword từ ô nhập liệu không
        if ($request->keyword) {
            $key = $request->keyword; // gán key = $keyword


           
            // dùng closure(hàm vô danh để truy vấn 
            $query->where(function($q) use ($key) {

                $q->where('tenTB', 'like', '%' . $key . '%')//WHERE (tenTB LIKE '%key%' OR maTB LIKE '%key%')
                  ->orWhere('maTB', 'like', '%' . $key . '%');
            });
        }

        // Xử lý lọc trạng thái
        if ($request->status && $request->status != 'all') {
            $query->where('tinhTrang', $request->status);
        }

        // 3. Lấy dữ liệu và sắp xếp (Sắp xếp theo mã cho đẹp)
       // $thietbi = $query->orderBy('maTB', 'asc')->get();
        $thietbi = $query->orderBy('maTB', 'asc')->paginate(5);
        // Trả về View
        return view('index', [
            'thietbi' => $thietbi,
            'keyword' => $request->keyword,
            'status'  => $request->status
        ]);
    }
}