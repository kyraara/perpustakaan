<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Member;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MemberResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Filament\Resources\MemberResource\RelationManagers\LoansRelationManager;
use Illuminate\Database\Eloquent\Model;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;


    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Anggota';
    protected static ?string $modelLabel = 'Anggota';
    protected static ?string $pluralModelLabel = 'Anggota';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $defaultSort = 'created_at desc';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nis')
                    ->label('Nomor Induk Siswa (NIS)')
                    ->required()
                    ->unique(table: 'members', column: 'nis', ignoreRecord: true),
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required(),
                Select::make('school_class_id')
                    ->relationship('schoolClass', 'name')
                    ->label('Kelas')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nama Kelas Baru')
                            ->required()
                            ->unique(table: 'school_classes', column: 'name'),
                    ]),
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->email()
                    ->unique(table: 'members', column: 'email', ignoreRecord: true),
                TextInput::make('phone_number')
                    ->label('Nomor Telepon'),
                Textarea::make('address')
                    ->label('Alamat'),
                FileUpload::make('photo')
                    ->label('Foto Anggota')
                    ->image()
                    ->columnSpanFull()
                    ->directory('member-photos'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo') // <-- Tambahkan kolom foto
                    ->label('Foto')
                    ->circular(), // Membuat gambar menjadi bulat
                TextColumn::make('nis') // <-- Tambahkan kolom NIS
                    ->label('NIS')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('schoolClass.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->label('Nomor Telepon'),
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
                                ->modalHeading('Hapus Anggota')
                                ->modalDescription('Apakah Anda yakin ingin menghapus anggota yang dipilih? Semua riwayat peminjaman terkait anggota ini juga akan hilang.')
                                ->modalSubmitActionLabel('Ya, Hapus'),
                        ]),
                    ]
                    : []
            );
    }

    public static function getRelations(): array
    {
        return [
            LoansRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'nis', 'email', 'schoolClass.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'NIS' => $record->nis,
            'Nama' => $record->name,
            'kelas' => $record->schoolClass ? $record->schoolClass->name : 'Tidak ada kelas',
        ];
    }
}
