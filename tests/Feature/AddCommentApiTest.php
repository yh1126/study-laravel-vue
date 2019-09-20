<?php

namespace Tests\Feature;

use App\Models\Photo;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddCommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_コメントを追加できる()
    {
        factory(Photo::class)->create();
        $photo = Photo::first();

        $content = 'sample content';

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.comment', [
                'photo' => $photo->id,
            ]), compact('content'));

        $comments = $photo->comments()->get();

        // 記事に従って書いてるけど、テスト三分割した方が良さそう
        $response->assertStatus(201)
        // JSONフォーマットが期待通りであること
        ->assertJsonFragment([
            "author" => [
                "name" => $this->user->name,
            ],
            "content" => $content,
        ]);

        // DBにコメントが一見登録されていること
        $this->assertEquals(1, $comments->count());
        // 内部がAPIでリクエストしたものであること
        $this->assertEquals($content, $comments[0]->content);
    }
}
