<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class LoansRelationManager extends RelationManager
{
    protected static string $relationship = 'loans';
    protected static ?string $title = 'Riwayat Peminjaman';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('book.title')
                    ->label('Judul Buku'),

                TextColumn::make('loan_date')
                    ->label('Tanggal Pinjam')
                    ->date('d M Y'),

                TextColumn::make('return_date')
                    ->label('Tanggal Kembali')
                    ->formatStateUsing(function ($state) {
                        if (blank($state)) {
                            return '-';
                        }
                        return \Carbon\Carbon::parse($state)->format('d M Y');
                    }),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(
                        fn(string $state, $record) => ($state === 'borrowed' && now()->gt($record->due_date))
                            ? 'Terlambat'
                            : match ($state) {
                                'borrowed' => 'Dipinjam',
                                'returned' => 'Kembali',
                                default => $state,
                            }
                    )
                    ->color(
                        fn(string $state, $record): string => ($state === 'borrowed' && now()->gt($record->due_date))
                            ? 'danger'
                            : match ($state) {
                                'borrowed' => 'warning',
                                'returned' => 'success',
                                default => 'gray',
                            }
                    ),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
