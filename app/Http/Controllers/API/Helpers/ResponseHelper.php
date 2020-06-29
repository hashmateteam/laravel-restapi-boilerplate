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
        $missing_scope = '';
        $request_scope = '';
        $scopes = '';
        $joiner = false;
        if(isset($this->scopes[$name]['joiner'])){
            $joiner = array();
            foreach($this->scopes[$name]['joiner'] as $scope){
                array_push($joiner,$scope);
            }    
        }
        $join_buble = true;
        $scope_buble = false;
        $missing_scope = 'atleast_one(';
        foreach($this->scopes[$name] as $scope){
            if(!is_array($scope)){
                $scopes = $scopes .$scope;
                if ($this->request->user()->tokenCan($scope)) {
                    $request_scope = $request_scope . $scope . ",";
                    $scope_buble = true;
                }else{
                    $missing_scope = $missing_scope . $scope . ',';
                }
                $scopes = $scopes .",";
            }
        }
        $missing_scope = $missing_scope . ')';
        if($scope_buble){
            $missing_scope = '';
        }
        $scopes = str_limit($scopes, strlen($scopes) - 1,'');
        $request_scope = str_limit($request_scope, strlen($request_scope) - 1,'');
        if($scope_buble){
            if($joiner != false){
                $scopes = $scopes .",joiner=>[required(";
                foreach($this->scopes[$name]['joiner'] as $scope){
                    $scopes = $scopes . $scope. ',';
                    $request_scope = $request_scope . ",";
                    if (!$this->request->user()->tokenCan($scope)) {
                        $join_buble = false;
                        $missing_scope = $missing_scope . $scope .',';
                    }else{
                        $request_scope = $request_scope . $scope . ",";
                    }
                }
                $scopes = str_limit($scopes, strlen($scopes) - 1,'');
                $scopes = $scopes .")]";
                $request_scope = str_limit($request_scope, strlen($request_scope) - 1,'');
                $missing_scope = str_limit($missing_scope, strlen($missing_scope) - 1,'');
            }
            if($join_buble){
                return true;
            }
        }
        $this->errors   = ['received_scopes'=>$request_scope,'required_scopes'=>$scopes,'missing_scopes'=>$missing_scope,'requested_method'=>$name,'type' => 'scope_not_included', 'alert' => 'The Authenticated token code is valid but may have not permission to do this operation.'];
        $this->code     = 400;
        $this->data     = '';
        $this->message  = "You are not authorized to access this function with with limited scope token.";
        return false;
    }
}