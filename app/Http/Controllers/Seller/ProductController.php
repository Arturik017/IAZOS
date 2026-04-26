<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\Product;
use App\Support\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (($user->role ?? null) !== 'seller' || ($user->seller_status ?? null) !== 'approved') {
            abort(403);
        }

        $products = Product::where('seller_id', $user->id)
            ->latest()
            ->get();

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'final_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],

            'image' => ['required', 'image', 'max:4096'],
            'gallery' => ['nullable', 'array', 'max:8'],
            'gallery.*' => ['image', 'max:4096'],

            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:categories,id'],

            'attributes' => ['nullable', 'array'],
            'units' => ['nullable', 'array'],
            'has_variants_enabled' => ['nullable', 'boolean'],

            'variants' => ['nullable', 'array'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.image' => ['nullable', 'image', 'max:4096'],
            'variants.*.is_active' => ['nullable'],
            'variants.*.attributes' => ['nullable', 'array'],
        ]);

        [$categoryId, $subcategoryId] = $this->normalizeCategoryIds(
            (int) $request->category_id,
            $request->filled('subcategory_id') ? (int) $request->subcategory_id : null
        );

        $finalCategory = $this->resolveFinalCategoryOrFail($categoryId, $subcategoryId);
        $hasVariantsEnabled = $request->boolean('has_variants_enabled');
        $this->validateCatalogPayload(
            $finalCategory,
            $request->input('attributes', []),
            $request->input('variants', []),
            $hasVariantsEnabled,
            false
        );

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = ImageStorage::storeWebp($request->file('image'), 'products', 'public', 82, 'image');
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'final_price' => $request->final_price,
            'stock' => (int) ($request->stock ?? 0),
            'status' => 1,
            'image' => $imagePath,
            'primary_image' => $imagePath,
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId,
            'seller_id' => auth()->id(),
            'shipping_included' => true,
            'is_approved' => false,
            'has_variants' => false,
        ]);

        if ($imagePath) {
            $product->images()->create([
                'path' => $imagePath,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $file) {
                $path = ImageStorage::storeWebp($file, 'products/gallery', 'public', 82, 'gallery');

                $product->images()->create([
                    'path' => $path,
                    'is_primary' => false,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        $this->syncAttributes(
            $product,
            $request->input('attributes', []),
            $request->input('units', [])
        );

        $this->syncVariants($product, $request, false, $hasVariantsEnabled);

        return redirect()->route('seller.products.index')
            ->with('success', 'Produs adăugat!');
    }

    public function edit($id)
    {
        $product = Product::with([
            'attributes',
            'images',
            'variants.attributes.option',
            'variants.attributes.attribute',
        ])->findOrFail($id);

        if ($product->seller_id !== auth()->id()) {
            abort(403);
        }

        $categories = Category::whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::with([
            'attributes',
            'images',
            'variants.attributes',
        ])->findOrFail($id);

        if ($product->seller_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'final_price' => ['required', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],

            'image' => ['nullable', 'image', 'max:4096'],
            'gallery' => ['nullable', 'array', 'max:8'],
            'gallery.*' => ['image', 'max:4096'],
            'delete_gallery' => ['nullable', 'array'],
            'delete_gallery.*' => ['integer'],

            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:categories,id'],

            'attributes' => ['nullable', 'array'],
            'units' => ['nullable', 'array'],
            'has_variants_enabled' => ['nullable', 'boolean'],

            'variants' => ['nullable', 'array'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.image' => ['nullable', 'image', 'max:4096'],
            'variants.*.is_active' => ['nullable'],
            'variants.*.attributes' => ['nullable', 'array'],
        ]);

        [$categoryId, $subcategoryId] = $this->normalizeCategoryIds(
            (int) $request->category_id,
            $request->filled('subcategory_id') ? (int) $request->subcategory_id : null
        );

        $finalCategory = $this->resolveFinalCategoryOrFail($categoryId, $subcategoryId);
        $hasVariantsEnabled = $request->boolean('has_variants_enabled');
        $this->validateCatalogPayload(
            $finalCategory,
            $request->input('attributes', []),
            $request->input('variants', []),
            $hasVariantsEnabled,
            true,
            $product
        );

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $newImage = ImageStorage::storeWebp($request->file('image'), 'products', 'public', 82, 'image');
            $product->image = $newImage;
            $product->primary_image = $newImage;

            $oldPrimary = $product->images()->where('is_primary', true)->first();
            if ($oldPrimary) {
                Storage::disk('public')->delete($oldPrimary->path);
                $oldPrimary->delete();
            }

            $product->images()->create([
                'path' => $newImage,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        if ($request->filled('delete_gallery')) {
            $imagesToDelete = $product->images()
                ->whereIn('id', $request->delete_gallery)
                ->where('is_primary', false)
                ->get();

            foreach ($imagesToDelete as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
        }

        if ($request->hasFile('gallery')) {
            $currentMaxSort = (int) ($product->images()->max('sort_order') ?? 0);

            foreach ($request->file('gallery') as $index => $file) {
                $path = ImageStorage::storeWebp($file, 'products/gallery', 'public', 82, 'gallery');

                $product->images()->create([
                    'path' => $path,
                    'is_primary' => false,
                    'sort_order' => $currentMaxSort + $index + 1,
                ]);
            }
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'final_price' => $request->final_price,
            'stock' => (int) ($request->stock ?? 0),
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId,
            'is_approved' => false,
        ]);

        $this->syncAttributes(
            $product,
            $request->input('attributes', []),
            $request->input('units', [])
        );

        $this->syncVariants($product, $request, true, $hasVariantsEnabled);

        return redirect()->route('seller.products.index')
            ->with('success', 'Produs actualizat!');
    }

    public function destroy($id)
    {
        $product = Product::with(['images', 'variants'])->findOrFail($id);

        if ($product->seller_id !== auth()->id()) {
            abort(403);
        }

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        foreach ($product->variants as $variant) {
            if ($variant->image) {
                Storage::disk('public')->delete($variant->image);
            }
        }

        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Produs șters!');
    }

    public function categoryAttributes(Category $category)
    {
        try {
            $attributes = CategoryAttribute::with('options')
                ->where('category_id', $category->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            return response()->json(
                $attributes->map(function ($attribute) {
                    return [
                        'id' => $attribute->id,
                        'name' => $attribute->name,
                        'slug' => $attribute->slug,
                        'type' => $attribute->type,
                        'unit_mode' => $attribute->unit_mode,
                        'default_unit' => $attribute->default_unit,
                        'allowed_units' => is_array($attribute->allowed_units)
                            ? $attribute->allowed_units
                            : (json_decode($attribute->allowed_units ?? '[]', true) ?: []),
                        'is_required' => (bool) $attribute->is_required,
                        'is_filterable' => (bool) $attribute->is_filterable,
                        'is_variant' => (bool) $attribute->is_variant,
                        'options' => $attribute->options->map(function ($option) {
                            return [
                                'id' => $option->id,
                                'value' => $option->value,
                                'label' => $option->label,
                            ];
                        })->values(),
                    ];
                })->values()
            );
        } catch (\Throwable $e) {
            Log::error('Seller category attributes failed', [
                'category_id' => $category->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'message' => 'Nu s-au putut încărca atributele categoriei.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function normalizeCategoryIds(int $categoryId, ?int $subcategoryId): array
    {
        if (!$subcategoryId) {
            return [$categoryId, null];
        }

        $category = Category::find($categoryId);
        $subcategory = Category::find($subcategoryId);

        if (!$category || !$subcategory) {
            return [$categoryId, null];
        }

        $current = $subcategory;
        $belongsToCategory = false;

        while ($current) {
            if ((int) $current->id === (int) $category->id) {
                $belongsToCategory = true;
                break;
            }

            $current = $current->parent;
        }

        if (!$belongsToCategory) {
            return [$categoryId, null];
        }

        return [$categoryId, $subcategoryId];
    }

    private function syncAttributes(Product $product, array $attributes = [], array $units = []): void
    {
        $product->attributes()->delete();
        $resolvedBrand = null;

        foreach ($attributes as $attributeId => $value) {
            $attribute = CategoryAttribute::find($attributeId);

            if (!$attribute || $attribute->is_variant) {
                continue;
            }

            $unit = $units[$attributeId] ?? null;

            if (is_array($value) && $attribute->type === 'multiselect') {
                foreach ($value as $optionId) {
                    if (!$optionId) {
                        continue;
                    }

                    $product->attributes()->create([
                        'category_attribute_id' => $attribute->id,
                        'option_id' => $optionId,
                        'unit' => $unit,
                    ]);
                }

                continue;
            }

            if ($attribute->type === 'select') {
                if (!$value) {
                    continue;
                }

                if ($attribute->slug === 'brand') {
                    $resolvedBrand = optional($attribute->options()->find($value))->label;
                }

                $product->attributes()->create([
                    'category_attribute_id' => $attribute->id,
                    'option_id' => $value,
                    'unit' => $unit,
                ]);

                continue;
            }

            if ($attribute->type === 'boolean') {
                $product->attributes()->create([
                    'category_attribute_id' => $attribute->id,
                    'value_boolean' => (bool) $value,
                    'unit' => $unit,
                ]);

                continue;
            }

            if ($attribute->type === 'number') {
                if ($value === null || $value === '') {
                    continue;
                }

                $product->attributes()->create([
                    'category_attribute_id' => $attribute->id,
                    'value_number' => $value,
                    'unit' => $unit,
                ]);

                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            if ($attribute->slug === 'brand') {
                $resolvedBrand = trim((string) $value);
            }

            $product->attributes()->create([
                'category_attribute_id' => $attribute->id,
                'value_text' => $value,
                'unit' => $unit,
            ]);
        }

        $product->brand = $resolvedBrand ?: null;
        $product->save();
    }

    private function syncVariants(Product $product, Request $request, bool $isUpdate = false, bool $hasVariantsEnabled = false): void
    {
        $variants = $request->input('variants', []);

        if ($isUpdate && !$hasVariantsEnabled) {
            foreach ($product->variants as $oldVariant) {
                if ($oldVariant->image) {
                    Storage::disk('public')->delete($oldVariant->image);
                }
            }

            $product->variants()->delete();

            $product->has_variants = false;
            $product->stock = (int) ($request->input('stock') ?? 0);
            $product->save();

            return;
        }

        if (!$hasVariantsEnabled) {
            $product->has_variants = false;
            $product->stock = (int) ($request->input('stock') ?? 0);
            $product->save();

            return;
        }

        if ($isUpdate) {
            foreach ($product->variants as $oldVariant) {
                if ($oldVariant->image) {
                    Storage::disk('public')->delete($oldVariant->image);
                }
            }

            $product->variants()->delete();
        }

        if (!is_array($variants) || empty($variants)) {
            throw ValidationException::withMessages([
                'variants' => 'Produsul este marcat cu variante, dar nu exista combinatii salvate.',
            ]);
        }

        $createdAny = false;
        $totalStock = 0;

        foreach ($variants as $index => $variantData) {
            $attributes = $variantData['attributes'] ?? [];

            if (!is_array($attributes) || empty($attributes)) {
                continue;
            }

            $normalizedAttributes = [];

            foreach ($attributes as $attributeId => $payload) {
                $attribute = CategoryAttribute::find($attributeId);

                if (!$attribute || !$attribute->is_variant) {
                    continue;
                }

                if (is_array($payload)) {
                    $optionId = $payload['option_id'] ?? null;
                    $customValue = trim((string) ($payload['custom_value'] ?? ''));

                    if ($customValue !== '') {
                        $normalizedAttributes[] = [
                            'category_attribute_id' => (int) $attributeId,
                            'option_id' => null,
                            'custom_value' => $customValue,
                        ];
                        continue;
                    }

                    if ($optionId !== null && $optionId !== '') {
                        $normalizedAttributes[] = [
                            'category_attribute_id' => (int) $attributeId,
                            'option_id' => (int) $optionId,
                            'custom_value' => null,
                        ];
                    }

                    continue;
                }

                if ($payload !== null && $payload !== '') {
                    $normalizedAttributes[] = [
                        'category_attribute_id' => (int) $attributeId,
                        'option_id' => (int) $payload,
                        'custom_value' => null,
                    ];
                }
            }

            if (empty($normalizedAttributes)) {
                continue;
            }

            $imagePath = null;
            if ($request->hasFile("variants.$index.image")) {
                $imagePath = ImageStorage::storeWebp(
                    $request->file("variants.$index.image"),
                    'products/variants',
                    'public',
                    82,
                    "variants.$index.image"
                );
            }

            $variantStock = isset($variantData['stock']) && $variantData['stock'] !== ''
                ? (int) $variantData['stock']
                : 0;

            $variant = $product->variants()->create([
                'sku' => $variantData['sku'] ?? null,
                'price' => ($variantData['price'] ?? null) !== '' ? $variantData['price'] : null,
                'stock' => $variantStock,
                'image' => $imagePath,
                'is_active' => (bool) ($variantData['is_active'] ?? true),
            ]);

            foreach ($normalizedAttributes as $normalizedAttribute) {
                $variant->attributes()->create([
                    'category_attribute_id' => $normalizedAttribute['category_attribute_id'],
                    'option_id' => $normalizedAttribute['option_id'],
                    'custom_value' => $normalizedAttribute['custom_value'],
                ]);
            }

            $totalStock += $variantStock;
            $createdAny = true;
        }

        $product->has_variants = $createdAny;
        $product->stock = $createdAny ? $totalStock : (int) ($request->input('stock') ?? 0);
        $product->save();
    }

    private function resolveFinalCategoryOrFail(int $categoryId, ?int $subcategoryId): Category
    {
        $finalCategoryId = $subcategoryId ?: $categoryId;
        $finalCategory = Category::find($finalCategoryId);

        if (!$finalCategory) {
            throw ValidationException::withMessages([
                'subcategory_id' => 'Categoria finala selectata nu exista.',
            ]);
        }

        if (!$finalCategory->isLeaf()) {
            throw ValidationException::withMessages([
                'subcategory_id' => 'Trebuie selectata categoria finala, nu una intermediara.',
            ]);
        }

        return $finalCategory;
    }

    private function validateCatalogPayload(
        Category $finalCategory,
        array $attributes,
        array $variants,
        bool $hasVariantsEnabled,
        bool $isUpdate,
        ?Product $product = null
    ): void {
        $categoryAttributes = CategoryAttribute::with('options')
            ->where('category_id', $finalCategory->id)
            ->get()
            ->keyBy(fn (CategoryAttribute $attribute) => (string) $attribute->id);

        $errors = [];

        foreach ($categoryAttributes as $attribute) {
            if (!$attribute->is_required || $attribute->is_variant) {
                continue;
            }

            $value = $attributes[$attribute->id] ?? null;

            if ($attribute->type === 'multiselect') {
                $selected = array_values(array_filter((array) $value, fn ($item) => $item !== null && $item !== ''));
                if (empty($selected)) {
                    $errors["attributes.{$attribute->id}"] = "Atributul '{$attribute->name}' este obligatoriu.";
                }
                continue;
            }

            if ($attribute->type === 'boolean') {
                continue;
            }

            if ($value === null || $value === '') {
                $errors["attributes.{$attribute->id}"] = "Atributul '{$attribute->name}' este obligatoriu.";
            }
        }

        foreach ($attributes as $attributeId => $value) {
            $attribute = $categoryAttributes->get((string) $attributeId);

            if (!$attribute || $attribute->is_variant) {
                $errors["attributes.{$attributeId}"] = 'Atribut invalid pentru categoria selectata.';
                continue;
            }

            if ($attribute->type === 'select') {
                if ($value !== null && $value !== '' && !$attribute->options->contains('id', (int) $value)) {
                    $errors["attributes.{$attributeId}"] = "Valoarea selectata pentru '{$attribute->name}' este invalida.";
                }
            }

            if ($attribute->type === 'multiselect') {
                foreach ((array) $value as $optionId) {
                    if ($optionId !== null && $optionId !== '' && !$attribute->options->contains('id', (int) $optionId)) {
                        $errors["attributes.{$attributeId}"] = "Una dintre valorile pentru '{$attribute->name}' este invalida.";
                        break;
                    }
                }
            }
        }

        if ($hasVariantsEnabled && (!is_array($variants) || empty($variants))) {
            $errors['variants'] = 'Produsul este setat cu variante, dar nu exista combinatii salvate.';
        }

        if (is_array($variants) && !empty($variants)) {
            $variantAttributeMap = $categoryAttributes->filter(fn (CategoryAttribute $attribute) => $attribute->is_variant);
            $seenCombinations = [];
            $seenSkus = [];

            foreach ($variants as $index => $variant) {
                $variantPath = "variants.$index";
                $variantAttributes = $variant['attributes'] ?? [];

                if (!is_array($variantAttributes) || empty($variantAttributes)) {
                    $errors[$variantPath] = 'Fiecare varianta trebuie sa aiba cel putin un atribut.';
                    continue;
                }

                $normalizedCombination = [];

                foreach ($variantAttributes as $attributeId => $payload) {
                    $attribute = $variantAttributeMap->get((string) $attributeId);

                    if (!$attribute) {
                        $errors["$variantPath.attributes.$attributeId"] = 'Atribut de varianta invalid pentru categoria selectata.';
                        continue;
                    }

                    if (is_array($payload)) {
                        $optionId = $payload['option_id'] ?? null;
                        $customValue = trim((string) ($payload['custom_value'] ?? ''));

                        if ($customValue !== '') {
                            $normalizedCombination[] = $attributeId . ':custom:' . mb_strtolower($customValue);
                            continue;
                        }

                        if ($optionId !== null && $optionId !== '') {
                            if (!$attribute->options->contains('id', (int) $optionId)) {
                                $errors["$variantPath.attributes.$attributeId"] = "Valoarea de varianta pentru '{$attribute->name}' este invalida.";
                                continue;
                            }

                            $normalizedCombination[] = $attributeId . ':option:' . (int) $optionId;
                        }

                        continue;
                    }

                    if ($payload === null || $payload === '') {
                        continue;
                    }

                    if (!$attribute->options->contains('id', (int) $payload)) {
                        $errors["$variantPath.attributes.$attributeId"] = "Valoarea de varianta pentru '{$attribute->name}' este invalida.";
                        continue;
                    }

                    $normalizedCombination[] = $attributeId . ':option:' . (int) $payload;
                }

                sort($normalizedCombination);
                $combinationKey = implode('|', $normalizedCombination);

                if ($combinationKey === '') {
                    $errors[$variantPath] = 'Varianta trebuie sa aiba valori valide.';
                } elseif (isset($seenCombinations[$combinationKey])) {
                    $errors[$variantPath] = 'Exista variante duplicate cu aceeasi combinatie de atribute.';
                } else {
                    $seenCombinations[$combinationKey] = true;
                }

                $sku = trim((string) ($variant['sku'] ?? ''));
                if ($sku !== '') {
                    $skuKey = mb_strtolower($sku);
                    if (isset($seenSkus[$skuKey])) {
                        $errors["$variantPath.sku"] = 'SKU-ul trebuie sa fie unic intre variantele produsului.';
                    } else {
                        $seenSkus[$skuKey] = true;
                    }
                }

                if (!array_key_exists('price', $variant) || $variant['price'] === null || $variant['price'] === '') {
                    $errors["$variantPath.price"] = 'Fiecare varianta trebuie sa aiba pret.';
                }
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
