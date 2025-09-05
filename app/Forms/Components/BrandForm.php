<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;

class BrandForm
{
    //protected string $view = 'forms.components.brand-form';

    public static function make(): Split
    {
        return Split::make([
            Section::make('Información principal')
                ->description('Datos generales de la marca')
                ->icon('heroicon-o-identification')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre de la marca')
                        ->placeholder('Nombre de la marca')
                        ->required()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-building-office-2')
                        ->columnSpan(2),

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
                ])
                ->columns([
                    'sm' => 1,
                    'md' => 1,
                    'xl' => 2,
                    '2xl' => 2,
                ]),

            Section::make('Multimedia')
                ->icon('heroicon-o-photo')
                ->columns([
                    'sm' => 1,
                    'md' => 1,
                    'xl' => 2,
                    '2xl' => 2,
                ])
                ->description('Fotos y archivos relacionados con la marca')
                ->schema([

                    FileUpload::make('foto_path')
                        ->label('Logo de la marca')
                        ->image()
                        ->imageEditor()
                        ->acceptedFileTypes(['image/*'])
                        ->hint('Sube el logo de la empresa')
                        ->directory('brands')
                        ->previewable(true)
                        ->columnSpanFull(),

                        
                ])
                ->columns(2),
        ])
            ->from('md')
            ->columnSpanFull();
    }
}
