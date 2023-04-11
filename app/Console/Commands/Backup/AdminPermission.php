<?php

namespace App\Console\Commands\Backup;

use App\Utils\Tools;
use Illuminate\Console\Command;

class AdminPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:admin-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '备份后台权限';

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
        $modelAdminPermission = new \App\Models\Admin\AdminPermission;
        $permissions = $modelAdminPermission
            ->where('pid', $params['pid'] ?? 0)
            ->orderBy('sort')
            ->orderByDesc('id')
            ->with(['children'])
            ->get();
        $permissions = $this->parsePermissions($permissions->toArray());
        file_put_contents(Tools::backupPath('admin_permission.json'), json_encode($permissions));
        // 指令输出
        $this->info('备份后台权限完成！');
        return 0;
    }

    private function parsePermissions($permissions)
    {
        $return_data = [];
        foreach ($permissions as $permission) {
            unset($permission['id'], $permission['pid']);
            if (empty($permission['children'])) {
                unset($permission['children']);
            } else {
                $permission['children'] = $this->parsePermissions($permission['children']);
            }
            $return_data[] = $permission;
        }
        return $return_data;
    }

}
