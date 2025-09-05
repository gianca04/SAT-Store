<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class ProductPhotoGalery extends Field
{
    protected string $view = 'forms.components.product-photo-galery';
    
    protected int | \Closure | null $maxFiles = null;
    protected int | \Closure | null $maxFileSize = null;
    protected array | \Closure | null $acceptedFileTypes = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (ProductPhotoGalery $component, $state) {
            // Ensure the state is properly formatted
            if (is_array($state)) {
                $component->state($state);
            }
        });

        $this->dehydrateStateUsing(function ($state) {
            // Process the state before saving
            if (!is_array($state)) {
                return [];
            }

            return collect($state)->map(function ($photo, $index) {
                return [
                    'id' => $photo['id'] ?? null,
                    'path' => $photo['path'] ?? null,
                    'description' => $photo['description'] ?? '',
                    'is_primary' => $photo['is_primary'] ?? false,
                    'position' => $index + 1,
                ];
            })->toArray();
        });
    }

    public function maxFiles(int | \Closure $maxFiles): static
    {
        $this->maxFiles = $maxFiles;
        return $this;
    }

    public function maxFileSize(int | \Closure $maxFileSize): static
    {
        $this->maxFileSize = $maxFileSize;
        return $this;
    }

    public function acceptedFileTypes(array | \Closure $types): static
    {
        $this->acceptedFileTypes = $types;
        return $this;
    }

    public function getMaxFiles(): int
    {
        return $this->evaluate($this->maxFiles) ?? 20;
    }

    public function getMaxFileSize(): int
    {
        return $this->evaluate($this->maxFileSize) ?? (10 * 1024 * 1024); // 10MB
    }

    public function getAcceptedFileTypes(): array
    {
        return $this->evaluate($this->acceptedFileTypes) ?? [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
    }
}
