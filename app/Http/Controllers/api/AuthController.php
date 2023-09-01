<?php
namespace App\Http\Controllers\api;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['signIn','register']]);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|string|min:10|unique:users',
            'password'=>'required|string|min:6',
            'name'=>'required|string',
            'age'=>'required|string',
            'phone'=>'required|string'
            
            
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()
            ],400);
        }
        $user= DB::table('users')->insert([
            'email'=>$request->email,
            'password'=>bcrypt($request->password),
            'name'=>$request->name,
            'age'=>$request->age,
            'phone'=>$request->phone,
        ]);
        if($user==true){
            return response()->json([
                'status'=>200,
                'message'=>"Đăng kí tài khoản thành công"
            ],200);
        }
        else{
            return response()->json([
                'status'=>400,
                'message'=>"Đăng kí tài khoản thất bại"
            ],400);
        }
        

    }

    public function signIn(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|string|min:10',
            'password'=>'required|string|min:6',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()
            ],400);
        }
        $credentials = $request->only('email','password');
        $token = auth('api')->attempt($credentials);
        if(!$token){
            return response()->json([
                "status"=>401,
                "message"=>"Đăng nhập thất bại"
            ],401);
        }
       
        return $this->CreateNewToken($token);


    }

    public function CreateNewToken($token){
        return response()->json([
            "message"=>"Đăng nhập thành công",
            'access_token'=>$token,
            'expires_in' => JWTAuth::factory()->getTTL(),
            
            
            

        ],200);
    }

    public function user(){
        $user = JWTAuth::user();
        if($user->count()>0){
            return response()->json([
                'data'=>$user
            ],200);
        }
        else{
            return response()->json([
                'status' => 400,
                'error' => 'fails'
            ],400);
        }
    }


}
