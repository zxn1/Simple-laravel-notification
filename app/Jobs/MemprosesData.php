<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use App\Mail\testEmail;

class MemprosesData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        //
        $this->emailData = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    //sini boleh jadi APA-APA Job/task yang boleh delayed
    //so letak dalam job ni untuk jadi queue nanti untuk improve performance

    public function handle()
    {
        sleep(5);
        Mail::to($this->emailData)->send(new testEmail());
    }
}
