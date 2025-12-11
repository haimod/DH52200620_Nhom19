<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ThietBi; 

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $status = $request->input('status');
        
        $query = ThietBi::orderBy('created_at', 'desc');

        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('tenTB', 'like', "%{$keyword}%")
                  ->orWhere('maTB', 'like', "%{$keyword}%")
                  ->orWhere('soSerial', 'like', "%{$keyword}%");
            });
        }

        if ($status) {
            $query->where('tinhTrang', $status);
        }

        $devices = $query->paginate(5);

        return view('admin.devices.index', compact('devices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tenTB' => 'required|string|max:255',
            // SỬA LỖI Ở ĐÂY: đổi 'thiet_bi' thành 'thietbi'
            'maTB' => 'required|string|unique:thietbi,maTB', 
            'loaiTB' => 'required',
            'soSerial' => 'nullable|string|max:100',
            'ngayMua' => 'nullable|date',
            'hanBaoHanh' => 'nullable|date|after_or_equal:ngayMua',
        ], [
            'maTB.unique' => 'Mã tài sản này đã tồn tại!',
            'hanBaoHanh.after_or_equal' => 'Hạn bảo hành phải sau ngày mua!',
        ]);

        $data = [
            'maTB' => $request->maTB,
            'tenTB' => $request->tenTB,
            'maLoai' => $request->loaiTB,
            'tinhTrang' => $request->tinhTrang,
            'soSerial' => $request->soSerial,
            'ngayMua' => $request->ngayMua,
            'hanBaoHanh' => $request->hanBaoHanh,
        ];

        ThietBi::create($data);

        return back()->with('success', 'Đã nhập kho thiết bị thành công!');
    }

    public function update(Request $request, $id)
    {
        $device = ThietBi::findOrFail($id);

        $request->validate([
            'tenTB' => 'required|string|max:255',
            'loaiTB' => 'required',
            'soSerial' => 'nullable|string|max:100',
            'ngayMua' => 'nullable|date',
            'hanBaoHanh' => 'nullable|date|after_or_equal:ngayMua',
        ], [
            'hanBaoHanh.after_or_equal' => 'Hạn bảo hành phải sau ngày mua!',
        ]);
        
        $device->update([
            'tenTB' => $request->tenTB,
            'maLoai' => $request->loaiTB,
            'tinhTrang' => $request->tinhTrang,
            'soSerial' => $request->soSerial,
            'ngayMua' => $request->ngayMua,
            'hanBaoHanh' => $request->hanBaoHanh,
        ]);

        return back()->with('success', 'Cập nhật thông tin thiết bị thành công!');
    }

    public function destroy($id)
    {
        $device = ThietBi::findOrFail($id);
        $device->delete();
        return back()->with('success', 'Đã xóa thiết bị thành công!');
    }
}