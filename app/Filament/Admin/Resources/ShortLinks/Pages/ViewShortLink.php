<?php

namespace App\Filament\Admin\Resources\ShortLinks\Pages;

use App\Filament\Admin\Resources\ShortLinks\ShortLinkResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewShortLink extends ViewRecord
{
    protected static string $resource = ShortLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }
}
