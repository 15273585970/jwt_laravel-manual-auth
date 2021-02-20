<?php


namespace App\Service;


use App\Models\users\Users;

class UserServices
{
    /**
     * Token生成
     */
    public function getToken()
    {
        return strtoupper(md5(uniqid(mt_rand(), true)));
    }


    /**
     * 注册用户
     */
    public function registerUser( $data )
    {
        try {
            $user = new Users();
            $user->user_name = $data['name'];
            $user->password  = $data['password'];
            $user->save();
            return 1;
        } catch ( \Exception $exception ) {
            return $exception->getMessage();
        }
    }

    public function login( $data )
    {
        $user = Users::where('user_name',$data['username'])->get();
        if ( !$user ) {
            return '账号错误';
        }
        if ( !password_verify($data['password'],$user->password)) {
            return '密码错误';
        }
        //检测密码加密值是否需要更换
    }
}
