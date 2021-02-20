<?php


namespace App\Service;


use App\Enums\users\UsersTokenState;
use App\Models\users\Users;
use App\Models\users\UsersToken;
use Illuminate\Support\Facades\DB;

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
        if (!$user) {
            return '账号错误';
        }
        if (!password_verify($data['password'],$user->password)) {
            return '密码错误';
        }
        //检测密码加密值是否需要更换
        $password_hash = '';
        if (password_needs_rehash($user->password,PASSWORD_DEFAULT)) {
            $password_hash = password_hash( $user->password, PASSWORD_DEFAULT);
        }
        //登录token
        $token = $this->getToken();
        $param = [
            'users_id' => $user->id,
            'token'    => $token,
            'express_at' => date('Y-m-d H:i:s', time() + 86400),
            'created_at' => date('Y-m-d H:i:s')
        ];
        //更新用户token信息
        DB::beginTransaction();
        try{
            UsersToken::where('users_id',$user->id)->update([
                'state' => UsersTokenState::过期,
                'deleted_at' => date('Y-m-d H:i:s')
            ]);
            $resule = Users::insert( $param );
            if (!$resule) {
                return '登录失败';
            }

            //如果密码散列值规则有变化时，更新数据库
            $user_param = [];
            if ($password_hash) {
                $user_param = [
                    'password' => $password_hash,
                    'update_at' => date('Y-m-d H:i:s')
                ];
            }
            Users::where('id',$user->id)->update($user_param);
            DB::commit();
            $user->token = $token;
            return $user;
        } catch ( \Exception $e ) {
            DB::rollBack();
            return  $e->getMessage();
        }
    }


    /**
     * 验证token是否正常
     * @param $token token值
     */
    public function checkToken( $token )
    {
        $now_date = date('Y-m-d H:i:s');
        $result = UsersToken::where('token',$token)->first();
        if (!$result) {
            return false;
        }
        //判断token是否过期
        if ( $result['express_at'] <= $now_date ) {
            $result->uodate_at = $now_date;
            $result->delete_at = $now_date;
            $result->state = UsersTokenState::过期;
            $result->save();
        }
        if ( $result->state == UsersTokenState::过期 ) {
            return false;
        }
        $user = Users::where('id',$result->users_id)->first();
        if (!$user) {
            return false;
        }
        $result->token = $token;
        return $user;
    }
}
