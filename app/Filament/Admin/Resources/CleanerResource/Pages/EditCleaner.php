<?php

namespace App\Filament\Admin\Resources\CleanerResource\Pages;

use App\Filament\Admin\Resources\CleanerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCleaner extends EditRecord
{
    protected static string $resource = CleanerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
