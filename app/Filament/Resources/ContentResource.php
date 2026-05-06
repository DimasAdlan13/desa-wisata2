<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentResource\Pages;
use App\Models\Content;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ContentResource extends Resource
{
    protected static ?string $model = Content::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Konten Wisata';
    protected static ?string $navigationGroup = 'Konten';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Info Konten')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul')
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
                    ->helperText('Dihasilkan otomatis berdasarkan Judul konten.'),
                Forms\Components\TextInput::make('type')
                    ->label('Tipe Konten')
                    ->required()
                    ->datalist([
                        'umkm',
                        'kuliner',
                        'info_wisata',
                    ])
                    ->placeholder('Pilih atau ketik tipe baru...')
                    ->helperText('Contoh: umkm, kuliner, info_wisata, berita, event, galeri, dll.'),
                Forms\Components\FileUpload::make('cover_image')
                    ->label('Foto Cover')
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
                    ->directory('contents'),
                Forms\Components\Toggle::make('is_published')
                    ->label('Publish')
                    ->live()
                    ->afterStateUpdated(fn($state, Forms\Set $set) =>
                        $set('published_at', $state ? now()->toDateTimeLocalString() : null)
                    ),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Unggulan di Homepage')
                    ->helperText('Jadikan artikel ini sebagai konten utama (besar) di section Info Wisata homepage.')
                    ->visible(fn(Forms\Get $get) => $get('type') === 'info_wisata'),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Tanggal Publish')
                    ->visible(fn(Forms\Get $get) => $get('is_published')),
            ])->columns(2),

            Forms\Components\Section::make('Isi Konten')->schema([
                Forms\Components\RichEditor::make('body')
                    ->label('Isi Artikel')
                    ->required()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->label('Cover')->disk('public')->square()->size(50),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->limit(50)->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->colors([
                        'success' => 'umkm',
                        'warning' => 'kuliner',
                        'info'    => 'info_wisata',
                    ]),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_published')->label('Publish')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->label('Tgl Publish')->date('d/m/Y'),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options(['umkm' => 'UMKM', 'kuliner' => 'Kuliner', 'info_wisata' => 'Info Wisata']),
                Tables\Filters\TernaryFilter::make('is_published')->label('Status Publish'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListContents::route('/'),
            'create' => Pages\CreateContent::route('/create'),
            'edit'   => Pages\EditContent::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }
}
