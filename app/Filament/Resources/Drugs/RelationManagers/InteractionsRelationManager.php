<?php

namespace App\Filament\Resources\Drugs\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class InteractionsRelationManager extends RelationManager
{
   protected static string $relationship = 'interactions';

   protected static ?string $recordTitleAttribute = 'interacting_drug';

   public function form(Schema $schema): Schema
   {
      return $schema
         ->components([
            Forms\Components\TextInput::make('interacting_drug')
               ->label('Interacting Drug')
               ->required()
               ->maxLength(255),

            Forms\Components\Select::make('risk_level')
               ->options([
                  'low' => 'Low',
                  'moderate' => 'Moderate',
                  'high' => 'High',
               ])
               ->required(),

            Forms\Components\Textarea::make('description')
               ->required()
               ->columnSpanFull(),
         ]);
   }

   public function table(Table $table): Table
   {
      return $table
         ->columns([
            Tables\Columns\TextColumn::make('interacting_drug')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('risk_level')->badge()
               ->color(fn(string $state): string => match ($state) {
                  'low' => 'success',
                  'moderate' => 'warning',
                  'high' => 'danger',
                  default => 'gray',
               }),
            Tables\Columns\TextColumn::make('description')->limit(50),
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
