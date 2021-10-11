<?php

namespace Modules\Icommercepaymentez\Services;

class PaymentezService
{

	public function __construct(){

	}

	/**
    * Make configuration to view in JS
    * @param 
    * @return Object Configuration
    */
	public function makeConfiguration($paymentMethod,$order,$transaction){


	 	$conf['clientAppCode'] = $paymentMethod->options->clientAppCode ?? null;
	 	$conf['clientAppKey'] = $paymentMethod->options->clientAppKey ?? null;
	 	$conf['locale'] = locale();

	 	$conf['envMode'] = 'stg';
	 	if($paymentMethod->options->mode!='sandbox')
	 		$conf['envMode'] = 'prod';


	 	$conf['description'] = paymentezGetOrderDescription($order);
	 	$conf['reedirectAfterPayment'] = $order->url;

	 	$conf['order'] = $order;

	 	return json_decode(json_encode($conf));
               
	}

    public function makeConfigurationToGenerateLink($paymentMethod,$order){
        
        $config = config('asgard.icommercepaymentez.config');

        $conf['endPoint'] = $config['apiUrl']['linkToPay']['sandbox'];
        if($paymentMethod->options->mode!='sandbox')
            $conf['endPoint'] = $config['apiUrl']['linkToPay']['production'];


        $params = array(
            "user" => [
                    "id" => $order->customer_id,
                    "email"  => $order->email,
                    "name" => $order->first_name,
                    "last_name" => $order->last_name
            ],
            "order" => [
                    "dev_reference" => $order->id,
                    "description" => paymentezGetOrderDescription($order),
                    "amount" => $order->total,
                    "order_vat" => 0,
                    "installments_type"=> -1,
                    "currency"=> $order->currency_code
            ],
            "configuration" => [
                "partial_payment" => false,
                "expiration_time"=> $config['linkToPay']['expirationTime'],
                "allowed_payment_methods" => $paymentMethod->options->allowedPaymentMethods ?? ['All'],
                "success_url" => route('icommercepaymentez.confirmation',$order->id),
                "failure_url" => route('icommercepaymentez.confirmation',$order->id),
                "pending_url" => route('icommercepaymentez.confirmation',$order->id),
                "review_url" => route('icommercepaymentez.confirmation',$order->id)
            ]
        );

        $conf['params'] = $params;

        return $conf;


    }

	/**
     * Get Status to Order
     * @param Paymentez Status
     * @return Int 
     */
	public function getStatusOrder($cod){

        switch ($cod) {

            case "success": // Aceptada
                $newStatus = 13; //processed
            break;

            case "pending": // Reviewed transaction
                $newStatus = 11; //pending
            break;

            case "failure": // Fallida
                $newStatus = 7; //failed
            break;

        }
        
        \Log::info('Icommercepaymentez: New Order Status: '.$newStatus);

        return $newStatus; 

    }

    /**
     * Get Status Detail
     * @param Paymentez Status Detail
     * @return Int 
     */
	public function getStatusDetail($cod){

        switch ($cod) {

            case 3:
                $statusDetail = "Cod 3 - Approved transaction"; 
                $newStatus = 13; //processed
            break;

            case 9:
               $statusnDetail = "Cod 9 - Denied transaction";
                $newStatus = 7; //failed
            break;

            case 1:
               $statusDetail = "Cod 1 - Reviewed transaction";
               $newStatus = 11; // Pending
            break;

            case 11:
                $statusDetail = "Cod 11 - Rejected by fraud system transaction";
                $newStatus = 7; //failed
            break;

            case 12:
                $statusDetail = "Cod 12 - Card in black list";
                $newStatus = 7; //failed
            break;

        }

        $allNewStatus['order'] = $newStatus;
        $allNewStatus['detail'] = $newStatus;
        
        \Log::info('Icommercepaymentez: New Order Status: '.$newStatus);
        \Log::info('Icommercepaymentez: Status Detail: '.$statusDetail);

        return $allNewStatus; 

    }

    /**
     * Generate Stoken
     * @param 
     * @return 
     */
    public function generateStoken(){

        //MD5 hash of [transaction_id]_[application_code]_[user_id]_[app_key]

    }

    

}