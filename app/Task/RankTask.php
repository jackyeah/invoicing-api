<?php
namespace App\Task;

use App\Http\Helper\HttpClientHelper;
use Illuminate\Console\Command;

class RankTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RankTask {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * OverMasterTask constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 進入點
     */
    public function handle()
    {
        switch ($this->argument('task_type')) {
            case 'getRank':
                self::getRank();
                break;
            default :
                break;
        }
    }

    protected static function getRank()
    {
        //取得屆數範圍
        $periodRange = range(1, 1);
        $client = new HttpClientHelper();
        $params = [
            //   'period' => 1,
            'ownercode' => 'Car',
            'limit' => 0,//填入0（或不填入）時會使用預設名次值 50
        ];
        //取得 Rank ,存入cache
        //主架構,cache尚未實做
        foreach ($periodRange as $value) {
            $period = $value;
            $params['period'] = $period;
            $data = $client->sendPost('rank/getrank', $params);
        }

        return $data;

    }


}