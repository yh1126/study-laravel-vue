<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutApitTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() :void
    {
        parent::setUp();

        // テストユーザーの作成
        $this->user = factory(User::class)->create();
    }

    /**
     *  @test
     */
    public function should_認証済みのユーザーをログアウトさせる()
    {
        // ログアウトされることを確認するためにはじめにログイン(actingAs)する
        $response = $this->actingAs($this->user)
                        ->json('POST', route('logout'));

        $response->assertStatus(200); //logoutを実行して200が返ってくるとログアウトできていると判定
        $this->assertGuest(); // ユーザーが認証されていないことを確認するためのテスト
    }
}
