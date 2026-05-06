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
            Forms\Components\Section::make('Info Layanan Wisata')
                ->description('Data ini akan tampil secara publik di katalog wisatawan.')
                ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Layanan')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, Forms\Set $set) =>
                        $set('slug', Str::slug($state) . '-' . Str::random(5))
                    ),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->readOnly()
                    ->helperText('Dihasilkan otomatis berdasarkan Nama Layanan.'),
                Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->options(fn() => \App\Models\ServiceCategory::orderBy('name')->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('user_id')
                    ->label('Admin Pengelola')
                    ->options(fn() => \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN_LAYANAN)
                        ->where('is_approved', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                    )
                    ->required()
                    ->searchable()
                    ->visible(fn() => auth()->user()->isSuperAdmin()),
                Forms\Components\TextInput::make('price')
                    ->label('Harga (Rp)')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Select::make('pricing_type')
                    ->label('Tipe Perhitungan Harga')
                    ->options([
                        'per_pax' => 'Per Orang (Hitung per kepala)',
                        'per_unit' => 'Per Unit (Kamar, Kapal, dll)',
                    ])
                    ->default('per_pax')
                    ->required(),
                Forms\Components\TextInput::make('unit_name')
                    ->label('Nama Satuan')
                    ->default('Orang')
                    ->required()
                    ->helperText('Satuan yang tampil di layar wisatawan (Contoh: Orang, Kamar, Kapal).'),
                Forms\Components\TextInput::make('quota_per_day')
                    ->label('Kuota / Kapasitas Maksimal per Hari')
                    ->required()
                    ->numeric()
                    ->default(10)
                    ->helperText('Batas maksimal booking dalam 1 hari.'),
                Forms\Components\TextInput::make('location')
                    ->label('Titik Lokasi / Meeting Point')
                    ->helperText('Titik kumpul atau lokasi tempat layanan berlangsung. Contoh: Dermaga Pulau Pramuka'),
                Forms\Components\TextInput::make('contact_person')
                    ->label('Nomor WA yang Bisa Dihubungi Wisatawan')
                    ->helperText('Nomor ini akan ditampilkan ke wisatawan untuk menghubungi pengelola'),
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
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1280')
                            ->imageResizeTargetHeight('720')
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

            Forms\Components\Section::make('Pertanyaan Tambahan Saat Booking (Opsional)')
                ->description(new \Illuminate\Support\HtmlString('
                    <div class="text-sm text-gray-500 mt-1">
                        <p class="mb-2">Gunakan fitur ini jika Anda butuh info tambahan spesifik dari wisatawan saat mereka memesan layanan ini. Kosongkan jika tidak perlu.</p>
                    </div>
                '))
                ->schema([
                    Forms\Components\Repeater::make('form_schema')
                        ->label('Daftar Pertanyaan Tambahan')
                        ->schema([
                            Forms\Components\TextInput::make('pertanyaan')
                                ->label('Pertanyaan untuk Wisatawan')
                                ->placeholder('Contoh: Tema foto yang diinginkan?')
                                ->required()
                                ->columnSpan(2),
                            Forms\Components\Select::make('tipe')
                                ->label('Tipe Jawaban')
                                ->options([
                                    'text' => 'Jawaban Pendek (1 Baris)',
                                    'textarea' => 'Jawaban Panjang (Paragraf)',
                                ])
                                ->default('text')
                                ->required()
                                ->columnSpan(1),
                        ])
                        ->columns(3)
                        ->addActionLabel('Tambah Pertanyaan')
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
