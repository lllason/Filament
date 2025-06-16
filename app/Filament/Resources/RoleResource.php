<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use App\Filament\Resources\RoleResource\Pages;

use Spatie\Permission\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Illuminate\Database\Eloquent\Model;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = '角色管理';
    protected static ?string $pluralModelLabel = '角色';
    protected static ?string $modelLabel = '角色';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('manage roles') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('manage roles') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('manage roles') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('manage roles') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('manage roles') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('角色名稱')
                ->required()
                ->unique(ignoreRecord: true),

            CheckboxList::make('permissions')
                ->label('權限列表')
                ->relationship('permissions', 'name') // 直接從 Spatie 的 permissions 關聯讀取
                ->columns(2) // 權限太多時可分欄
                ->searchable(), // 當權限數量多時可搜尋
        ]);        
    }

    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('角色名稱')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('建立時間')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');            
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
