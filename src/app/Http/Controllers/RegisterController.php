<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ShopAdminRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Auth\Events\Registered;


class RegisterController extends Controller
{
    //一般会員登録処理
    public function register_store(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'user'
        ]);

        Auth::login($user);

        event(new Registered($user));

        return redirect()->route('registration.success');
    }

    //店舗管理会員登録ページ
    public function shop_admin_register()
    {
        if (Auth::user()->role !== 'admin' || Auth::user()->email_verified_at === null) {
            return redirect('/')->with('message', 'アクセスが許可されていません。');
        }
        return view('auth.shop_admin_register');
    }

    public function shop_admin_store(ShopAdminRequest $request)
    {
        if (Auth::user()->role !== 'admin' || Auth::user()->email_verified_at === null) {
            return redirect('/')->with('message', 'アクセスが許可されていません。');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'shop-admin',
        ]);

        event(new Registered($user));

        return redirect('/')->with('message', '店舗管理者が登録されました。');
    }
}
