<?php

namespace App\Forms\Components;

use App\Forms\Components\BrandSelect;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class ProductForm
{
    // protected string $view = 'forms.components.product-form';

    public static function make(): Split
    {
        return Split::make([
            Section::make('Información principal')
                ->description('Datos generales de la marca')
                ->icon('heroicon-o-identification')
                ->schema([
                    TextInput::make('name')
                        ->columnSpan(2)
                        ->required()
                        ->maxLength(255),
                    RichEditor::make('description')
                        ->label('Descripción')
                        ->placeholder('Descripción del cliente')
                        ->columnSpanFull()
                        ->toolbarButtons([
                            'blockquote',
                            'bold',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ]),
                ])->columns([
                    'sm' => 1,
                    'md' => 1,
                    'xl' => 2,
                    '2xl' => 2,
                ]),

                
            Section::make('Adicional')
                ->icon('heroicon-o-photo')->columns([
                    'sm' => 1,
                    'md' => 1,
                    'xl' => 2,
                    '2xl' => 2,
                ])
                ->description('Datos adicionales del producto')
                ->schema([
                    Toggle::make('active')
                        ->required()
                        ->columnSpan(2)
                        ->label('¿Activo?'),
                    BrandSelect::make()
                        ->columnSpan(2),
                    RichEditor::make('characteristics')
                        ->label('características')
                        ->placeholder('Descripción de las características del producto')
                        ->columnSpanFull()
                        ->toolbarButtons([
                            'orderedList',
                            'blockquote',
                            'bold',
                            'bulletList',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ]),
                ])
                ->columns(2),

            
        ])
            ->from('md')
            ->columnSpanFull();
    }
}
