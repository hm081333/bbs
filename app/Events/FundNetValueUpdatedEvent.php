<?php

namespace App\Events;

use App\Models\Fund\FundNetValue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 基金净值更新事件
 */
class FundNetValueUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public FundNetValue $fundNetValue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FundNetValue $fundNetValue)
    {
        $this->fundNetValue = $fundNetValue;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // 公有通道，对应基金代码
        return new Channel('fund.' . $this->fundNetValue->code);
    }
}
