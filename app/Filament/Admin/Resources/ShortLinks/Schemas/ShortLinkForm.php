<?php

namespace App\Filament\Admin\Resources\ShortLinks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShortLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Link')->schema([
                TextInput::make('slug')
                    ->required()
                    ->maxLength(64)
                    ->unique(ignoreRecord: true)
                    ->helperText('Short URL: /s/{slug}'),

                TextInput::make('target_url')
                    ->label('Target URL')
                    ->url()
                    ->required()
                    ->maxLength(2000)
                    ->columnSpanFull(),

                TextInput::make('description')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('— anonymous —'),

                Select::make('category')
                    ->options([
                        'general' => 'General',
                        'project' => 'Project',
                        'social' => 'Social',
                        'docs' => 'Docs',
                    ])
                    ->default('general'),
            ])->columns(2),

            Section::make('Status')->schema([
                Toggle::make('is_active')->default(true),
                Toggle::make('is_reserved')->default(false),
                DateTimePicker::make('expires_at'),
            ])->columns(3),

            Section::make('UTM (optional)')->schema([
                TextInput::make('utm_source')->maxLength(255),
                TextInput::make('utm_medium')->maxLength(255),
                TextInput::make('utm_campaign')->maxLength(255),
                TextInput::make('utm_content')->maxLength(255),
                TextInput::make('utm_term')->maxLength(255),
            ])->columns(2)->collapsed(),
        ]);
    }
}