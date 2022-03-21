<?php

declare(strict_types=1);

namespace App\Job;

use App\Utils\Cache;
use Hyperf\AsyncQueue\Job;

class CacheJob extends Job
{
    public function __construct()
    {
    }

    public function handle()
    {
        Cache::set('max', Cache::get('max') - 1);
    }
}
