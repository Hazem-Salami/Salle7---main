<?php

namespace App\Services;

use App\Http\Traits\NotificationsTrait;
use App\Http\Traits\ResponseTrait;

class BaseService
{
    use ResponseTrait;
    use NotificationsTrait;
}
