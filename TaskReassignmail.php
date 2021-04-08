<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Lead;
use App\User;
use DB;
use Auth;
use Input;
use Schema;
use Redirect;
use DateTime;
use Mail;

class TaskReassignmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $items;
    public function __construct($items)
    {
        $this->items = $items;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $address = 'yogeshsoni.developer@gmail.com';
        $name = 'LEAD-CRM-SYSTEM | ADMIN';
        $subject = 'Laed re-assigned successfully.';
        $user_id = Auth::user()->id;
        $email = Auth::user()->email;
        //dd($items);
        return $this->view('sendmail.task_reassigne_mail')
                    ->from($address, $name)
                    ->to($email)
                    ->to('yogeshsoni.developer@gmail.com')
                    ->subject($subject)
                    ->cc('yogeshsoni.developer@gmail.com');
                    //->cc('adityaj772@gmail.com');
    }
}
