<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'photos' => 'required|array',
            'photos.*.id' => 'required|exists:product_photos,id',
            'photos.*.position' => 'required|integer|min:1',
            'photos.*.is_primary' => 'required|boolean',
            'photos.*.description' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'photos.required' => 'Las fotos son obligatorias.',
            'photos.array' => 'Las fotos deben enviarse como un arreglo.',
            'photos.*.id.required' => 'El ID de cada foto es obligatorio.',
            'photos.*.id.exists' => 'Una o más fotos no existen.',
            'photos.*.position.required' => 'La posición de cada foto es obligatoria.',
            'photos.*.position.integer' => 'La posición debe ser un número entero.',
            'photos.*.position.min' => 'La posición debe ser mayor a 0.',
            'photos.*.is_primary.required' => 'El estado principal de cada foto es obligatorio.',
            'photos.*.is_primary.boolean' => 'El estado principal debe ser verdadero o falso.',
            'photos.*.description.max' => 'La descripción no puede tener más de 255 caracteres.',
        ];
    }

    /**
     * Perform additional validation after the basic validation passes.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $photos = collect($this->input('photos', []));
            
            // Check that exactly one photo is marked as primary
            $primaryCount = $photos->where('is_primary', true)->count();
            if ($primaryCount !== 1) {
                $validator->errors()->add('photos', 'Debe haber exactamente una foto principal.');
            }

            // Check for duplicate positions
            $positions = $photos->pluck('position');
            if ($positions->count() !== $positions->unique()->count()) {
                $validator->errors()->add('photos', 'No puede haber posiciones duplicadas.');
            }
        });
    }
}
