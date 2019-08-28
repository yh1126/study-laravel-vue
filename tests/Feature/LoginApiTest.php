<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginApiTest extends TestCase
{
    use RefreshDatabase;
    /**
     * テストのため、ユーザ作成処理を入れておく
     */
    public function setUp() :void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_登録済みのユーザーを認証して返却する()
    {
        $response = $this->json('POST', route('login'), [
            'email'    => $this->user->email,
            'password' => 'password', //参考にしたサイトではsecretになっていたので注意(Laravelのバージョンが違うせい)
            // setUpで作っているユーザは「daabase/factory/UserFactory」を元に作っているので、パスワードなのはそのファイルを参照
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(['name' => $this->user->name]);

        $this->assertAuthenticatedAs($this->user);
    }
}
