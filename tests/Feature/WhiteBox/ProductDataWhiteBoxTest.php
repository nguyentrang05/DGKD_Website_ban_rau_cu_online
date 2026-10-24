<?php

namespace Tests\Feature\WhiteBox;

class ProductDataWhiteBoxTest extends ExtendedWhiteBoxTestCase
{
    public function test_PD01_catalogue_passes_real_fields_and_image_branches_to_javascript(): void
    {
        $image = $this->newProduct();
        $noImage = $this->newProduct(['image_url' => null]);
        $response = $this->get(route('products.index'));
        $response->assertOk()->assertViewIs('products.index');
        $products = collect($response->viewData('allProducts'))->keyBy('id');
        $this->assertCount(2, $products);
        $row = $products[$image->id];
        $this->assertSame($image->name, $row['name']);
        $this->assertEquals(15000, $row['price']);
        $this->assertSame('kg', $row['unit']);
        $this->assertSame(10, (int) $row['stock_quantity']);
        $this->assertSame('extended-wb', $row['category']['slug']);
        $this->assertStringEndsWith('/storage/whitebox/product.jpg', $row['image_url']);
        $this->assertNull($products[$noImage->id]['image_url']);
    }

    public function test_PD02_empty_catalogue_returns_empty_dataset(): void
    {
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $this->assertCount(0, $response->viewData('allProducts'));
    }

    public function test_PD03_existing_product_detail_can_be_opened(): void
    {
        $product = $this->newProduct();
        $response = $this->get(route('products.show', $product->id));
        $response->assertOk()->assertViewIs('products.show')->assertSee($product->name);
        $this->assertSame($product->id, $response->viewData('product')->id);
    }

    public function test_PD04_missing_product_detail_returns_404(): void
    {
        $this->get(route('products.show', 999999))->assertNotFound();
    }
}
