<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
use App\Http\Requests\StoreComment;
use App\Models\Comment;
use App\Models\Photo;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function __construct()
    {
        // 認証が必要
        // しかし、indexだけメソッドだけ認証なしでも観れるようにする
        // コントローラを分けた方が良さそうな気がするなぁ
        $this->middleware('auth')->except(['index', 'show', 'download']);
    }

    public function index()
    {
        // TODO：serviceに切り出したい
        $photos = Photo::with(['owner'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();

        // コントローラからインスタンスをreturnすると自動的にjsonに変換されてレスポンスが生成される
        return $photos;
    }

    public function show(string $id)
    {
        $photo = Photo::where('id', $id)->with(['owner', 'comments.author'])->first();

        return $photo ?? abort(404);
    }

    /**
     * 写真投稿
     * @param Storephoto $request
     * @return \Illuminate\Http\Response
     */
    public function create(StorePhoto $request)
    {
        // 投稿写真の拡張子を取得する
        // photoはFileオブジェクトでFileオブジェクトはFileHelperに定義されているメソッドを使用できる
        $extension = $request->photo->extension();

        $photo = new Photo();
        // インスタンス生成時に割り振られたランダムなID値と
        // 本来の拡張子を組み合わせえてファイル名とする
        $photo->filename = $photo->id . '.' . $extension;

        // $s3にファイルを保存する
        // 第4引数の'public'はファイルを公開状態で保存するため
        try {
            Storage::cloud()->putFileAs('photos', $request->photo, $photo->filename, 'public');
        } catch (ConnectException $exception) {
            Log::error('S3通信エラー');
            Log::error($exception->getHandlerContext());
            throw $exception;
        } catch (\Exception $exception) {
            Log::error($exception);
            throw $exception;
        }
        // データベースエラー時にファイル削除を行うため
        // トランザクションを利用する
        DB::beginTransaction();

        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('DB登録エラー');
            DB::rollBack();
            // DBとの不整合を避けるためアップロードしたファイルを削除
            Storage::cloud()->delete($photo->filename);
            throw $exception;
        }
        Log::info('DB & S3登録成功');
        // リソースの新規作成なので
        // レスポンスコードは201(CREATED)を返却する
        return response($photo, 201);
    }

    public function download(Photo $photo)
    {
        // 写真の存在をチェックする
        if (! Storage::cloud()->exists('photos/' . $photo->filename)) {
            abort(404);
        }

        $headers = [
            'Content-type' => 'application/octet-stream', //ファイルの種類は無視するようなMIMEタイプを返す
            'Content-Disposition' => 'attachment; filename="' . $photo->filename . '"',
        ];

        return response(Storage::cloud()->get('photos/' . $photo->filename), 200, $headers);
    }

    public function addComment(Photo $photo, StoreComment $request)
    {
        $comment = new Comment();
        // コメント作成処理などはサービスに切り出したほうがテストしやすそう
        // 後、この処理ってオブジェクト破壊だった気がするのでテスト以外でこの処理は書きたくない
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        // authorリレーションをロードするためにコメントを取得しなおす
        // 別のメソッドに切り出すべき
        // 以前このメソッドでコメントの追加とコメントの取得をしているので仕事しすぎ
        $new_comment = Comment::where('id', $comment->id)->with('author')->first();
        return response($new_comment, 201);
    }

    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        // 一度しかいいねがつかないようにするため、detachしてattachする
        $photo->likes()->detach(Auth::user()->id);
        $photo->likes()->attach(Auth::user()->id);

        return ["photo_id" => $id];
    }

    public function unlike(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (! $photo) {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);

        return ["photo_id" => $id];
    }
}
