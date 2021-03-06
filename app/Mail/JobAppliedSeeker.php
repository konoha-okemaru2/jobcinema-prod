<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class JobAppliedSeeker extends Mailable
{
    use Queueable, SerializesModels;

    public $title = [];
    public $jobitem = [];
    public $data = [];
    public $company = [];
    public $employer = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($jobitem, $data)
    {
        $this->title = sprintf('【JOB CiNEMA】求人応募完了のお知らせ');
        $this->jobitem = $jobitem;
        $this->data = $data;
        $this->company = $jobitem->company;
        $this->employer = $jobitem->company->employer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->title)
            ->replyTo(config('mail.reply.address'))
            ->view('emails.seeker.job_applied');
    }
}
