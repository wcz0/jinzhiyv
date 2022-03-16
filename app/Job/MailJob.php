<?php

declare(strict_types=1);

namespace App\Job;

use App\Utils\Mail;
use Hyperf\AsyncQueue\Job;

class MailJob extends Job
{
    protected $email;
    public function __construct($email)
    {
        $this->email = $email;
    }

    public function handle()
    {
        $mail = new Mail();
        $mail->send($this->email, '恭喜, 你抢到了!', '邮件内容');
    }
}
