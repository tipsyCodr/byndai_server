<?php

namespace Tests\Feature;

use App\Models\MasterPost;
use App\Models\MasterComment;
use App\Models\User;
use App\Models\MasterIndustry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class SocialApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_post()
    {
        $industry = MasterIndustry::factory()->create();
        
        $payload = [
            'indus_type' => $industry->id,
            'title' => 'My First Post',
            'content' => 'Hello World',
            'status' => 1,
        ];

        $response = $this->postJson('/api/create-post', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('master_posts', ['title' => 'My First Post']);
    }

    public function test_can_list_posts_by_industry()
    {
        $industry = MasterIndustry::factory()->create();
        MasterPost::factory()->count(3)->create(['indus_type' => $industry->id]);

        $response = $this->getJson("/api/show-postby-industry/{$industry->id}");

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_toggle_like_on_post()
    {
        $post = MasterPost::factory()->create();

        // Like
        $response = $this->postJson("/api/toggle-like/{$post->id}");
        $response->assertStatus(200)->assertJson(['liked' => true]);
        $this->assertDatabaseHas('master_likes', ['post_id' => $post->id, 'user_id' => $this->user->id]);

        // Unlike
        $response = $this->postJson("/api/toggle-like/{$post->id}");
        $response->assertStatus(200)->assertJson(['liked' => false]);
        $this->assertDatabaseMissing('master_likes', ['post_id' => $post->id, 'user_id' => $this->user->id]);
    }

    public function test_can_toggle_save_post()
    {
        $post = MasterPost::factory()->create();

        // Save
        $response = $this->postJson("/api/save-post/{$post->id}");
        $response->assertStatus(200);
        $this->assertDatabaseHas('master_saves', ['post_id' => $post->id, 'user_id' => $this->user->id]);

        // Unsave
        $response = $this->postJson("/api/save-post/{$post->id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('master_saves', ['post_id' => $post->id, 'user_id' => $this->user->id]);
    }

    public function test_can_comment_on_post()
    {
        $post = MasterPost::factory()->create();

        $payload = [
            'post_id' => $post->id,
            'comment' => 'Great post!',
        ];

        $response = $this->postJson('/api/comment', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('master_comments', ['post_id' => $post->id, 'comment' => 'Great post!']);
    }

    public function test_can_list_comments()
    {
        $post = MasterPost::factory()->create();
        MasterComment::create(['post_id' => $post->id, 'user_id' => $this->user->id, 'comment' => 'Comment 1']);
        MasterComment::create(['post_id' => $post->id, 'user_id' => $this->user->id, 'comment' => 'Comment 2']);

        $response = $this->getJson("/api/comment/{$post->id}");

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }
}
