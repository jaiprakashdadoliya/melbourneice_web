<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class checkMembershipDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deactivate:membership';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate membership if date is equal to 2018-03-01';

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
        $getAllMembershipsRecords = DB::table(MEMBERSHIP)->select('id','membershipStatus')->whereDate('endDate', '=', date('2018-03-02'))->get();
        if(!empty($getAllMembershipsRecords)){
            foreach ($getAllMembershipsRecords as $value) {
                DB::table(MEMBERSHIP)->where('id', $value->id)->update(['membershipStatus' => 'Inactive']);
                DB::table(SEAT_ALLOCATION)->where('memberShipId', $value->id)->update(['reserved' =>0, 'deleted' => 0]);
            }
            $this->info('Memberhsip deactivated successfully');
        }
    }
}
