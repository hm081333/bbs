<?php

namespace App\Console\Commands\Backup;

use App\Utils\Tools;
use Illuminate\Console\Command;

class AdministrativeDivisionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:administrative-division';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '备份行政区划表';

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
     * @return int
     */
    public function handle()
    {
        $modelArea = new \App\Models\System\AdministrativeDivision();
        $areas = $modelArea
            ->select([
                'id',
                'name',
                'attr',
                'code',
                'initial',
                'pid',
                'level',
                'sort',
                'lat',
                'lng',
            ])
            ->get()
            ->toArray();
        $areas = array_merge(Tools::translateDataToTree($areas));
        $areas = $this->parse($areas);
        file_put_contents(Tools::backupPath('administrative_division.json'), Tools::jsonEncode($areas));
        // 指令输出
        $this->info($this->description . ' 完成！');
        return 0;
    }

    private function parse($areas)
    {
        $return_data = [];
        foreach ($areas as $area) {
            unset($area['id'], $area['pid']);
            if (empty($area['children'])) {
                unset($area['children']);
            } else {
                $area['children'] = $this->parse($area['children']);
            }
            $return_data[] = $area;
        }
        return $return_data;
    }

}
