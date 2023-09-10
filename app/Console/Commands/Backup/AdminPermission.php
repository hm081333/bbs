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
    protected $description = '备份后台权限表';

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
            ->orderBy('id')
            ->with(['children'])
            ->get();
        $permissions = $this->parse($permissions->toArray());
        file_put_contents(Tools::backupPath('admin_permission.json'), Tools::jsonEncode($permissions));
        // 指令输出
        $this->info('备份后台权限表完成！');
        return 0;
    }

    private function parse($permissions)
    {
        $return_data = [];
        foreach ($permissions as $permission) {
            unset($permission['id'], $permission['pid'], $permission['sort']);
            if (empty($permission['children'])) {
                unset($permission['children']);
            } else {
                $permission['children'] = $this->parse($permission['children']);
            }
            $return_data[] = $permission;
        }
        return $return_data;
    }

}
