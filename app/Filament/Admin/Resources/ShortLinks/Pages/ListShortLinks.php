<?php

namespace App\Filament\Admin\Resources\ShortLinks\Pages;

use App\Filament\Admin\Resources\ShortLinks\ShortLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListShortLinks extends ListRecords
{
    protected static string $resource = ShortLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
