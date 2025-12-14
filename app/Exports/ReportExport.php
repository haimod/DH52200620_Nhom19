<?php

namespace App\Exports;

use App\Models\ThietBi;
use App\Models\PhieuMuon;
use App\Models\ChiTietMuon; // Cần model này để lấy lịch sử chi tiết
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets; // Quan trọng: Để xuất nhiều sheet
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// --- CLASS CHÍNH: ĐIỀU PHỐI CÁC SHEET ---
class ReportExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new DashboardSheet(),      // Sheet 1: Thống kê
            new DeviceListSheet(),     // Sheet 2: Danh sách thiết bị
            new BorrowHistorySheet(),  // Sheet 3: Lịch sử mượn trả
        ];
    }
}

// --- SHEET 1: THỐNG KÊ TỔNG QUAN ---
class DashboardSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        // Tính toán số liệu
        $total = ThietBi::count();
        $inUse = ThietBi::where('tinhTrang', 'In_Use')->count();
        $broken = ThietBi::where('tinhTrang', 'Broken')->count();
        $available = ThietBi::where('tinhTrang', 'Available')->count();
        
        // Đếm tổng số lượt mượn trong hệ thống
        $totalBorrows = PhieuMuon::count();

        return collect([
            ['Tổng số thiết bị', $total],
            ['Sẵn sàng sử dụng', $available],
            ['Đang cho mượn', $inUse],
            ['Hư hỏng / Bảo trì', $broken],
            ['', ''], // Dòng trống
            ['Tổng số phiếu mượn đã tạo', $totalBorrows],
            ['Ngày xuất báo cáo', date('d/m/Y H:i:s')],
        ]);
    }

    public function headings(): array
    {
        return ['CHỈ SỐ', 'SỐ LƯỢNG'];
    }

    public function title(): string
    {
        return 'Thống kê tổng quan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']]], // Header
            'A1:B1' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]], // Màu nền xanh
        ];
    }
}

// --- SHEET 2: DANH SÁCH THIẾT BỊ (Code cũ của bạn) ---
class DeviceListSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return ThietBi::orderBy('maTB', 'asc')->get();
    }

    public function headings(): array
    {
        return ['Mã Tài Sản', 'Tên Thiết Bị', 'Vị Trí', 'Tình Trạng', 'Ngày Mua'];
    }

    public function map($device): array
    {
        $statuses = [
            'Available' => 'Sẵn sàng', 'In_Use' => 'Đang sử dụng',
            'Maintenance' => 'Bảo trì', 'Broken' => 'Hỏng', 'Liquidated' => 'Thanh lý'
        ];

        return [
            $device->maTB,
            $device->tenTB,
            $device->viTri ?? 'Kho Trung Tâm',
            $statuses[$device->tinhTrang] ?? $device->tinhTrang,
            $device->ngayMua ? date('d/m/Y', strtotime($device->ngayMua)) : '',
        ];
    }

    public function title(): string
    {
        return 'Danh sách thiết bị';
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}

// --- SHEET 3: LỊCH SỬ MƯỢN TRẢ (Mới thêm) ---
class BorrowHistorySheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Lấy chi tiết mượn, kèm thông tin Phiếu + User + Thiết bị
        return ChiTietMuon::with(['phieuMuon.user', 'thietBi'])
            ->orderBy('created_at', 'desc') // Mới nhất lên đầu
            ->get();
    }

    public function headings(): array
    {
        return ['Mã Phiếu', 'Người Mượn', 'Phòng Ban', 'Thiết Bị', 'Ngày Mượn', 'Ngày Trả (Dự kiến)', 'Trạng Thái'];
    }

    public function map($ct): array
    {
        $phieu = $ct->phieuMuon;
        $user = $phieu->user;
        $tb = $ct->thietBi;

        // Dịch trạng thái phiếu
        $statusMap = [
            'Pending' => 'Chờ duyệt', 'Active' => 'Đang mượn',
            'Closed' => 'Đã trả', 'Cancelled' => 'Đã hủy', 'Rejected' => 'Từ chối'
        ];

        return [
            $phieu->maPM ?? $phieu->id,
            $user->hoTen ?? 'User đã xóa',
            $user->phongBan ?? '---',
            $tb->tenTB ?? 'TB đã xóa',
            date('H:i d/m/Y', strtotime($phieu->ngayMuon)),
            date('H:i d/m/Y', strtotime($phieu->ngayTraDuKien)),
            $statusMap[$phieu->trangThai] ?? $phieu->trangThai,
        ];
    }

    public function title(): string
    {
        return 'Lịch sử sử dụng';
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}