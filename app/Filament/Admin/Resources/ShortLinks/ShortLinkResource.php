<?php

namespace App\Filament\Admin\Resources\ShortLinks;

use App\Filament\Admin\Resources\ShortLinks\Pages\CreateShortLink;
use App\Filament\Admin\Resources\ShortLinks\Pages\EditShortLink;
use App\Filament\Admin\Resources\ShortLinks\Pages\ListShortLinks;
use App\Filament\Admin\Resources\ShortLinks\Pages\ViewShortLink;
use App\Filament\Admin\Resources\ShortLinks\Schemas\ShortLinkForm;
use App\Filament\Admin\Resources\ShortLinks\Tables\ShortLinksTable;
use App\Models\ShortLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ShortLinkResource extends Resource
{
    protected static ?string $model = ShortLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;
    protected static ?string $navigationLabel = 'Links';
    protected static ?string $modelLabel = 'Link';
    protected static ?string $pluralModelLabel = 'Links';
    protected static string|\UnitEnum|null $navigationGroup = 'Go';
    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return ShortLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShortLinksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListShortLinks::route('/'),
            'create' => CreateShortLink::route('/create'),
            'view'   => ViewShortLink::route('/{record}'),
            'edit'   => EditShortLink::route('/{record}/edit'),
        ];
    }
}
