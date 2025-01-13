<?php

namespace Modules\Icommercepayzen\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Icommerce\Entities\PaymentMethod;

class Icommercepaymentez extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    if (!is_module_enabled('Icommercepaymentez')) {
      $this->command->alert('This module: Icommercepaymentez is DISABLED!! , please enable the module and then run the seed');
      exit();
    }

    //Validation if the module has been installed before
    $name = config('asgard.icommercepaymentez.config.paymentName');
    $paymentMethod = PaymentMethod::where('name', $name)->first();
    $PaymentMethodRepository = app('Modules\Icommerce\Repositories\PaymentMethodRepository');


    if (!$paymentMethod) {
      $options['init'] = "Modules\Icommercepaymentez\Http\Controllers\Api\IcommercePaymentezApiController";

      $options['mode'] = 'sandbox';
      $options['serverAppCode'] = null;
      $options['serverAppKey'] = null;
      $options['clientAppCode'] = null;
      $options['clientAppKey'] = null;
      $options['type'] = 'checkout';
      $options['allowedPaymentMethods'] = [];
      $options['minimunAmount'] = 0;
      $options['maximumAmount'] = null;
      $options['showInCurrencies'] = ['COP'];

      $titleTrans = 'icommercepaymentez::icommercepaymentezs.single';
      $descriptionTrans = 'icommercepaymentez::icommercepaymentezs.description';

      $params = [
        'name' => $name,
        'status' => 1,
        'options' => $options,
      ];
      $paymentMethod = PaymentMethod::create($params);

      $this->addTranslation($paymentMethod, 'en', $titleTrans, $descriptionTrans);
      $this->addTranslation($paymentMethod, 'es', $titleTrans, $descriptionTrans);
    } else {
      if ($paymentMethod->description != trans('icommercepayzen::icommercepayzens.iaDescription', [], locale())) {
        $data = array(
          'es' => ['description' => trans('icommercepayzen::icommercepayzens.iaDescription', [], 'es')],
          'en' => ['description' => trans('icommercepayzen::icommercepayzens.iaDescription', [], 'en')]
        );
        $paymentMethod = $PaymentMethodRepository->update($paymentMethod, $data);
        //Instance file service
        $fileService = app("Modules\Media\Services\FileService");
        //Instance the file path
        $filePath = 'Modules/Icommercepayzen/Resources/img/payzen_default.png';
        if (Storage::disk('local')->exists($filePath)) {
          // Obtener el contenido del archivo
          $fileContents = Storage::disk('local')->get($filePath);
          // Convertir el archivo a base64
          $base64File = base64_encode($fileContents);
          //Get base64 file
          $uploadedFile = getUploadedFileFromBase64($base64File);
          //Create file
          $file = $fileService->store($uploadedFile, 0, 'publicmedia');
          //set file if
          $fileId = $file->id;
          //Sync file id
          $paymentMethod->files()->attach($fileId, ['zone' => 'mainimage']);
        }
      }
//      $this->command->alert("This method has already been installed !!");
    }
  }

  /*
    * Add Translations
    * PD: New Alternative method due to problems with astronomic translatable
    **/
  public function addTranslation($paymentMethod, $locale, $title, $description)
  {
    \DB::table('icommerce__payment_method_translations')->insert([
      'title' => trans($title, [], $locale),
      'description' => trans($description, [], $locale),
      'payment_method_id' => $paymentMethod->id,
      'locale' => $locale,
    ]);
  }

}