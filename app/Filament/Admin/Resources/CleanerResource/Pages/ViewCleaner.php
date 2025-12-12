<?php

namespace App\Filament\Admin\Resources\CleanerResource\Pages;

use App\Filament\Admin\Resources\CleanerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCleaner extends ViewRecord
{
    protected static string $resource = CleanerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
