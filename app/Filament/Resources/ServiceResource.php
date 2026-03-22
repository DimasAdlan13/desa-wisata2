<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\ApprovalService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Layanan Wisata';
    protected static ?string $navigationGroup = 'Manajemen Layanan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Info Layanan')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Layanan')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, Forms\Set $set) =>
                        $set('slug', Str::slug($state) . '-' . Str::random(5))
                    ),
                Forms\Components\TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true),
                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('user_id')
                    ->label('Admin Pengelola')
                    ->relationship('user', 'name', fn(Builder $q) =>
                        $q->where('role', User::ROLE_ADMIN_LAYANAN)->where('is_approved', true)
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->visible(fn() => auth()->user()->isSuperAdmin()),
                Forms\Components\TextInput::make('price')
                    ->label('Harga (Rp)')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('quota_per_day')
                    ->label('Kuota per Hari (Pax)')
                    ->required()
                    ->numeric()
                    ->default(10),
                Forms\Components\TextInput::make('location')->label('Lokasi'),
                Forms\Components\TextInput::make('contact_person')->label('Kontak Person'),
                Forms\Components\RichEditor::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),

            Forms\Components\Section::make('Foto Layanan')->schema([
                Forms\Components\Repeater::make('photos')
                    ->relationship()
                    ->schema([
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto')
                            ->image()
                            ->maxSize(2048)
                            ->disk('public')
                            ->directory('services')
                            ->required(),
                        Forms\Components\Toggle::make('is_primary')->label('Foto Utama'),
                        Forms\Components\TextInput::make('order')->label('Urutan')->numeric()->default(0),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Form Dinamis (JSON Schema)')
                ->description('Definisikan field yang harus diisi user saat booking. Biarkan kosong jika tidak ada form tambahan.')
                ->schema([
                    Forms\Components\KeyValue::make('form_schema')
                        ->label('Form Fields')
                        ->keyLabel('Field Key')
                        ->valueLabel('Label / Tipe (misal: nama_kapal|text, dibuat_untuk|textarea)')
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                if (auth()->user()->isAdminLayanan()) {
                    $query->where('user_id', auth()->id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Layanan')->searchable()->sortable()->limit(40),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->badge(),
                Tables\Columns\TextColumn::make('formattedPrice')->label('Harga')->getStateUsing(fn($record) => $record->formatted_price),
                Tables\Columns\TextColumn::make('quota_per_day')->label('Kuota/Hari')->alignCenter(),
                Tables\Columns\IconColumn::make('is_approved')->label('Disetujui')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')->label('Kategori')->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_approved')->label('Status Approve'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Service $record) =>
                        auth()->user()->isSuperAdmin() && !$record->is_approved
                    )
                    ->requiresConfirmation()
                    ->action(function (Service $record) {
                        (new ApprovalService())->approveService($record, auth()->user());
                        Notification::make()->title('Layanan disetujui!')->success()->send();
                    }),

                Tables\Actions\Action::make('revoke')
                    ->label('Batalkan Approve')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn(Service $record) =>
                        auth()->user()->isSuperAdmin() && $record->is_approved
                    )
                    ->requiresConfirmation()
                    ->action(function (Service $record) {
                        (new ApprovalService())->revokeService($record);
                        Notification::make()->title('Approve dibatalkan!')->warning()->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit'   => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
