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
            ['install', 'i', InputOption::VALUE_NONE, 'install for patch.'],
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

        if($unOption){
            try {
                set_time_limit(0);
                $sqlFile = strtolower(dirname(app_path()).$sqlPath.$branch."-uninstall.sql");
                if(file_exists($sqlFile)){
                    $self->info("Patching database file: $sqlFile\n");
                    DB::unprepared(file_get_contents($sqlFile));
                }
            } catch (\Exception $e) {
                return $self->error($e->getMessage(). "\n" . $e->getTraceAsString() . "\n");
            }
        }

        if($inOption){
            try {
                set_time_limit(0);
                $sqlFile = strtolower(dirname(app_path()).$sqlPath.$branch."-install.sql");
                if(file_exists($sqlFile)){
                    $self->info("Patching database file: $sqlFile\n");
                    DB::unprepared(file_get_contents($sqlFile));
                }
            } catch (\Exception $e) {
                return $self->error($e->getMessage(). "\n" . $e->getTraceAsString() . "\n");
            }
        }

        return $self->info("Patching database Success\n");
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
}