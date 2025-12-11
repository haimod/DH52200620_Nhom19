<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('keyword')) {
            $query->where(function($q) use ($request) {
                $q->where('hoTen', 'like', '%' . $request->keyword . '%')
                  ->orWhere('email', 'like', '%' . $request->keyword . '%');
            });
        }

        // Lọc theo vai trò
        if ($request->filled('role')) {
            $query->where('vaiTro', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hoTen' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'vaiTro' => 'required|in:Admin,User', // Tùy chỉnh theo roles của bạn
            'phongBan' => 'nullable|string'
        ]);

        User::create([
            'hoTen' => $request->hoTen,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'vaiTro' => $request->vaiTro,
            'phongBan' => $request->phongBan,
            'avatar' => null // Xử lý upload ảnh sau nếu cần
        ]);

        return back()->with('success', 'Thêm nhân sự mới thành công!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'hoTen' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'vaiTro' => 'required|in:Admin,User',
        ]);

        $data = [
            'hoTen' => $request->hoTen,
            'email' => $request->email,
            'vaiTro' => $request->vaiTro,
            'phongBan' => $request->phongBan,
        ];

        // Nếu có nhập pass mới thì mới cập nhật
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // // Không cho phép xóa chính mình
        // if ($user->id == auth()->id()) {
        //     return back()->with('error', 'Bạn không thể xóa tài khoản đang đăng nhập!');
        // }

        $user->delete();
        return back()->with('success', 'Đã xóa nhân sự khỏi hệ thống.');
    }
}