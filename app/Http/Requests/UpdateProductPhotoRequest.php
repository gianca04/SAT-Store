<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductPhotoRequest extends FormRequest
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
            'product_id' => 'sometimes|exists:products,id',
            'path' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_primary' => 'sometimes|boolean',
            'position' => 'sometimes|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.exists' => 'El producto seleccionado no existe.',
            'path.max' => 'La ruta de la imagen no puede tener más de 255 caracteres.',
            'description.max' => 'La descripción no puede tener más de 500 caracteres.',
            'is_primary.boolean' => 'El campo imagen principal debe ser verdadero o falso.',
            'position.integer' => 'La posición debe ser un número entero.',
            'position.min' => 'La posición debe ser mayor a 0.',
        ];
    }
}
