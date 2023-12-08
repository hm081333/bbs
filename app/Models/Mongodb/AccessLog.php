<?php

namespace App\Models\Mongodb;

use Jenssegers\Mongodb\Eloquent\Model;

class AccessLog extends Model
{
    // 库名
    protected $connection = 'mongodb';
    // 集合名
    public $collection = 'access_log';
    const UPDATED_AT = null;
    protected $guarded = [];

    public function __construct(array $attributes = [], $daily_collection = true)
    {
        if ($daily_collection) $this->collection .= '/' . date('Y-m-d');
        parent::__construct($attributes);
    }

}
