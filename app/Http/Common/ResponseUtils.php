<?php
/**
 * Created by PhpStorm.
 * @description: RestFull响应工具类
 * @author: VAIO
 * @date: 2018-04-07 0:34
 */

namespace App\Http\Common;

use Symfony\Component\HttpFoundation\Response;

class ResponseUtils
{
    /**
     * 200 OK - [GET]：服务器成功返回用户请求的数据，该操作是幂等的（Idempotent）
     *
     * @param null $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function ok($message = null, $data = null)
    {
        return self::build(true, Response::HTTP_OK, $message, $data);
    }


    /**
     * 201 Created - [POST/PUT/PATCH]：用户新建或修改数据成功
     *
     * @param null $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function created($message = null, $data = null)
    {
        return self::build(true, Response::HTTP_CREATED, $message, $data);
    }

    /**
     * 202 Accepted - [*]：表示一个请求已经进入后台排队（异步任务）
     *
     * @param null $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function accepted($message = null, $data = null)
    {
        return self::build(true, Response::HTTP_ACCEPTED, $message, $data);
    }

    /**
     * 204 No Content - [DELETE]：用户删除数据成功，无数据返回
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function noContent()
    {
        return self::build(true, Response::HTTP_NO_CONTENT, null, null);
    }


    /**
     * 400 Bad Request - [*]：请求参数有误
     *
     * @param null $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function badRequest($message = null, $data = null)
    {
        return self::build(false, Response::HTTP_BAD_REQUEST, $message, $data);
    }

    /**
     * 401 Unauthorized - [*]：表示用户没有权限（令牌、用户名、密码错误）
     *
     * @param null $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unauthorized($message = null, $data = null)
    {
        return self::build(false, Response::HTTP_UNAUTHORIZED, $message, $data);
    }

    /**
     * 403 Forbidden - [*] 表示用户得到授权（与401错误相对），但是访问是被禁止的
     *
     * @param null $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function forbidden($message = null, $data = null)
    {
        return self::build(false, Response::HTTP_FORBIDDEN, $message, $data);
    }


    /**404 Not Found - [*]：用户发出的请求针对的是不存在的记录，服务器没有进行操作，该操作是幂等的。
     *
     * @param null $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound($message = null, $data = null)
    {
        return self::build(false, Response::HTTP_NOT_FOUND, $message, $data);
    }

    /**
     * 406 Not Acceptable - [GET]：用户请求的格式不可得（比如用户请求JSON格式，但是只有XML格式）。
     *
     * @param null $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notAcceptable($message = null, $data = null)
    {
        return self::build(false, Response::HTTP_NOT_ACCEPTABLE, $message, $data);
    }

    /**
     * 422  Unprocesable entity - [POST/PUT/PATCH] 当创建一个对象时，发生一个验证错误。
     *
     * @param null $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unprocesableEntity($message = null, $data = null)
    {
        return self::build(false, Response::HTTP_UNPROCESSABLE_ENTITY, $message, $data);
    }

    /**
     * 500 Internal Server Error - [*]：服务器发生错误，用户将无法判断发出的请求是否成功。
     *
     * @param null $message
     * @param null $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message = null, $data = null)
    {
        return self::build(false, Response::HTTP_INTERNAL_SERVER_ERROR, $message, $data);
    }


    /**
     * 构建http响应体
     *
     * @param null $bool
     * @param null $status
     * @param null $message
     * @param null $data
     * @param array $headers
     * @param int $options
     * @return \Illuminate\Http\JsonResponse
     */
    public static function build($bool = null, $status = null, $message = null, $data = null,
                                 array $headers = [], $options = 0)
    {
        $status = static::getStatus($status);

        $responseBody = new ResponseBody($bool, $status,
            self::getHttpStatusText($message, $status), $data, request()->url(), time());

        return response()->json($responseBody, $status, $headers, $options);
    }

    /**
     * 获取http状态码的描述
     *
     * @param $message
     * @param $status
     * @return string
     */
    private static function getHttpStatusText($message, $status)
    {
        return $message ?: (isset(Response::$statusTexts[$status]) ? Response::$statusTexts[$status] : 'unknown status');
    }

    /**
     * 获取http状态码
     *
     * @param $code
     * @return int
     */
    private static function getStatus($code)
    {
        $errorCode = 500;

        if ($code == $errorCode || $code < 100 || $code > 600) {
            return $errorCode;
        }

        return $code;
    }
}
