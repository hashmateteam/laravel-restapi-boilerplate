<?php
namespace App\Http\Controllers\API\Helpers;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use Illuminate\Support\Carbon; 
use Validator;
class ResponseHelper extends Controller {
    
    public $inputs;
    public $errors;
    public $message;
    public $code;
    public $data;
    public $ip;
    
    public function get_inputs(Request $request){
        $this->inputs = $request->all();
        $this->ip = $request->ip();
    }

    
    public function validater($array){
        $validator = Validator::make($this->inputs, $array);
        if ($validator->fails()) { 
            $this->errors = ['type'=>'not_validated','inputs'=>$validator->errors()];
            $this->code = 400;
            $this->message = "The data you have passed has been failed in validation. See errors{} for more details.";
            return false;            
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

    public function response($payload){
        $this->code = $payload["code"];
        $this->message = $payload["message"];
        $this->data = $payload["data"];
        $this->errors = $payload["errors"];
        return $this->json_back();
    }
}