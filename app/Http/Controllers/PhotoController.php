<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
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
        $this->middleware('auth')->except(['index']);
    }

    public function index()
    {
        // TODO：serviceに切り出したい
        $photos = Photo::with(['owner'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();

        // コントローラからインスタンスをreturnすると自動的にjsonに変換されてレスポンスが生成される
        return $photos;
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
}
