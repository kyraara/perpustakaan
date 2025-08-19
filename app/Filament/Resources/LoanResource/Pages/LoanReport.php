<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Models\Loan;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\LoanResource;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table; // <-- Diperbaiki
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;


class LoanReport extends ListRecords
{
    protected static string $resource = LoanResource::class;
    protected ?string $heading = 'Laporan Peminjaman';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->label('Ekspor ke Excel')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename('Laporan Peminjaman - ' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                    ])
            ])
            ->columns([
                TextColumn::make('book.title')->label('Judul Buku'),
                TextColumn::make('member.name')->label('Nama Peminjam'),
                TextColumn::make('loan_date')->label('Tgl Pinjam')->date(),
                TextColumn::make('due_date')->label('Jatuh Tempo')->date(),
                TextColumn::make('return_date')
                    ->label('Tgl Kembali')
                    ->formatStateUsing(function ($state) {
                        // Cek jika nilai dari database (state) kosong/null
                        if (blank($state)) {
                            return '-';
                        }
                        // Jika tidak kosong, format sebagai tanggal
                        return \Carbon\Carbon::parse($state)->format('d M Y');
                    }),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(function (string $state, $record) {
                        // Jika statusnya dipinjam DAN sudah lewat jatuh tempo
                        if ($state === 'borrowed' && now()->gt($record->due_date)) {
                            return 'Terlambat'; // Tampilkan "Terlambat"
                        }

                        // Terjemahkan status lainnya
                        return match ($state) {
                            'borrowed' => 'Dipinjam',
                            'returned' => 'Kembali',
                            default => $state,
                        };
                    })
                    ->color(function (string $state, $record): string {
                        // Jika statusnya dipinjam DAN sudah lewat jatuh tempo
                        if ($state === 'borrowed' && now()->gt($record->due_date)) {
                            return 'danger'; // Beri warna merah
                        }

                        // Atur warna untuk status lainnya
                        return match ($state) {
                            'borrowed' => 'warning',
                            'returned' => 'success',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->color(
                        fn(Loan $record): string =>
                        // Logika pengecekan:
                        // Jika tanggal jatuh tempo sudah lewat DAN statusnya masih 'borrowed'
                        now()->gt($record->due_date) && $record->status === 'borrowed'
                            ? 'danger' // Maka warnanya merah (danger)
                            : 'gray'   // Jika tidak, warnanya abu-abu (gray)
                    ),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')->label('Dari Tanggal'),
                        DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('loan_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('loan_date', '<=', $date),
                            );
                    }),
            ]);
    }
}
