<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductImage;
use App\Models\ProductLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_products()
    {
        Product::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/product');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_create_product_with_links_and_images()
    {
        $payload = [
            'product_name' => 'New Product',
            'product_price' => 500,
            'description' => 'A great product',
            'title_links' => ['Official Site', 'Support'],
            'links' => ['https://example.com', 'https://support.com'],
            'file_name' => ['image_one', 'image_two']
        ];

        $file1 = UploadedFile::fake()->image('prod1.jpg');
        $file2 = UploadedFile::fake()->image('prod2.jpg');

        $response = $this->postJson('/api/product/create', array_merge($payload, [
            'image_one' => [$file1],
            'image_two' => [$file2],
        ]));

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['product_name' => 'New Product']);
        $this->assertDatabaseHas('product_links', ['title' => 'Official Site']);
        $this->assertDatabaseCount('product_images', 2);
    }

    public function test_can_update_product()
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);

        $payload = [
            'product_name' => 'Updated Name',
            'product_price' => 600,
            'description' => 'Updated description',
        ];

        $response = $this->postJson("/api/product/update/{$product->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'product_name' => 'Updated Name']);
    }

    public function test_can_delete_product_image()
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);
        $image = ProductImage::create(['prod_id' => $product->id, 'file_name' => 'test.jpg']);

        $response = $this->deleteJson("/api/product/image/{$image->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
    }

    public function test_can_delete_product_link()
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);
        $link = ProductLink::create(['prod_id' => $product->id, 'title' => 'Test Link', 'links' => 'http://test.com']);

        $response = $this->deleteJson("/api/product/link/{$link->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('product_links', ['id' => $link->id]);
    }
}
