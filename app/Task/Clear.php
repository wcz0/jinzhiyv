<?

namespace App\Task;

use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;

/**
 * @Crontab(name="Clear", rule="* * * * * *", callback="execute", memo="这是一个示例的定时任务")
 */
class Clear
{
    public function execute()
    {
        // Cache::delete('am_buy');
        // Cache::delete('pm_buy');
        // Cache::delete('am_max');
        // Cache::delete('pm_max');
        // Cache::delete('pm_goods');
        // Cache::delete('am_goods');
        return true;
    }
}
