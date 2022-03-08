<?

namespace App\Task;

use App\Utils\Cache;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;

/**
 * @Crontab(name="AAA", rule="0 6 * * *", callback="execute", memo="这是一个示例的定时任务")
 */
class Clear
{
    public function execute()
    {
        Cache::delete('am_buy');
        Cache::delete('pm_buy');
        Cache::delete('am_max');
        Cache::delete('pm_max');
        Cache::delete('pm_goods');
        Cache::delete('am_goods');
    }
}
