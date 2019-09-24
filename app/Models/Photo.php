<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Photo extends Model
{
    /** IDの桁数 */
    const ID_LENGTH = 12;

    /**
     * プライマリキーの型を変更する
     * */
    protected $keyType = 'string';

    /**
     * JSONに含める属性を追加する
     * HasAttributesを見る限りappendメソッドでも追加できそう
     */
    protected $appends = [
        'url', 'likes_count', 'liked_by_user',
    ];

    /** JSONに含めない属性 */
    protected $hidden = [
        'user_id', 'filename',
        self::CREATED_AT, //modelにconstで値を持っている const CREATED_AT = 'created_at';
        self::UPDATED_AT, //同様
    ];

    /**
     * JSONに含める属性
     * hiddenと同じ表現をしている
     * 基本的にはvisibleを使用するようにする
     * 理由：hiddenに項目を定義する場合、項目が増えた時に漏れが出る可能性が高くなる & 可読性が下がる
     */
    protected $visible = [
        'id', 'owner', 'url', 'comments',
        'likes_count', 'liked_by_user',
    ];

    protected $perPage = 15;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! array_get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    public function owner()
    {
        // userモデルとのリレーションを定義
        // メソッド名がuserではないため、第二引数に外部キーを、第三引数に親モデルの主キー、
        // 第四引数にテーブル名？を指定する
        return $this->belongsTo('App\Models\User', 'user_id', 'id', 'users');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('id', 'desc');
    }

    // likesテーブルは中間テーブルとして、photosテーブルとusersテーブルの多対多の関連性を定義する
    public function likes()
    {
        // 中間テーブルにタイムスタンプを追加する
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    public function getUrlAttribute()
    {
        // クラウドストレージのurlメソッドはs3の公開URLを返却する
        return Storage::cloud()->url($this->attributes['filename']);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }

        return $this->likes->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }

    /**
     * ランダムなID値をid属性に代入する
     */
    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }

    /**
     * ランダムなID値を生成する
     */
    private function getRandomId()
    {
        // 0~9 , a~z, A~Z, -~_ の範囲のcharを全て生成して一つずつ配列の要素とする
        $characters = array_merge(
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );

        $length = count($characters);

        $id = "";

        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }
}
