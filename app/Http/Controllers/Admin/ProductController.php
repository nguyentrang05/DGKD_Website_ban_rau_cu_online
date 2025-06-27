<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    //index
    public function index()
    {
        $categories = Category::all(); // Lấy tất cả danh mục
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5)->count();
        $outOfStockProducts = Product::where('stock_quantity', '=', 0)->count();
        $activeProducts = $totalProducts - $outOfStockProducts;
        $products = Product::with('category')->paginate(4);

        return view('admin.products.index', compact('categories', 'totalProducts', 'lowStockProducts', 'activeProducts', 'outOfStockProducts', 'products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Validation rules
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('products', 'slug')
            ],
            'description' => 'required|string',
            'price' => 'required|numeric|min:0|max:999999999.99',
            'stock_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|in:kg,gram,bó,gói,hộp,chai,lon,cái,chiếc,lít',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ], [
            // Custom validation messages
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'slug.unique' => 'URL slug này đã được sử dụng.',
            'slug.regex' => 'URL slug chỉ được chứa chữ cái thường, số và dấu gạch ngang.',
            'description.required' => 'Mô tả sản phẩm là bắt buộc.',
            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá sản phẩm phải là số.',
            'price.min' => 'Giá sản phẩm không được âm.',
            'price.max' => 'Giá sản phẩm quá lớn.',
            'stock_quantity.required' => 'Số lượng tồn kho là bắt buộc.',
            'stock_quantity.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'stock_quantity.min' => 'Số lượng tồn kho không được âm.',
            'unit.required' => 'Đơn vị tính là bắt buộc.',
            'unit.in' => 'Đơn vị tính không hợp lệ.',
            'category_id.required' => 'Danh mục là bắt buộc.',
            'category_id.exists' => 'Danh mục được chọn không tồn tại.',
            'image_url.image' => 'File phải là hình ảnh.',
            'image_url.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'image_url.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ]);

        try {
            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = $this->generateUniqueSlug($validated['name']);
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image_url')) {
                $imagePath = $request->file('image_url')->store('products', 'public');
            }

            // Create product
            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'stock_quantity' => $validated['stock_quantity'],
                'unit' => $validated['unit'],
                'category_id' => $validated['category_id'],
                'image_url' => $imagePath,
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được tạo thành công!');

        } catch (\Exception $e) {
            // Delete uploaded image if product creation fails
            if (isset($imagePath) && $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra khi tạo sản phẩm. Vui lòng thử lại.');
        }
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'unit' => 'required|string',
            'description' => 'nullable|string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image_url')) {
            $validatedData['image_url'] = $request->file('image_url')->store('products', 'public');
        }

        $product->update($validatedData);

        // 4. Chuyển hướng hoặc trả về thông báo
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công.');
    }
    protected function generateUniqueSlug($name)
{
    $slug = \Str::slug($name);
    $originalSlug = $slug;
    $counter = 1;

    while (\App\Models\Product::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $counter++;
    }

    return $slug;
}

}
