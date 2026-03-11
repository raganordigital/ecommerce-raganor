<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($productId)],
            'sku' => ['required', 'string', 'max:50', Rule::unique('products')->ignore($productId)],
            'description' => ['required', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'lt:price'],
            'sale_price_from' => ['nullable', 'date', 'required_with:sale_price'],
            'sale_price_to' => ['nullable', 'date', 'required_with:sale_price', 'after:sale_price_from'],
            'stock_quantity' => ['required_if:manage_stock,true', 'nullable', 'integer', 'min:0'],
            'manage_stock' => ['boolean'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'images' => ['nullable', 'array', 'max:5'], // Max 5 images
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'], // 2MB max per image
            'allow_cod' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU is already in use. Please choose a different one.',
            'description.required' => 'Product description is required.',
            'price.required' => 'Regular price is required.',
            'price.numeric' => 'Regular price must be a number.',
            'sale_price.lt' => 'Sale price must be less than regular price.',
            'sale_price_from.required_with' => 'Sale start date is required when sale price is set.',
            'sale_price_to.required_with' => 'Sale end date is required when sale price is set.',
            'sale_price_to.after' => 'Sale end date must be after start date.',
            'stock_quantity.required_if' => 'Stock quantity is required when tracking stock.',
            'stock_quantity.integer' => 'Stock quantity must be a whole number.',
            'images.*.max' => 'Each image must not exceed 2MB.',
            'images.*.image' => 'All files must be valid image files.',
            'images.max' => 'You can upload maximum 5 images per product.',
            'shipping_cost' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'free_shipping' => ['boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'manage_stock' => $this->boolean('manage_stock'),
            'is_active' => $this->boolean('is_active'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
