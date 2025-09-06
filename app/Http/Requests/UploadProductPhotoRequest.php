<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadProductPhotoRequest extends FormRequest
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
            'photos' => 'required|array|max:20',
            'photos.*' => 'required|file|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB
            'descriptions' => 'sometimes|array',
            'descriptions.*' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'photos.required' => 'Debe subir al menos una foto.',
            'photos.array' => 'Las fotos deben enviarse como un arreglo.',
            'photos.max' => 'No puede subir más de 20 fotos a la vez.',
            'photos.*.required' => 'Cada archivo de foto es obligatorio.',
            'photos.*.file' => 'Cada foto debe ser un archivo válido.',
            'photos.*.image' => 'Cada archivo debe ser una imagen.',
            'photos.*.mimes' => 'Las fotos deben ser de tipo: jpeg, png, jpg, gif, webp.',
            'photos.*.max' => 'Cada foto no puede ser mayor a 10MB.',
            'descriptions.array' => 'Las descripciones deben enviarse como un arreglo.',
            'descriptions.*.max' => 'Cada descripción no puede tener más de 255 caracteres.',
        ];
    }
}
