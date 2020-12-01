<?php


namespace Library\WorkerMan;


class Push
{
    public static function pushMessage($client_id, $data)
    {
        $data = [
            'type' => 'push',
            'data' => $data,
        ];
        Events::sendToClient($client_id, $data);
    }
}