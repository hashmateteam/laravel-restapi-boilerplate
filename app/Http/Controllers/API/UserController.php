<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon; 
use Illuminate\Http\Request; 
use Validator; 
use App\User; 


class UserController extends Controller {

    private $scopes = [
        'auth'  =>  ['default-user'],
        'any'   =>  ['default-admin'],
    ];

    public function authin(Request $request){ 
        //NEW RESPONSE_HELPER object
        $xponse = new ResponseHelper($request,false);
        
        //Validating
        if(!$xponse->validater([
            'email'=>'required|email:rfc,dns|exists:users',
            'password'=>'required|min:6|max:64'
        ])){
            //If fail then send response
            return $xponse->response();
        }

        //Logging in... 
        if(Auth::attempt(['email' => $xponse->inputs['email'], 'password' => $xponse->inputs['password']])){
            //Sending token...
            $user = Auth::user();
            
            $client = DB::table('oauth_clients')->where('id', 2)->first();

            $params = [
                'username' => $xponse->inputs['email'],
                'password' => $xponse->inputs['password'],
                'grant_type' => 'password',
                'client_id' => $client->id,
                'client_secret' => $client->secret,
                'scope' => 'default-user'
            ];

            $proxy = Request::create(
                'oauth/token',
                'POST',
                $params
            );
            
            $res = app()->handle($proxy);
            $response = json_decode($res->getContent(),true);
            //return Route::dispatch($proxy);

            //$token = $user->createToken('auth',['default-user']);
            $created_at = Carbon::now()->toDayDateTimeString();
            $expire_at = Carbon::now()->addSeconds($response["expires_in"])->toDayDateTimeString();
            //$expire_at = Carbon::parse($response["expires_in"])->toDayDateTimeString();

            return $xponse->response([
                'code'    => 200,
                'message' => "[ " . $user->email . " ] is being authenticated successfully. Your bearer_token is dispatched in return data.",
                'data'    => [
                    'access' =>[
                        //'response' => $response,
                        //"name" => $token->token->name,
                        'access_token' => $response["access_token"],
                        'refresh_token' => $response["refresh_token"],
                        'created_at' => $created_at ,
                        'expire_at' => $expire_at ,
                        'expires_in'=>$response["expires_in"], 
                        'expire_format'=>'seconds',
                    ],
                    'user' => $user
                 ],
                'errors'  => ''
            ]);
        }else{
            //Credentials are in correct
            return $xponse->response([
                'code'      => 400,
                'message'   => "[ ". $xponse->inputs["email"] . " ] is not being authenticated with the given credentials.",
                'data'      => '',
                'errors'    => ['type' => 'not_matched','alert' => "Your credentials didn't match our users base..."],
            ]);
        }
    }
    
    public function authup(Request $request) {
        //NEW RESPONSE_HELPER object
        $xponse = new ResponseHelper($request,false);

        //Validating
        if(!$xponse->validater([
            'name' => 'required', 
            'email' => 'required|email:rfc,dns|unique:users', 
            'password' => 'required|min:6|max:64', 
            'c_password' => 'required|same:password',
        ])){
            //If fail then send response
            return $xponse->response();
        }

        //Encrypting password...
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        
        //Create a new user
        $user = User::create($input);

        $token = $user->createToken('auth',['default-user']);
        $created_at = Carbon::parse($token->token->created_at)->toDayDateTimeString();
        $expire = Carbon::now()->diffInSeconds(Carbon::parse($token->token->expires_at));
        $expire_at = Carbon::parse($token->token->expires_at)->toDayDateTimeString();

        //Sending response...
        return $xponse->response([
            'code'    => 200,
            'message' => "[ " . $user->email . " ] has been registered successfully. Your bearer_token is dispatched in return data.",
            'data'    => [
                'access' =>[
                    "name" => $token->token->name,
                    'token' => $token->accessToken,
                    'created_at' => $created_at ,
                    'expire_at' => $expire_at ,
                    'expires_in'=>$expire, 
                    'expire_format'=>'seconds',
                    'scopes' => $token->token->scopes
                ],
                'user' => $user
             ],
            'errors'  => ''
        ]); 
    }
    
    public function auth(Request $request){ 
        //NEW RESPONSE_HELPER object
        $xponse = new ResponseHelper($request,$this->scopes,__FUNCTION__);
        if(!$xponse->is_allowed()){
            return $xponse->response();
        }

        if(Auth::check()){
            $user = Auth::user();
            return $xponse->response([
                'code'    => 200,
                'message' => "[ " . $user->email . " ] is being using this function. Authenticated user is dispatched in return data.",
                'data'    => ['user' => $user ],
                'errors'  => ''
            ]);
        }
        return $xponse->response([
            'code'    => 400,
            'message' => "You are not authorized to access this function without credentials or valid tokens.",
            'data'    => '',
            'errors'  => ['type' => 'not_valid_token', 'alert' => 'The Authenticated token code is invalid or may not be found.' ]
        ]);
    }

    public function any(Request $request){ 
        
        //NEW RESPONSE_HELPER object
        $xponse = new ResponseHelper($request,$this->scopes,__FUNCTION__);
        if(!$xponse->is_allowed()){
            return $xponse->response();
        }

        if(Auth::check()){
            $user = Auth::user();
            return $xponse->response([
                'code'    => 200,
                'message' => "[ " . $user->email . " ] is being using this function. Authenticated user is dispatched in return data.",
                'data'    => ['user' => $user ],
                'errors'  => ''
            ]);
        }
        return $xponse->response([
            'code'    => 400,
            'message' => "You are not authorized to access this function without credentials or valid tokens.",
            'data'    => '',
            'errors'  => ['type' => 'not_valid_token', 'alert' => 'The Authenticated token code is invalid or may not be found.' ]
        ]);
    }
    public function refresh_token(Request $request){
        //NEW RESPONSE_HELPER object
        $xponse = new ResponseHelper($request,false);
        
        //Validating
        if(!$xponse->validater([
            'refresh_token'=>'required'
        ])){
            //If fail then send response
            return $xponse->response();
        }
        $client = DB::table('oauth_clients')->where('id', 2)->first();

        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $xponse->inputs['refresh_token'],
            'client_id' => $client->id,
            'client_secret' => $client->secret,
        ];

        $proxy = Request::create(
            'oauth/token',
            'POST',
            $params
        );
        
        $res = app()->handle($proxy);
        $response = json_decode($res->getContent(),true);
        if(isset($response['error'])){
            return $xponse->response([
                'code'    => 400,
                'message' => "You must have to use the latest refresh token to get new access_token.",
                'data'    => '',
                'errors' => $response,
            ]);    
        }
        return $xponse->response([
            'code'    => 200,
            'message' => "Your new access_token is dispatched in return data with new refresh_token.",
            'data'    => $response,
            'errors' => '',
        ]);
    } 
}