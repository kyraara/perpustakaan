<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Loan;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LoanResource\Pages;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LoanResource\RelationManagers;
use Filament\Tables\Actions\Action;
use Filament\Navigation\NavigationItem;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;
    protected static ?string $defaultSort = 'created_at desc';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Peminjaman';
    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?string $pluralModelLabel = 'Peminjaman';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Dropdown untuk memilih Buku berdasarkan judulnya
                Select::make('book_id')
                    ->relationship('book', 'title')
                    ->label('Buku')
                    ->searchable()
                    ->preload()
                    ->required(),

                // Dropdown untuk memilih Anggota berdasarkan namanya
                Select::make('member_id')
                    ->relationship('member', 'name')
                    ->label('Anggota')
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('loan_date')
                    ->label('Tanggal Pinjam')
                    ->default(now()) // Otomatis diisi tanggal hari ini
                    ->required(),

                DatePicker::make('due_date')
                    ->label('Tanggal Jatuh Tempo')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan judul buku dari relasi
                TextColumn::make('book.title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable(),

                // Menampilkan nama anggota dari relasi
                TextColumn::make('member.name')
                    ->label('Nama Peminjam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('loan_date')
                    ->label('Tanggal Pinjam')
                    ->date(),


                // Menggunakan Badge agar status terlihat lebih bagus
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // TAMBAHKAN BLOK KODE AKSI INI
                Action::make('return')
                    ->label('Kembalikan')
                    ->icon('heroicon-s-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Loan $record) {
                        $record->status = 'returned';
                        $record->return_date = now();
                        $record->save();
                    })
                    // Tombol ini hanya akan muncul jika statusnya masih 'borrowed'
                    ->visible(
                        fn(Loan $record): bool =>
                        $record->status === 'borrowed' && auth()->user()->role === 'admin'
                    ),
            ])
            ->bulkActions(
                // Tambahkan pengecekan peran di sini
                auth()->user()->role === 'admin'
                    ? [ // JIKA admin, tampilkan aksi massal
                        Tables\Actions\BulkActionGroup::make([
                            Tables\Actions\DeleteBulkAction::make(),
                        ]),
                    ]
                    : [] // JIKA BUKAN admin, kosongkan (tidak ada aksi)
            );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
            'report' => Pages\LoanReport::route('/report'),
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            // Ini akan membuat menu utama untuk CRUD (Create, Read, Update, Delete)
            NavigationItem::make(static::$navigationLabel) // Mengambil nama dari properti
                ->group(static::$navigationGroup) // Mengambil grup dari properti
                ->icon(static::$navigationIcon) // Mengambil ikon dari properti
                ->activeIcon(static::$activeNavigationIcon)
                ->isActiveWhen(fn() => request()->routeIs(static::getRouteBaseName() . '.*') && !request()->routeIs(static::getRouteBaseName() . '.report'))
                ->url(static::getUrl('index')),

            // Ini akan membuat menu Laporan Peminjaman yang sudah kita buat sebelumnya
            NavigationItem::make('Laporan Peminjaman')
                ->url(static::getUrl('report'))
                ->icon('heroicon-o-document-chart-bar')
                ->group('Laporan'),
        ];
    }
}
