<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class getBillingAgreement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate all the cancelled subscription and its membership';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $getAllMembershipsRecords = DB::table(MEMBERSHIP)->select('id', 'agreementId')->get();
        if(!empty($getAllMembershipsRecords)){
            $agreement = array();
            foreach ($getAllMembershipsRecords as $getAllMembershipsRecord) {
                $createdAgreementId = $getAllMembershipsRecord->agreementId;
                $membershipId = $getAllMembershipsRecord->id;
                try {
                    // get billing agreement
                    $getAgreement = Agreement::get($createdAgreementId, $this->_api_context);
                    $agreementState = $getAgreement->state;

                    if($agreementState == 'Cancelled'){
                        DB::table(MEMBERSHIP)->where('agreementId', $createdAgreementId)->update(['membershipStatus' => 'Inactive']);

                        DB::table(SEAT_ALLOCATION)->where('memberShipId', $membershipId)->update(['reserved' =>0, 'deleted' => 0]);
                    }
                } catch (Exception $ex) {
                    // p("Retrieved an Agreement", "Agreement", $agreement->getId(), $createdAgreementId, $ex);
                    $this->info('Error while retrive an agreement');
                    exit(1);
                }
            }
            $this->info('Memberhsip deactivated successfully');
        } else {
            // return redirect('/')->with('message', 'Record not exist. Cron Record!!');
            $this->info('Memberhsip record not found.');
        }
    }
}
