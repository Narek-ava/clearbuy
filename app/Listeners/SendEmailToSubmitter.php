<?php

namespace App\Listeners;

use App\Events\ProductRetailerAdded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductRetailerMailToSubmitter;
use App\Models\User;

class SendEmailToSubmitter
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ProductRetailerAdded  $event
     * @return void
     */
    public function handle(ProductRetailerAdded $event)
    {
        $mail_data['name'] = $event->product->name;

        $deals = $event->product->deal;

        if(!empty($deals))
        {
            foreach($deals as $deal)
            {
                $agent_name = $deal->agent->name;
                $mail_data['retailers'][] = $agent_name;
            }
        }

        $prices = $event->product->prices;

        if(!empty($prices))
        {
            foreach($prices as $price)
            {
                $agent_name = $price->agent->name;
                $mail_data['retailers'][] = $agent_name;
            }
        }

        if(!is_null($event->product->submitter_id)) {

            $submitter_email = User::where('id', $event->product->submitter_id)->value('email');

            try {
                //Log::channel('import')->info($mail_data);
                Mail::to($submitter_email)->send(new ProductRetailerMailToSubmitter($mail_data));
            }catch(\Exception $e){ return $e->getMessage(); }
        }
    }
}
