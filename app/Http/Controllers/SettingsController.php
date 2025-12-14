<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    // Hiển thị trang cài đặt
    public function index()
    {
        return view('settings.index');
    }

    // 1. Cập nhật hồ sơ cá nhân
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate dữ liệu
        $request->validate([
            'hoTen' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id, // Bỏ qua check unique với chính mình
            'soDienThoai' => 'nullable|string|max:15',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Tối đa 2MB
        ], [
            'email.unique' => 'Email này đã được sử dụng bởi người khác.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB.',
            'avatar.image' => 'File tải lên phải là hình ảnh.',
        ]);

        $data = [
            'hoTen' => $request->hoTen,
            'email' => $request->email,
            'soDienThoai' => $request->soDienThoai,
        ];

        // Xử lý upload ảnh đại diện
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu có (để tiết kiệm dung lượng)
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Lưu ảnh mới vào thư mục public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        // Cập nhật Database (Cần đảm bảo biến $user được type hint đúng hoặc dùng Model)
        /** @var \App\Models\User $user */
        $user->update($data);

        return back()->with('success', 'Cập nhật hồ sơ thành công!');
    }

    // 2. Đổi mật khẩu
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password', // Laravel có rule check pass hiện tại
            'new_password' => 'required|string|min:6|confirmed|different:current_password',
        ], [
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',
            'new_password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'new_password.different' => 'Mật khẩu mới không được trùng với mật khẩu cũ.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công! Vui lòng ghi nhớ mật khẩu mới.');
    }

    // 3. Cập nhật cấu hình hệ thống (Dành cho Admin)
    public function updateSystem(Request $request)
    {
        // Kiểm tra quyền Admin lần nữa cho chắc
        if (!in_array(Auth::user()->vaiTro, ['Admin', 'admin', 'QuanTri', 'Super Admin'])) {
            return back()->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }

        $request->validate([
            'max_borrow_days' => 'required|integer|min:1',
            'max_devices_per_user' => 'required|integer|min:1',
            'admin_email' => 'required|email'
        ]);

        // Ở đây bạn có thể lưu vào bảng 'settings' trong DB hoặc file .env
        // Demo: Tạm thời chỉ thông báo thành công
        
        return back()->with('success', 'Đã lưu cấu hình hệ thống.');
    }
}