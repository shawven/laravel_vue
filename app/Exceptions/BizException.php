<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: VAIO
 * @date: 2018-04-08 23:28
 */

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;
use RuntimeException;

class BizException extends RuntimeException
{
    /**
     * BizException constructor.
     */
    public function __construct($message, \Throwable $e = null)
    {
        parent::__construct($message, 422, $e);
    }


    /**
     *
     */
    public function report()
    {
        $currentTrace = $this->getTrace()[0];
        $errorFile = '发生位置：' . $this->getFile() . "({$this->getLine()}): " ;
        $detailMessage = ($currentTrace['class'] ?? $currentTrace['class'])
            . $currentTrace['type'] . $currentTrace['function'] . '()';

        Log::error($this->getMessage() . ":\n\t". $errorFile . $detailMessage);
    }


}
