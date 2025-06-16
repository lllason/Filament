<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\EditAction;

use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = '使用者管理';
    protected static ?string $pluralModelLabel = '使用者';
    protected static ?string $modelLabel = '使用者';

    // 這邊判斷使用者是否有權限能看列表
    public static function canViewAny(): bool
    {
        return auth()->user()->can('view users');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create users');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('edit users');
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('delete users');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('view users');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('使用者名稱')
                ->required(),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('密碼')
                ->password()
                ->default(fn ($livewire) =>
                    $livewire instanceof \App\Filament\Resources\UserResource\Pages\EditUser
                        ? '********'
                        : ''
                )
                ->dehydrateStateUsing(function ($state) {
                    // 使用者有輸入新密碼才處理
                    return $state !== '********' && filled($state) ? bcrypt($state) : null;
                })
                ->dehydrated(fn ($state) =>
                    $state !== '********' && filled($state)
                )
                ->autocomplete('new-password')
                ->same('password_confirmation')
                ->required(fn ($livewire) =>
                    $livewire instanceof \App\Filament\Resources\UserResource\Pages\CreateUser
                ),

            TextInput::make('password_confirmation')
                ->label('密碼確認')
                ->password()
                ->autocomplete('new-password')
                ->visible(fn ($livewire) =>
                    $livewire instanceof \App\Filament\Resources\UserResource\Pages\CreateUser
                    || $livewire instanceof \App\Filament\Resources\UserResource\Pages\EditUser
                )
                ->required(fn ($livewire) =>
                    $livewire instanceof \App\Filament\Resources\UserResource\Pages\CreateUser
                ),

            Select::make('role')
                ->label('角色')
                ->options(Role::all()->pluck('name', 'name')) // name 當 value，也當 label
                ->required()
                ->afterStateHydrated(function ($component, $state) {
                    // 如果是編輯使用者，設定他目前的角色（只取第一個）
                    if (! $state && $component->getRecord()) {
                        $role = $component->getRecord()->roles->pluck('name')->first();
                        $component->state($role);
                    }
                }),

            Select::make('status')
                ->label('帳號狀態')
                ->options([
                    1 => '啟用',
                    0 => '停用',
                ])
                ->default(1) // 預設為啟用
                ->required()
                ->native(false), // 選擇是否使用 HTML 原生下拉（false 則是漂亮的下拉樣式）                
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('姓名')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),

                // 顯示角色（因為你是單選）
                TextColumn::make('roles.name')
                    ->label('角色')
                    ->formatStateUsing(fn ($state) => $state ?? '未指定'),

                ToggleColumn::make('status')
                    ->label('啟用狀態')
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make(),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
