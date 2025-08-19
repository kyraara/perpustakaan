<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\LoanResource;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestLoans extends BaseWidget
{

    protected static ?string $heading = 'Peminjaman Terbaru';
    protected static ?int $sort = 2;
    public function table(Table $table): Table
    {
        return $table
            ->query(LoanResource::getModel()::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('book.title')
                    ->label('Judul Buku'),
                Tables\Columns\TextColumn::make('member.name')
                    ->label('Peminjam'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Pinjam')
                    ->since(),
            ]);
    }
}
