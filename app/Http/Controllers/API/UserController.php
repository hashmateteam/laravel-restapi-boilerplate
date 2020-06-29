<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon; 
use Validator;
use App\Http\Controllers\API\Helpers\ResponseHelper;

class UserController extends Controller {

    private $scopes = [
        'authin' => ['default-user'],
        'authup' => ['default-user'],
        'auth' => ['default-user','basic','joiner'=>[
            'default-admin','user_status'
        ]],
        'any' => ['default'],
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
            return $xponse->response([
                'code'    => 200,
                'message' => "[ " . $user->email . " ] is being authenticated successfully. Your bearer_token is dispatched in return data.",
                'data'    => ['token' => $user->createToken('auth',['default-user','default-admin'])->accessToken, 'user' => $user ],
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
        
        //Sending response...
        return $xponse->response([
            'code'    => 200,
            'message' => "[ " . $user->email . " ] has been registered successfully. Your bearer_token is dispatched in return data.",
            'data'    => ['token' => $user->createToken('auth',['default'])->accessToken, 'user' => $user ],
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
}