<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryItemResource\Pages;
use App\Filament\Resources\GalleryItemResource\RelationManagers;
use App\Models\GalleryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GalleryItemResource extends Resource
{
    protected static ?string $model = GalleryItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk(fn (?GalleryItem $record): string =>
                        $record?->image && file_exists(public_path('images/' . $record->image))
                            ? 'legacy_images'
                            : 'public')
                    ->directory('uploads/gallery')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->getStateUsing(fn (GalleryItem $record): ?string =>
                        $record->image && file_exists(public_path('images/' . $record->image))
                            ? asset('images/' . $record->image)
                            : $record->image),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryItems::route('/'),
            'create' => Pages\CreateGalleryItem::route('/create'),
            'edit' => Pages\EditGalleryItem::route('/{record}/edit'),
        ];
    }
}
