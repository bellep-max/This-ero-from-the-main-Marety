<?php

namespace App\Jobs;

use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $type;

    protected $mail;

    protected $data;

    protected $from;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $mail, $data = [], $from = '')
    {
        $this->type = $type;
        $this->mail = $mail;
        $this->data = $data;
        $this->from = $from;
    }

    private function parse($data, $content)
    {
        return preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($data) {
            [$shortCode, $index] = $matches;
            if (isset($data[$index])) {
                return $data[$index];
            } else {
                /*
                 * for testing only
                 */
                // throw new Exception("Shortcode {$shortCode} not found in template id {$this->id}", 1);
            }
        }, $content);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::send([], [], function ($message) {
            $template = Email::query()->firstWhere('type', $this->type);
            $message->from($this->from ?? config('settings.admin_mail'));
            $message->to($this->mail);
            $message->subject(config('settings.mail_title') . $this->parse($this->data, $template->subject));
            $message->setBody($this->parse($this->data, $template->content), 'text/html');
        });
    }
}
