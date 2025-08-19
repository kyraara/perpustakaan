<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Judul Buku', Book::count())
                ->description('Jumlah semua judul buku di perpustakaan')
                ->descriptionIcon('heroicon-o-book-open')
                ->color('success'),

            Stat::make('Total Anggota Terdaftar', Member::count())
                ->description('Jumlah semua anggota yang terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),

            Stat::make('Buku Sedang Dipinjam', Loan::where('status', 'borrowed')->count())
                ->description('Jumlah buku yang saat ini belum dikembalikan')
                // Perbaikan ada di baris ini
                ->descriptionIcon('heroicon-o-document-text')
                ->color('warning'),
        ];
    }
}
