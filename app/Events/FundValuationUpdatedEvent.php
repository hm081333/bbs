<?php

namespace App\Events;

use App\Models\Fund\FundValuation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * 基金估值更新事件
 */
class FundValuationUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public FundValuation $fundValuation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(FundValuation $fundValuation)
    {
        $this->fundValuation = $fundValuation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // 公有通道，对应基金代码
        return new Channel('fund.' . $this->fundValuation->code);
    }
}
