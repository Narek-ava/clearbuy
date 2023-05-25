<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Socialite;
use Auth;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class GoogleSocialiteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleCallback()
    {
        try {

            $user = Socialite::driver('google')->user();
            $finduser = User::where('social_id', $user->id)->first();

            $allowed_domains = DB::table('auth_domains')->pluck('domain');

            $pos = strpos($user->email, '@');
            $user_domain = substr($user->email, $pos+1);

            if($allowed_domains->contains($user_domain))
            {
                if($finduser) {

                    Auth::login($finduser);
                    return redirect('/admin/products');

                }else{

                    try{

                        $default_role = Role::findByName('Product editors');

                    }catch(\Exception $e){ //create default role with permissions

                        $role = Role::create(['name' => 'Product editors', 'guard_name' => 'web']);
                        $role->givePermissionTo([
                          'use admin panel',
                          'view products',
                          'update products' ,
                          'view {product_id}/request_success',
                          'update {product_id}/request_success'
                        ]);
                    }

                    $newUser = User::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'social_id'=> $user->id,
                        'social_type'=> 'google',
                        'password' => encrypt('my-google')
                    ]);

                    $newUser->assignRole('Product editors');

                    Auth::login($newUser);
                    return redirect('/admin/products');
                }

            }else{

                return redirect('/login')->with([
                    'status' => 'Access denied for domain '.$user_domain,
                    //'message' => 'Access denied for domain '.$user_domain
                ]);
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
