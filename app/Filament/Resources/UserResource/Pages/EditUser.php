<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->roleId = $data['role_id'] ?? null;
        unset($data['role_id']);
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->roleId) {
            $this->record->syncRoles([Role::find($this->roleId)?->name]);
        }
    }
}
