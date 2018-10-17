<?php
/**
 * Created by PhpStorm.
 * @description: 消息响应体
 * @author: VAIO
 * @date: 2018-04-07 0:26
 */

namespace App\Http\Common;


class ResponseBody
{
    public $success;

    public $code;

    public $message;

    public $data;

    public $path;

    public $timestamp;


    /**
     * ResponseBody constructor.
     * @param $success
     * @param $code
     * @param $message
     * @param $data
     * @param $path
     * @param $timestamp
     */
    public function __construct($success, $code, $message, $data, $path, $timestamp)
    {
        $this->success = $success;
        $this->code = $code;
        $this->message = $message;
        $this->data = $data;
        $this->path = $path;
        $this->timestamp = $timestamp;
    }



}
