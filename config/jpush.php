<?php
/**
 * Created by PhpStorm.
 * @description:
 * @author: FS
 * @date: 2018-06-26 14:57
 */
return [
    'appKey' => env('JPUSH_APP_KEY', 'fbd619f1ae0f88f5f538ac3f'),
    'masterSecret' => env('JPUSH_MASTER_SECRET', 'e14a670568a2e60f8dab5c67'),
    'logFile' => env('JPUSH_LOG_FILE', storage_path('logs') . DIRECTORY_SEPARATOR . 'jpush.log')
];
