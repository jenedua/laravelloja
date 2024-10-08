<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Events\AddingTeam;
use Laravel\Socialite\Facades\Socialite;
use PhpParser\Node\Stmt\TryCatch;

class LinkedinController extends Controller
{
    CONST LINKEDIN_TYPE = 'linkedin';

    public function handleLinkedinRedirect(){

        //return Socialite::driver(static::LINKEDIN_TYPE )->redirect();
        $query = http_build_query([
            'client_id' => env('LINKEDIN_CLIENT_ID'),
            'redirect_uri' => 'http://localhost:8000/auth/linkedin/callback',
            'response_type' => 'code',
            'scope' => 'r_liteprofile r_emailaddress', // escopos que você deseja solicitar
        ]);

        return redirect('https://www.linkedin.com/oauth/v2/authorization?' . $query);


    }

    public function handleLinkedinCallback(){
        
        try {
            $user = Socialite::driver(static::LINKEDIN_TYPE )->user();

            $userExisted = User::where('oauth_id', $user->id)->where('oauth_type', static::LINKEDIN_TYPE )->first();

            if($userExisted){

                Auth::login($userExisted);

                return redirect()->route('dashboard');

            }else{
                $newUser = User::create([
                    'name' => $user->name,
                    'email' =>$user->email,
                    'oauth_id' =>$user->id,
                    'oauth_type' => static::LINKEDIN_TYPE ,
                    'password' => Hash::make($user->id)
                ]);

                AddingTeam::dispatch($newUser);

                $newUser->switchTeam($team = $newUser->ownedTeams()->create([
                    'name' => $newUser->name ."'s Team",
                    'personal_team' => false
                ]));

                $newUser->update([
                    'current_team_id' => $newUser->id
                ]);

                Auth::login($newUser);

                return redirect()->route('dashboard');
            }
            

        } catch (Exception $e) {
           dd($e);
        }

    }



}
