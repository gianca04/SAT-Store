<?php

namespace App\Forms\Components;

use App\Models\Brand;
use App\Forms\Components\BrandForm;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action as FormAction;

class BrandSelect
{
    public static function make(): Split
    {
        return Split::make([
            Section::make([
                Select::make('brand_id')
                    ->required()
                    ->prefixIcon('heroicon-m-briefcase')
                    ->label('Marca')
                    ->options(function (callable $get) {
                        return Brand::query()
                            ->select('id', 'name')
                            ->when($get('search'), function ($query, $search) {
                                $query->where('name', 'like', "%{$search}%");
                            })
                            ->get()
                            ->mapWithKeys(function ($brand) {
                                return [$brand->id => $brand->name];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->reactive()
                    ->helperText('Selecciona la marca para este producto.')
                    ->suffixAction(
                        FormAction::make('view_brand')
                            ->icon('heroicon-o-eye')
                            ->tooltip('Ver información de la marca')
                            ->color('info')
                            ->modalContent(function (callable $get) {
                                $brandId = $get('brand_id');
                                if (!$brandId) return null;
                                $brand = Brand::withCount('products')->find($brandId);
                                if (!$brand) return null;
                                return view('filament.components.brand-info-modal', compact('brand'));
                            })
                            ->modalHeading('Información de la Marca')
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Cerrar')
                            ->modalWidth('2xl')
                            ->visible(fn(callable $get) => !empty($get('brand_id')))
                    ),
            ]),
        ])
        ->from('md')
        ->columnSpanFull();
    }
}