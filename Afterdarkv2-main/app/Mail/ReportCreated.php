<?php

namespace App\Mail;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReportCreated extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    private $report;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('erocast@example.com')
            ->subject('Report #' . $this->report->id)
            ->view('frontend.mail.report')
            ->with([
                'report' => $this->report->load([
                    'user:id,name',
                    'reportable:id,title',
                ]),
            ]);
    }
}
