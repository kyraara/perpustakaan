<?php

namespace App\Filament\Widgets; // <-- Diperbaiki

use App\Models\Loan; // <-- Diperbaiki
use Filament\Widgets\ChartWidget; // <-- Diperbaiki
use Illuminate\Support\Facades\DB; // <-- Diperbaiki

class LoansChart extends ChartWidget
{
    protected static ?string $heading = 'Peminjaman Buku per Bulan';
    protected static ?string $maxHeight = '300px';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        // Mengambil data peminjaman, dihitung & dikelompokkan per bulan untuk tahun ini
        $data = Loan::select(DB::raw('MONTH(loan_date) as month'), DB::raw('count(*) as count'))
            ->whereYear('loan_date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $values = [];
        // Memformat data agar sesuai dengan format grafik
        for ($i = 1; $i <= 12; $i++) {
            $month = date('M', mktime(0, 0, 0, $i, 1));
            $count = $data->firstWhere('month', $i)?->count ?? 0;

            $labels[] = $month;
            $values[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Peminjaman Buku',
                    'data' => $values,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Tipe grafiknya adalah 'line' (garis)
    }
}
