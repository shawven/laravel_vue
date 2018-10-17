<?php
/**
 * Created by PhpStorm.
 * @description: SQL监听器
 * @author: FS
 * @date: 2018-04-10 10:54
 */
namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;

class SqlListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * @param QueryExecuted $event
     */
    public function handle(QueryExecuted $event)
    {
        $sql = str_replace("?", "'%s'", $event->sql);

        $log = '[' . date('Y-m-d H:i:s') . sprintf(' cost => %.5f', $event->time / 1000) . 's]: '
            . vsprintf($sql, $event->bindings) . "\r\n";

        $filePath = storage_path('logs\sql.log');

        file_put_contents($filePath, $log, FILE_APPEND);
    }
}
