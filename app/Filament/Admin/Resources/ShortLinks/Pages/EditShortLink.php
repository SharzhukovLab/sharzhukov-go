<?php

namespace App\Filament\Admin\Resources\ShortLinks\Pages;

use App\Filament\Admin\Resources\ShortLinks\ShortLinkResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShortLink extends EditRecord
{
    protected static string $resource = ShortLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open')
                ->label('Open short URL')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => $this->record->short_url)
                ->openUrlInNewTab(),
            Action::make('target')
                ->label('Open target')
                ->icon('heroicon-o-globe-alt')
                ->url(fn () => $this->record->target_url)
                ->openUrlInNewTab()
                ->color('gray'),
            DeleteAction::make(),
        ];
    }
}
