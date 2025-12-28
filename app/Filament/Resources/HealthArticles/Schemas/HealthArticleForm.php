<?php

namespace App\Filament\Resources\HealthArticles\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class HealthArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('category')
                    ->options([
                        'mental' => 'Mental Health',
                        'physical' => 'Physical Health',
                        'nutrition' => 'Nutrition',
                        'general' => 'General',
                    ]),

                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('source')
                    ->label('Reference Source'),

                Forms\Components\Toggle::make('verified')
                    ->label('Verified')
                    ->default(false),

                Forms\Components\DateTimePicker::make('published_at'),
            ]);
    }
}