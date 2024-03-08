<?php

namespace App\Http;

class Request extends \Illuminate\Http\Request
{
    public function expectsJson()
    {
        return true;
    }

    public function wantsJson()
    {
        return true;
    }
}
