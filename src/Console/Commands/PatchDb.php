<?php

namespace DavinBao\PhpGit\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

/**
 * 运行数据库补丁
 *
 * @package App\Console\Commands
 *
 * @author cunqinghuang
 * @since 2017/4/25 14:34
 */
class PatchDb extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'patch:db';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Patching the database.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getOptions()
    {
        return [
            ['uninstall', 'u', InputOption::VALUE_NONE, 'uninstall for patch.'],
            ['install', 'i', InputOption::VALUE_NONE, 'install for patch.']
        ];
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $self = $this;
        $self->info("Patching database... \n");
        $unOption = $this->option('uninstall');
        $inOption = $this->option('install');

        $sqlPath = app('config')->get('phpgit.path');
        $branch = $self->getBranch($self);

        $uninstallSqlFile = strtolower(dirname(app_path()).$sqlPath.$branch."-uninstall.sql");
        $installSqlFile = strtolower(dirname(app_path()).$sqlPath.$branch."-install.sql");
        $productionUninstallSqlFile = strtolower(dirname(app_path()).$sqlPath."production-uninstall.sql");
        $productionInstallSqlFile = strtolower(dirname(app_path()).$sqlPath."production-install.sql");

        $production =(env('APP_ENV') === 'production');
        if($production){
            $self->executeSql($unOption,$inOption,$productionUninstallSqlFile,$productionInstallSqlFile);
        }else{
            $self->executeSql($unOption,$inOption,$uninstallSqlFile,$installSqlFile);
        }

        return $self->info("Patching Database Success\n");
    }

    /**
     * 获取当前分支名称
     *
     * @return mixed
     */
    public function getBranch($self){
        $branch = @file_get_contents(base_path() . '/.git/HEAD');

        if (!empty($branch)) {
            $branch = trim($branch);
            $i      = strripos($branch, '/');
            $branch = strtolower(substr($branch, $i + 1));
            return $branch;
        }else{
            return $self->error("Expect parameter '--branch'\n");
        }
    }

    /**
     * 根据SQL语句进行安装和卸载
     * @param string $unOption 卸载指令
     * @param string $inOption 安装指令
     * @param string $uninstallSqlFile 卸载内容
     * @param string $installSqlFile 安装内容
     *
     * @return mixed
     */
    public function executeSql($unOption,$inOption,$uninstallSqlFile,$installSqlFile){
        $self = $this;
        if($unOption){
            try {
                set_time_limit(0);
                if(file_exists($uninstallSqlFile)){
                    $self->info("Patching database file: $uninstallSqlFile\n");
                    DB::unprepared(file_get_contents($uninstallSqlFile));
                }else{
                    return $self->info("No Configuration\n");
                }
            } catch (\Exception $e) {
                return $self->error($e->getMessage(). "\n" . $e->getTraceAsString() . "\n");
            }
        }

        if($inOption){
            try {
                set_time_limit(0);
                if(file_exists($installSqlFile)){
                    $self->info("Patching database file: $installSqlFile\n");
                    DB::unprepared(file_get_contents($installSqlFile));
                }else{
                    return $self->info("No Configuration\n");
                }
            } catch (\Exception $e) {
                return $self->error($e->getMessage(). "\n" . $e->getTraceAsString() . "\n");
            }
        }
    }

}