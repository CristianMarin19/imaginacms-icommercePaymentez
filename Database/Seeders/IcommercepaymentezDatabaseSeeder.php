<?php

namespace Modules\Icommercepaymentez\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Icommerce\Entities\PaymentMethod;
use Modules\Isite\Jobs\ProcessSeeds;

class IcommercepaymentezDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run()
  {

    ProcessSeeds::dispatch([
      "baseClass" => "\Modules\Icommercepaymentez\Database\Seeders",
      "seeds" => ["IcommercepaymentezModuleTableSeeder", "IcommercepaymentezSeeder"]
    ]);

  }

}
