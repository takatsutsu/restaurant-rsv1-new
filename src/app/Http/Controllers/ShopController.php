<?php


namespace App\Http\Controllers;

use App\Http\Requests\ShopInfoRequest;
use App\Models\Shop;
use App\Models\Genre;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ShopController extends Controller
{
    //店舗一覧画面の表示
    public function index()
    {
        $user_id = Auth::id();
        $genres = Genre::all();
        $areas = Area::all();

        $user = Auth::user();


        // クエリビルダを使用してクエリを作成
        $query = Shop::leftJoin('favorites', function ($join) use ($user_id) {
            $join->on('shops.id', '=', 'favorites.shop_id')
            ->where('favorites.user_id', '=', $user_id);
        })
            ->select('shops.*', \DB::raw('CASE WHEN favorites.user_id IS NOT NULL THEN TRUE ELSE FALSE END AS is_favorite'))
            ->get();

        // $shops =  load('area', 'genre')->get();
        $shops = Shop::with('area', 'genre')->findMany($query->pluck('id'))->each(function ($shop) use ($query) {
            $shop->is_favorite = $query->firstWhere('id', $shop->id)->is_favorite;
        });

        return view('index', compact('shops','genres', 'areas', 'user'));
    }
    //検索後店舗一覧
    public function shop_search(Request $request)
    {
        $userId = Auth::id();
        $genres = Genre::all();
        $areas = Area::all();

        $user = Auth::user();


        $search_area = $request->search_area;
        $search_genre = $request->search_genre;
        $search_shop = $request->search_shop;

        $query = Shop::leftJoin('favorites', function ($join) use ($userId) {
            $join->on('shops.id', '=', 'favorites.shop_id')
            ->where('favorites.user_id', '=', $userId);
        })
        ->select('shops.*', \DB::raw('CASE WHEN favorites.user_id IS NOT NULL THEN TRUE ELSE FALSE END AS is_favorite'));

        if (!empty($search_area)) {
            $query->where('area_id', $search_area);
        }

        if (!empty($search_genre)) {
            $query->where('genre_id', $search_genre);
        }

        if (!empty($search_shop)) {
            $query->where('shop_name', 'like', '%' . $search_shop . '%');
        }

        $query = $query->get();

        $shops = Shop::with('area', 'genre')->findMany($query->pluck('id'))->each(function ($shop) use ($query) {
            $shop->is_favorite = $query->firstWhere('id', $shop->id)->is_favorite;
        });

        return view('index', compact('shops', 'genres', 'areas', 'search_area','search_genre', 'search_shop', 'user'));
    }

    //店舗詳細画面の表示
    public function detail($id)
    {
        $shop = Shop::with('area', 'genre')->findOrFail($id);
        return view('detail', compact('shop'));
    }
    //店舗情報の変更画面表示
    public function shop_edit()
    {
        $user = Auth::user();

        // ユーザーがショップ管理者であり、メール認証が完了していることを確認
        if ($user->role !== 'shop-admin' || $user->email_verified_at === null) {
            return redirect('/')->with('message', 'アクセスが許可されていません。');
        }
        $genres = Genre::all();
        $areas = Area::all();
        $shop = Shop::where('id', $user->shop_id)->firstOrFail();
        return view('shop_edit', compact('shop', 'genres', 'areas'));
    }
    //店舗情報の変更処理
    public function shop_update(ShopInfoRequest $request)
    {
        $user = Auth::user();

        // ユーザーがショップ管理者であり、メール認証が完了していることを確認
        if ($user->role !== 'shop-admin' || $user->email_verified_at === null) {
            return redirect('/')->with('message', 'アクセスが許可されていません。');
        }
        if (Auth::user()->shop_id !== null){
        $shop = Shop::where('id', $user->shop_id)->firstOrFail();

        $shop->shop_name = $request->shop_name;
        $shop->shop_explanation = $request->shop_explanation;
        $shop->genre_id = $request->genre_id;
        $shop->area_id = $request->area_id;
        // 変更を保存
        $shop->save();

        // 成功メッセージと共にリダイレクト
        return redirect('/')->with('message', '店舗情報を更新しました。');
        }
    }
    //店舗情報更新画面の表示
    public function shop_new()
    {
        $user = Auth::user();

        // ユーザーがショップ管理者であり、メール認証が完了していることを確認
        if ($user->role !== 'shop-admin' || $user->email_verified_at === null) {
            return redirect('/')->with('message', 'アクセスが許可されていません。');
        }
        $genres = Genre::all();
        $areas = Area::all();
        return view('shop_new', compact('genres', 'areas'));
    }
    //店舗情報更新の処理
    public function shop_store(shop_InfoRequest $request)
    {
        $user = Auth::user();

        // ユーザーがショップ管理者であり、メール認証が完了していることを確認
        if ($user->role !== 'shop-admin' || $user->email_verified_at === null ) {
            return redirect('/')->with('message', 'アクセスが許可されていません。');
        }

        // 新しい店舗情報をshopsテーブルに登録
        $shop = Shop::create([
            'shop_name' => $request->shop_name,
            'shop_explanation' => $request->shop_explanation,
            'genre_id' => $request->genre_id,
            'area_id' => $request->area_id,
        ]);

        // 登録されたショップのIDを取得して、usersテーブルのshop_idに代入
        $user->shop_id = $shop->id;
        $user->save();  // ユーザー情報を更新

        // 成功メッセージと共にリダイレクト
        return redirect('/')->with('message', '店舗情報を登録し、ユーザー情報を更新しました。');
    }

}