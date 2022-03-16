<?php

declare(strict_types=1);

namespace App\Task;

use App\Controller\IndexController;
use App\Utils\Cache;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;

class Update
{
    protected $index;

    public function __construct()
    {
        // $this->index = new IndexController();

    }
    /**
     * @Crontab(name="AmUpdate", rule="0 10 * * *")
     */
    public function amUpdate()
    {
        $this->index->am();
        $this->index->supBuy();
    }

    /**
     * @Crontab(name="pmUpdate", rule="30 13 * * *")
     */
    public function pmUpdate()
    {
        $this->index->pm();
        $this->index->supBuy();
    }

    /**
     * @Crontab(name="Day", rule="* 0 * * *")
     */
    public function day()
    {
        Cache::set('day_max', 3);
    }
}
