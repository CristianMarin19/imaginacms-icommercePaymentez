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

	 	
	 	$description = "Orden #{$order->id} - {$order->first_name} {$order->last_name}";
	 	$conf['description'] = $description;
	 	$conf['reedirectAfterPayment'] = $order->url;

	 	$conf['order'] = $order;

	 	return json_decode(json_encode($conf));
               
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
            break;

            case 9:
               $statusnDetail = "Cod 9 - Denied transaction";
            break;

            case 1:
               $statusDetail = "Cod 1 - Reviewed transaction";
            break;

            case 11:
               $statusDetail = "Cod 11 - Rejected by fraud system transaction";
            break;

            case 12:
               $statusDetail = "Cod 12 - Card in black list";
            break;

        }
        
        \Log::info('Icommercepaymentez: Status Detail: '.$statusDetail);

        return $statusDetail; 

    }

    

}