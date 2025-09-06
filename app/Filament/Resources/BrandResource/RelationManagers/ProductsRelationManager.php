<?php

namespace App\Filament\Resources\BrandResource\RelationManagers;

use App\Forms\Components\ProductForm;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ProductForm::make(),
                Repeater::make('photos')
                    ->label('Fotografías')
                    ->relationship()
                    ->schema([
                        FileUpload::make('path')
                            ->label('Imagen')
                            ->disk('public')
                            ->directory('products')
                            ->imageEditor()
                            ->imageEditorMode(2)
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->image(),
                        Toggle::make('is_primary')
                            ->label('Foto Principal')
                            ->live()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state, $record) {
                                // Solo ejecuta la lógica si el toggle se ha activado (estado es true)
                                if ($state === true) {
                                    $parentRepeaterItems = $get('../../photos');
                                    // Itera sobre todos los ítems y desmarca los que no son el actual
                                    foreach ($parentRepeaterItems as $key => $item) {
                                        // Obtén el ID de la foto actual para evitar desmarcarla
                                        $currentId = $record ? $record->id : null;
                                        // Si el ID del item es diferente y está marcado como principal, desmárcalo
                                        if (isset($item['is_primary']) && $item['is_primary'] === true && (isset($item['id']) && $item['id'] !== $currentId)) {
                                            $set('../../photos.' . $key . '.is_primary', false);
                                        }
                                    }
                                }
                            }),
                        TextInput::make('description')
                            ->label('Descripción'),
                    ])
                    ->grid(2)
                    ->columnSpanFull()
                    ->orderColumn('position')
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->itemLabel(fn(array $state): ?string => $state['description'] ?? 'Foto sin descripción'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('primaryPhoto.path')
                    ->label('Imagen')
                    ->disk('public')
                    ->size(50)
                    ->circular()
                    ->defaultImageUrl(fn() => 'https://via.placeholder.com/50x50/e5e7eb/9ca3af?text=Sin+Imagen'),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Marca')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('photos_count')
                    ->label('Fotos')
                    ->counts('photos')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Estado')
                    ->placeholder('Todos los productos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}