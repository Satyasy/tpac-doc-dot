<?php

namespace App\Filament\Resources\MedicalDocuments\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;

class EmbeddingsRelationManager extends RelationManager
{
   protected static string $relationship = 'embeddings';

   protected static ?string $recordTitleAttribute = 'vector_id';

   public function form(Schema $schema): Schema
   {
      return $schema
         ->components([
            Forms\Components\TextInput::make('vector_id')
               ->label('Vector ID')
               ->required()
               ->maxLength(255),

            Forms\Components\TextInput::make('chunk_index')
               ->label('Chunk Index')
               ->numeric()
               ->required(),
         ]);
   }

   public function table(Table $table): Table
   {
      return $table
         ->columns([
            Tables\Columns\TextColumn::make('vector_id')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('chunk_index')->sortable(),
            Tables\Columns\TextColumn::make('created_at')->since(),
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
