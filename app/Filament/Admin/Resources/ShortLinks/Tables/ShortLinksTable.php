<?php

namespace App\Filament\Admin\Resources\ShortLinks\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ShortLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('slug')
                    ->prefix('/s/')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('target_url')
                    ->label('Target')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->target_url)
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('Owner')
                    ->placeholder('— anonymous —')
                    ->toggleable(),

                TextColumn::make('clicks_count')
                    ->label('Clicks')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                ToggleColumn::make('is_active')->label('Active'),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
                SelectFilter::make('category')
                    ->options([
                        'general' => 'General',
                        'project' => 'Project',
                        'social' => 'Social',
                        'docs' => 'Docs',
                    ]),
            ])
            ->recordActions([
                Action::make('open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->tooltip('Open short URL')
                    ->url(fn ($record) => $record->short_url)
                    ->openUrlInNewTab()
                    ->color('gray'),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
