<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Flow;
use Illuminate\Support\Facades\DB;

class MergeFlow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:flow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge flows into 5 minutes';

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

        $start = mktime(18, 30, 0, 11, 02, 2016);
        // $end = mktime(19, 30, 0, 11, 02, 2016);
        $end = time();
        while ($start < $end) {
            $startTime = date('Y-m-d H:i:s', $start);
            $endTime = date('Y-m-d H:i:s', $start+60*5);
            echo $startTime."\n";
            $ports = DB::select("select distinct(port) from flows where date_time >= '$startTime' and date_time <= '$endTime'");
            echo implode(",", array_map(function ($portObj){return $portObj->port;}, $ports))."\n";
            $updateSql = "UPDATE flows SET flow = CASE id "; 
            $updateIDs = [];
            $deleteIDs = [];
            foreach ($ports as $port) {
                $flows = $this->getFlowsFromStartToEnd($port->port, $startTime, $endTime);
                if ($flows) {
                    $total = 0;
                    foreach ($flows as $flow) {
                        $total += $flow->flow;
                        if ($flow == $flows[0]) {
                            array_push($updateIDs, $flow->id);
                        }else{
                            array_push($deleteIDs, $flow->id);
                        }
                    }
                    $updateSql .= sprintf("WHEN %d THEN %d ", $flows[0]->id, $total);
                }
            }
            if ($updateIDs) {
                $updateIDs = implode(',', $updateIDs);
                $updateSql.="END WHERE id IN ($updateIDs)";
                // echo 'updateSql:'.$updateSql;
                DB::update($updateSql);   
            }
            if ($deleteIDs) {
                $deleteIDs = implode(',', $deleteIDs);
                $deleteSql = "delete from flows where id in ($deleteIDs)";
                // echo 'deleteSql:'.$deleteSql;
                DB::delete($deleteSql);
            }

            $start = $start + 60*5;
        }
        
    }

    public function getFlowsFromStartToEnd($port, $start, $end)
    {
        // $flows = Flow::where([
        //         ['port', '=', $port],
        //         ['date_time', '>=', $start],
        //         ['date_time', '<=', $end]
        //     ])->get();
        $flows = DB::table('flows')->where([
                ['port', '=', $port],
                ['date_time', '>=', $start],
                ['date_time', '<=', $end]
            ])->get();
        return $flows;
    }


}
