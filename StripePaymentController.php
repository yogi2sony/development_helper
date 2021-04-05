<?php
   
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
use App\Models\Todo_comment;
use Session;
use Stripe;
   
class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        return view('stripe');
    }
  
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
        "amount" => 100 * 100,
     */
    public function stripePost(Request $request)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        /*Stripe\Charge::create ([
            "amount" => 10,
            "currency" => "usd",
            "source" => $request->stripeToken,
            "description" => "Test payment from yogesh"
        ]);
        $customer = \Stripe\Customer::create(array(
            'name' => 'Test',
            'description' => 'Test description',
            'email' => 'yogeshsoni.developer@gmail.com',
            'source' => $request->stripeToken,
            'address' => [
                'line1' => '510 Townsend St',
                'postal_code' => '98140',
                'city' => 'San Francisco',
                'state' => 'CA',
                'country' => 'US',
            ],
        ));*/

        $payment_intent = \Stripe\PaymentIntent::create([
            'description' => 'Software development services',
            'shipping' => [
                'name' => $request->name,
                'address' => [
                    'line1' => '510 Townsend St',
                    'postal_code' => '98140',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'country' => 'US',
                ],
            ],
            'amount' => 10*100,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);
  
        Session::flash('success', 'Payment successful!');
          
        return back();
    }
}