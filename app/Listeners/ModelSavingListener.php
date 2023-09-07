<?php

namespace App\Listeners;

use App\Events\ModelSavingEvent;
use App\Utils\Tools;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ModelSavingListener
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
     * Handle the event.
     *
     * @param ModelSavingEvent $event
     * @return void
     */
    public function handle(ModelSavingEvent $event)
    {
        $model = $event->model;
        //region 保存模型前，把Carbon对象转为时间戳
        foreach ($model->getAttributes() as $key => $value) {
            if ($value instanceof Carbon) $model->setAttribute($key, $value->timestamp);
        }
        //endregion
    }
}
