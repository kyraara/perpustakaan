<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Book;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BookResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookResource\RelationManagers;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Buku';
    protected static ?string $modelLabel = 'Buku';
    protected static ?string $pluralModelLabel = 'Buku';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $defaultSort = 'created_at desc';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Membuat grup layout untuk form
                Section::make('Informasi Utama Buku')
                    ->schema([
                        // Field untuk Judul
                        TextInput::make('title')
                            ->label('Judul Buku')
                            ->required()
                            ->maxLength(255),

                        // Field untuk Penulis
                        TextInput::make('author')
                            ->label('Penulis')
                            ->required()
                            ->maxLength(255),

                        // Field untuk ISBN
                        TextInput::make('isbn')
                            ->label('ISBN')
                            ->unique(table: 'books', column: 'isbn', ignoreRecord: true)
                            ->maxLength(255),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Kategori')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Kategori Baru')
                                    ->required()
                                    ->unique(table: 'categories', column: 'name'),
                            ]),

                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Sampul Buku')
                            ->image() // Hanya menerima file gambar
                            ->directory('book-covers') // Folder penyimpanan
                            ->columnSpanFull(), // Mengambil lebar penuh
                    ])->columns(2), // Layout 2 kolom

                Section::make('Detail Penerbitan')
                    ->schema([
                        // Field untuk Penerbit
                        TextInput::make('publisher')
                            ->label('Penerbit'),

                        // Field untuk Tahun Terbit
                        TextInput::make('published_year')
                            ->label('Tahun Terbit')
                            ->numeric() // Hanya menerima angka
                            ->minValue(1000)
                            ->maxValue(date('Y')),

                        // Field untuk Stok
                        TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(0),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Sampul'),

                TextColumn::make('title')
                    ->label('Judul Buku')
                    ->searchable()
                    ->sortable(),

                // Menampilkan kolom Penulis
                TextColumn::make('author')
                    ->label('Penulis')
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                // Menampilkan kolom Stok
                TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions(
                auth()->user()->role === 'admin'
                    ? [
                        Tables\Actions\BulkActionGroup::make([
                            Tables\Actions\DeleteBulkAction::make()
                                ->modalHeading('Hapus Buku')
                                ->modalDescription('Apakah Anda yakin ingin menghapus Buku yang dipilih? Semua riwayat peminjaman terkait anggota ini juga akan hilang.')
                                ->modalSubmitActionLabel('Ya, Hapus'),
                        ]),
                    ]
                    : []
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
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'author', 'isbn', 'category.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Penulis' => $record->author,
            'ISBN' => $record->isbn,
            'Kategori' => $record->category ? $record->category->name : 'Tidak ada kategori',
            'Stok' => $record->stock,
        ];
    }
}
