<?php
namespace App\Http\Controllers\API\Helpers;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Carbon; 
use Validator;
class ResponseHelper extends Controller {
    
    public $request;
    public $inputs;
    public $errors;
    public $message;
    public $code;
    public $data;
    public $ip;
    public $scopes;
    public $func_name;
    
    public function __construct(Request $request,$scopes=false,$func_name=''){
        $this->request = $request;
        $this->inputs = $request->all();
        $this->ip = $request->ip();
        $this->scopes = $scopes;
        $this->func_name = $func_name;
    }

    public function validater($array){
        if($this->scopes != false){
            if(!$this->scope_limiter($this->func_name,$this->request)){
                return false;
            }
        }
        $validator = Validator::make($this->inputs, $array);
        if ($validator->fails()) { 
            $this->errors = ['type'=>'not_validated','inputs'=>$validator->errors()];
            $this->code = 400;
            $this->message = "The data you have passed has been failed in validation. See errors{} for more details.";
            return false;            
        }
        return true;
    }
    public function is_allowed(){
        if($this->scopes != false){
            if(!$this->scope_limiter($this->func_name,$this->request)){
                return false;
            }
        }
        return true;
    }
    public function json_back(){
        if($this->code === 400){
            return response()->json([
                'code'      => $this->code,
                'errors'    => $this->errors,
                'message'   => $this->message,
                'timestamp' => Carbon::now()->timestamp,
                'request_ip' => $this->ip,
            ]);
        }
        if($this->code === 200){
            return response()->json([
                'code'    => $this->code,
                'message' => $this->message,
                'data'    => $this->data,
                'timestamp' => Carbon::now()->timestamp,
                'request_ip' => $this->ip,
            ]);
        }
    }

    public function response($payload=false){
        if(!$payload){
            return $this->json_back();
        }
        $this->code = $payload["code"];
        $this->message = $payload["message"];
        $this->data = $payload["data"];
        $this->errors = $payload["errors"];
        return $this->json_back();
    }

    private function scope_limiter($name, Request $request){
        if(isset($this->scopes[$name]) && (!empty($this->scopes[$name]))){
            $missing_scope = '';
            $request_scope = '';
            $scopes = '';
            $join_buble = true;
            $scope_buble = false;
            $joiner = array();
            $missing_scope = 'atleast_one(';
            foreach($this->scopes[$name] as $key=>$scope){
                if(!is_array($scope)){
                    $scopes = $scopes .$scope;
                    if ($this->request->user()->tokenCan($scope)) {
                        $request_scope = $request_scope . $scope . ",";
                        $scope_buble = true;
                    }else{
                        $missing_scope = $missing_scope . $scope . ',';
                    }
                    $scopes = $scopes .",";
                }else{
                    $scopes = $scopes . $key;
                    if ($this->request->user()->tokenCan($key)) {
                        $request_scope = $request_scope . $key . ",";
                        $scope_buble = true;
                        $joiner[$key] = array();
                        foreach($scope as $join){
                            array_push($joiner[$key],$join);
                        }
                    }else{
                        $missing_scope = $missing_scope . $key . ',';
                    }
                    $scopes = $scopes .",";
                    
                }
            }
            if($missing_scope != ''){
                $missing_scope = str_limit($missing_scope, strlen($missing_scope) - 1,'');
                $missing_scope = $missing_scope . ')';
            }
            if($scope_buble){
                $missing_scope = '';
            }
            if($scopes != ''){
                $scopes = str_limit($scopes, strlen($scopes) - 1,'');
            }
            if($request_scope != ''){
                $request_scope = str_limit($request_scope, strlen($request_scope) - 1,'');
            }
            if($scope_buble){
                if($joiner != false){
                    foreach($joiner as $scope=>$join_array){
                        $scopes = $scopes .",$scope=>[required(";
                        foreach($join_array as $joins){
                            $scopes = $scopes . $joins. ',';
                            $request_scope = $request_scope . ",";
                            if (!$this->request->user()->tokenCan($joins)) {
                                $join_buble = false;
                                $missing_scope = $missing_scope . $joins .',';
                            }else{
                                $request_scope = $request_scope . $joins . ",";
                            }
                        }
                        $scopes = str_limit($scopes, strlen($scopes) - 1,'');
                        $scopes = $scopes .")]";
                    }
                    //$scopes = str_limit($scopes, strlen($scopes) - 1,'');
                    $request_scope = str_limit($request_scope, strlen($request_scope) - 1,'');
                    $missing_scope = str_limit($missing_scope, strlen($missing_scope) - 1,'');
                }
                if($join_buble){
                    return true;
                }
            }
            $this->errors   = ['matched_scopes'=>$request_scope,'required_scopes'=>$scopes,'missing_scopes'=>$missing_scope,'requested_method'=>$name,'type' => 'scope_not_included', 'alert' => 'The Authenticated token code is valid but may have not permission to do this operation.'];
            $this->code     = 400;
            $this->data     = '';
            $this->message  = "You are not authorized to access this function with this limited scope token.";
            return false;
        }
        return true;
    }
}