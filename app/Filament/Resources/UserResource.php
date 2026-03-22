<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Services\ApprovalService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manajemen User';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Akun')->schema([
                Forms\Components\TextInput::make('name')->label('Nama')->required(),
                Forms\Components\TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('phone')->label('No. HP'),
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->options([
                        'super_admin'   => 'Super Admin',
                        'admin_layanan' => 'Admin Layanan',
                        'wisatawan'     => 'Wisatawan',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context) => $context === 'create'),
            ])->columns(2),

            Forms\Components\Section::make('Info Bisnis (Admin Layanan)')
                ->schema([
                    Forms\Components\TextInput::make('business_name')->label('Nama Bisnis'),
                    Forms\Components\TextInput::make('business_address')->label('Alamat Bisnis'),
                    Forms\Components\Textarea::make('business_description')->label('Deskripsi Bisnis')->rows(3)->columnSpanFull(),
                ])
                ->columns(2)
                ->visible(fn(Forms\Get $get) => $get('role') === 'admin_layanan'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->label('Role')
                    ->colors([
                        'danger'  => 'super_admin',
                        'warning' => 'admin_layanan',
                        'success' => 'wisatawan',
                    ]),
                Tables\Columns\IconColumn::make('is_approved')->label('Disetujui')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Daftar')->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'super_admin'   => 'Super Admin',
                        'admin_layanan' => 'Admin Layanan',
                        'wisatawan'     => 'Wisatawan',
                    ]),
                Tables\Filters\TernaryFilter::make('is_approved')->label('Status Approve'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(User $record) => $record->role === User::ROLE_ADMIN_LAYANAN && !$record->is_approved)
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        (new ApprovalService())->approveUser($record, auth()->user());
                        Notification::make()->title('Admin disetujui!')->success()->send();
                    }),

                Tables\Actions\Action::make('revoke')
                    ->label('Cabut Akses')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(User $record) => $record->role === User::ROLE_ADMIN_LAYANAN && $record->is_approved)
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        (new ApprovalService())->revokeUser($record);
                        Notification::make()->title('Akses dicabut!')->warning()->send();
                    }),

                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }
}
