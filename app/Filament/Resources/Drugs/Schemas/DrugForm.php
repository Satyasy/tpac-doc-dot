<?php

namespace App\Filament\Resources\Drugs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class DrugForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\FileUpload::make('image')
                    ->label('Gambar Obat')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('drugs')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('category')
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('dosage_info')
                    ->label('Dosage Information')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('side_effects')
                    ->label('Side Effects')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('warnings')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('pregnancy_safe')
                    ->label('Safe for Pregnancy')
                    ->default(false),
            ]);
    }
}