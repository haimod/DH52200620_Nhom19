<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Thêm thư viện này để xử lý file/ảnh
use App\Models\User;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id, 
            // 'department' => 'nullable|string|max:100', // Không cần validate department vì không cho sửa
            'avatar' => 'nullable|image|max:2048', // Thêm validate ảnh: Phải là ảnh, tối đa 2MB
        ]);

        // 1. Cập nhật thông tin cơ bản
        $user->hoTen = $request->name; 
        $user->soDienThoai = $request->phone; 
        $user->email=$request->email;
        // Dòng này comment lại là đúng rồi (để chặn sửa phòng ban)
        // $user->phongBan = $request->department; 

        // 2. Xử lý upload Avatar (PHẦN BỔ SUNG QUAN TRỌNG)
        if ($request->hasFile('avatar')) {
            // (Tùy chọn) Xóa ảnh cũ nếu muốn tiết kiệm dung lượng
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Lưu ảnh mới vào thư mục 'avatars' trong storage/app/public
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path; 
        }
        
        $user->save();

        return back()->with('success', 'Cập nhật hồ sơ thành công!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng!');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}