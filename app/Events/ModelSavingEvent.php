<?php

namespace App\Events;

use App\Models\BaseModel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModelSavingEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var BaseModel
     */
    public BaseModel $model;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }

}
