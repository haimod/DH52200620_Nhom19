<?php

namespace App\Exports;

use App\Models\ThietBi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeviceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
    * Lấy dữ liệu từ Database
    */
    public function collection()
    {
        // Lấy tất cả thiết bị, sắp xếp theo mã
        return ThietBi::orderBy('maTB', 'asc')->get();
    }

    /**
    * Định nghĩa dòng tiêu đề (Header)
    */
    public function headings(): array
    {
        return [
            'Mã Tài Sản',
            'Tên Thiết Bị',
            'Loại Thiết Bị',
            'Số Serial',
            'Vị Trí',       // <--- Cột mới thêm
            'Tình Trạng',
            'Ngày Mua',
            'Hạn Bảo Hành',
        ];
    }

    /**
    * Map dữ liệu từng dòng (Format lại cho đẹp)
    */
    public function map($device): array
    {
        // Dịch trạng thái sang tiếng Việt
        $statuses = [
            'Available'   => 'Sẵn sàng',
            'In_Use'      => 'Đang sử dụng',
            'Maintenance' => 'Bảo trì',
            'Broken'      => 'Hỏng',
            'Liquidated'  => 'Đã thanh lý',
        ];

        return [
            $device->maTB,
            $device->tenTB,
            $device->maLoai,
            $device->soSerial ?? '---',
            $device->viTri ?? 'Kho Trung Tâm', // <--- Dữ liệu cột vị trí
            $statuses[$device->tinhTrang] ?? $device->tinhTrang,
            $device->ngayMua ? date('d/m/Y', strtotime($device->ngayMua)) : '',
            $device->hanBaoHanh ? date('d/m/Y', strtotime($device->hanBaoHanh)) : '',
        ];
    }

    /**
    * Style cho file Excel (In đậm dòng tiêu đề)
    */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}