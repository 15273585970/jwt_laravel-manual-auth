<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $status_code = Response::HTTP_OK;

    public function __construct()
    {

    }

    /**
     * 接口请求成功返回格式
     * @param array $data 输出数据
     * @param string $status_code 状态码
     * @param string $msg 输出信息
     */
    public function success($data, $status_code = Response::HTTP_OK, $msg = '请求成功')
    {
        return response()->json(['status' => 'success', 'code' => $status_code, 'msg' => $msg, 'data' => $data], $status_code, [], JSON_NUMERIC_CHECK);
    }

    /**
     * 接口请求失败返回格式
     * @param array $data 输出数据
     * @param string $status_code 状态码
     * @param string $msg 输出信息
     */
    public function fail($data = '', $status_code = Response::HTTP_BAD_REQUEST, $msg = '请求成功')
    {
        return response()->json(['status' => 'failed', 'code' => $status_code, 'msg' => $msg, 'data' => $data]);
    }
}
