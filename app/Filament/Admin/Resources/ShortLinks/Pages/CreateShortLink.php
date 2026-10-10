<?php

namespace App\Filament\Admin\Resources\ShortLinks\Pages;

use App\Filament\Admin\Resources\ShortLinks\ShortLinkResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShortLink extends CreateRecord
{
    protected static string $resource = ShortLinkResource::class;
}
