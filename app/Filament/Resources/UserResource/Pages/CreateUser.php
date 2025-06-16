<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;

use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->roleId = $data['role_id'] ?? null;
        unset($data['role_id']);
        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->roleId) {
            $this->record->syncRoles([Role::find($this->roleId)?->name]);
        }
    }
}

