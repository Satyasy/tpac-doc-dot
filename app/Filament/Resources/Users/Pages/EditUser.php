<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
   protected static string $resource = UserResource::class;

   protected function getHeaderActions(): array
   {
      return [
         DeleteAction::make(),
      ];
   }

   protected function mutateFormDataBeforeFill(array $data): array
   {
      // Get user's non-admin role
      $user = $this->record;
      $role = $user->roles->whereNotIn('name', ['super_admin'])->first();
      $data['role_select'] = $role?->name;

      return $data;
   }

   protected function afterSave(): void
   {
      $roleSelect = $this->data['role_select'] ?? null;
      $user = $this->record;

      // Remove doctor role if exists
      $user->roles()->detach(
         Role::whereIn('name', ['doctor'])->pluck('id')
      );

      // Assign new role if selected
      if ($roleSelect) {
         $user->assignRole($roleSelect);
      }
   }
}
