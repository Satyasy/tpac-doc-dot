<?php

namespace App\Filament\Resources\Drugs\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class PricesRelationManager extends RelationManager
{
   protected static string $relationship = 'prices';

   protected static ?string $recordTitleAttribute = 'pharmacy_name';

   public function form(Schema $schema): Schema
   {
      return $schema
         ->components([
            Forms\Components\TextInput::make('pharmacy_name')
               ->required()
               ->maxLength(255),

            Forms\Components\TextInput::make('price_min')
               ->label('Minimum Price')
               ->numeric()
               ->required()
               ->prefix('Rp'),

            Forms\Components\TextInput::make('price_max')
               ->label('Maximum Price')
               ->numeric()
               ->required()
               ->prefix('Rp'),
         ]);
   }

   public function table(Table $table): Table
   {
      return $table
         ->columns([
            Tables\Columns\TextColumn::make('pharmacy_name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('price_min')->money('IDR')->sortable(),
            Tables\Columns\TextColumn::make('price_max')->money('IDR')->sortable(),
            Tables\Columns\TextColumn::make('updated_at')->since(),
         ])
         ->headerActions([
            Actions\CreateAction::make(),
         ])
         ->recordActions([
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
         ])
         ->toolbarActions([
            Actions\BulkActionGroup::make([
               Actions\DeleteBulkAction::make(),
            ]),
         ]);
   }
}
