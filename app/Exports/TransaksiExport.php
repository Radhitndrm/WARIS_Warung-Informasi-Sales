<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransaksiExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function __construct(
        protected ?string $from = null,
        protected ?string $to   = null,
    ) {}

    public function title(): string
    {
        return 'Riwayat Transaksi';
    }

    public function headings(): array
    {
        return ['No', 'Invoice', 'Tanggal', 'Total (Rp)', 'Metode Bayar', 'Status'];
    }

    public function collection()
    {
        $orders = Order::with('payment')
            ->when($this->from, fn($q) => $q->whereDate('created_at', '>=', $this->from))
            ->when($this->to,   fn($q) => $q->whereDate('created_at', '<=', $this->to))
            ->latest()
            ->get();

        return $orders->map(fn($order, $i) => [
            'no'      => $i + 1,
            'invoice' => $order->invoice_no,
            'tanggal' => $order->created_at->format('d M Y H:i'),
            'total'   => $order->total,
            'metode'  => ucfirst(optional($order->payment)->method ?? '-'),
            'status'  => ucfirst($order->status),
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 22,
            'C' => 22,
            'D' => 18,
            'E' => 16,
            'F' => 14,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF394766'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}