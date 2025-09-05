<?php

namespace App\Forms\Components;

use App\Models\Brand;
use App\Forms\Components\BrandForm;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action as FormAction;

class BrandSelect
{
    public static function make(): Select
    {
        return Select::make('brand_id')
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
            ->createOptionForm([
                BrandForm::make(),
            ])
            ->createOptionUsing(function (array $data): int {
                $brand = Brand::create($data);

                Notification::make()
                    ->title('Marca creada exitosamente')
                    ->success()
                    ->body("La marca '{$brand->name}' ha sido creada y seleccionada.")
                    ->send();

                return $brand->id;
            })
            ->createOptionAction(function (FormAction $action) {
                return $action
                    ->modalHeading('Crear nueva marca')
                    ->modalSubmitActionLabel('Crear marca')
                    ->modalWidth('3xl');
            })
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
            );
    }
}
