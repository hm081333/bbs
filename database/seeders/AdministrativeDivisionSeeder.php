<?php

namespace Database\Seeders;

use App\Models\System\AdministrativeDivision;
use App\Utils\Tools;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdministrativeDivisionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        config(['app.no_sql_log' => true]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');//禁用外键约束
        AdministrativeDivision::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');//启用外键约束
        $areas = file_get_contents(Tools::backupPath('administrative_region.json'));
        $areas = Tools::jsonDecode($areas);
        foreach ($areas as $area) {

            // if (empty($area['lat'])) $area['lat'] = null;
            // if (empty($area['lng'])) $area['lng'] = null;
            (new AdministrativeDivision())->saveData($area);
        }
    }
}
