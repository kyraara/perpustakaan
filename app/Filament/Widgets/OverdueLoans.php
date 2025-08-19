<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\LoanResource;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueLoans extends BaseWidget
{
    protected static ?string $heading = 'Peminjaman Melewati Jatuh Tempo';
    protected static ?int $sort = 3;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                LoanResource::getModel()::query()
                    ->where('status', 'borrowed')
                    ->where('due_date', '<', now()) // <-- Kunci Logika
            )
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Judul Buku'),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Peminjam'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->color('danger'),
            ]);
    }
}
