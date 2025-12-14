<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // <--- BẮT BUỘC PHẢI CÓ DÒNG NÀY

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // 1. TÌM KIẾM
        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('hoTen', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('tenDangNhap', 'like', "%{$keyword}%")
                  ->orWhere('maNV', 'like', "%{$keyword}%")
                  ->orWhere('soDienThoai', 'like', "%{$keyword}%")
                  ->orWhere('id', $keyword);
            });
        }

        // 2. LỌC THEO VAI TRÒ
        if ($request->filled('role')) {
            $role = $request->role;
            if ($role == 'User' || $role == 'NhanVien') {
                $query->whereIn('vaiTro', ['User', 'NhanVien', 'user', 'nhanvien']);
            } elseif ($role == 'Admin') {
                $query->whereIn('vaiTro', ['Admin', 'QuanTri', 'admin']);
            } else {
                $query->where('vaiTro', $role);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    // --- CHỨC NĂNG THÊM MỚI ---
    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'hoTen' => 'required|string|max:255',
            'tenDangNhap' => 'required|string|unique:users,tenDangNhap|max:50',
            'maNV' => 'required|string|unique:users,maNV|max:20',
            'email' => 'required|email|unique:users,email',
            'soDienThoai' => 'nullable|string|max:15',
            'password' => 'required|min:6',
            'vaiTro' => 'required|in:Admin,NhanVien', 
            'phongBan' => 'nullable|string|max:255' 
        ], [
            'tenDangNhap.unique' => 'Tên đăng nhập này đã được sử dụng.',
            'maNV.unique' => 'Mã nhân viên đã tồn tại.',
            'email.unique' => 'Email này đã có người dùng.'
        ]);

        // 2. Lưu vào Database
        User::create([
            'hoTen' => $request->hoTen,
            'tenDangNhap' => $request->tenDangNhap,
            'maNV' => $request->maNV,
            'soDienThoai' => $request->soDienThoai,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'vaiTro' => $request->vaiTro,
            'phongBan' => $request->phongBan, 
            'trangThai' => 'Active', // Mặc định là Hoạt động
            'avatar' => null
        ]);

        return back()->with('success', 'Thêm nhân sự mới thành công!');
    }

    // --- CHỨC NĂNG CẬP NHẬT ---
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'hoTen' => 'required|string|max:255',
            'tenDangNhap' => ['required', Rule::unique('users', 'tenDangNhap')->ignore($user->id)],
            'maNV' => ['required', Rule::unique('users', 'maNV')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'soDienThoai' => 'nullable|string|max:15',
            'vaiTro' => 'required|in:Admin,NhanVien',
            'phongBan' => 'nullable|string|max:255',
        ]);

        $data = [
            'hoTen' => $request->hoTen,
            'tenDangNhap' => $request->tenDangNhap,
            'maNV' => $request->maNV,
            'soDienThoai' => $request->soDienThoai,
            'email' => $request->email,
            'vaiTro' => $request->vaiTro,
            'phongBan' => $request->phongBan,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    // --- CHỨC NĂNG KHÓA / MỞ KHÓA TÀI KHOẢN ---
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Sử dụng Auth::id() thay vì auth()->id() để tránh lỗi IDE và đảm bảo chính xác
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Bạn không thể khóa tài khoản đang đăng nhập!');
        }

        // Đổi trạng thái: Active <-> Blocked
        $user->trangThai = ($user->trangThai == 'Active') ? 'Blocked' : 'Active';
        $user->save();

        $msg = $user->trangThai == 'Active' ? 'Đã mở khóa tài khoản.' : 'Đã khóa tài khoản thành công.';
        return back()->with('success', $msg);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Sử dụng Auth::id() để kiểm tra xóa chính mình
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Bạn không thể xóa tài khoản đang đăng nhập!');
        }

        $user->delete();
        return back()->with('success', 'Đã xóa nhân sự khỏi hệ thống.');
    }
}