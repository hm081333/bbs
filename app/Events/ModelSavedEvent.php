<?php

namespace App\Events;

use App\Models\BaseModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModelSavedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var BaseModel
     */
    private $model;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return BaseModel
     */
    public function getModel(): BaseModel
    {
        return $this->model;
    }

    /**
     * 找到事件应该播出的频道。
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
