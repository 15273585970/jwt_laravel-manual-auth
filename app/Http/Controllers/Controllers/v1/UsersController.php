<?php

namespace App\Http\Controllers\Controllers\v1;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Service\UserServices;
class UsersController extends ApiController
{
    public function login(Request $request)
    {
        //验证用户信息
        $validator = Validator::make( $request->all(),[
            'username' => 'required',
            'password' => 'required'
        ],[
            'username.required' => '用户名为必填项',
            'password.required' => '密码为必填项'
        ]);
        if ( $validator->fails() ) {
            return $this->fail('','',$validator->errors()->first());
        }
        $user = $this->UserServices->login($request->all());
//        $token = $this->UserServices->getToken();
//        if ( !$token ) {
//            return $this->fail('',500,'token异常，请稍后再试');
//        }

    }


    /**
     * 用户账号注册
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required',
            'password' => 'required'
        ],[
            'username.required' => '用户名为必填项',
            'password.required' => '密码为必填项'
        ]);
        if ( $validator->fails() ) {
            return $this->fail('','',$validator->errors()->first());
        }
        $result = $this->UserService->registerUser( $request->all() );
        if ( !is_numeric( $result ) ) {
            return $this->fail('',500,$request);
        }
        return $this->success('',200,'注册成功');
    }
}
