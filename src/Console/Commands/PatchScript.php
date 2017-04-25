<?php

namespace DavinBao\PhpGit\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

/**
 * 运行脚本
 *
 * @package App\Console\Commands
 *
 * @author cunqinghuang
 * @since 2017/4/25 14:34
 */
class PatchScript extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'patch:script';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Patching the script.';

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
            ['uninstall', null, InputOption::VALUE_REQUIRED, 'install or uninstall for patch.', null],
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
        $self->info("Patching script... \n");
        $option = $this->option('uninstall');
        $path = app('config')->get('phpgit.path');

        //获取当前分支名称
        $branch = @file_get_contents(base_path() . '/.git/HEAD');
        if (!empty($branch)) {
            $branch = trim($branch);
            $i      = strripos($branch, '/');
            $branch = strtolower(substr($branch, $i + 1));
        }else{
            return $self->error("Expect parameter '--branch'\n");
        }

        if ($option ===  'true') {
            try {
                set_time_limit(0);
                $pathFile = strtolower(dirname(app_path()).$path.$branch.".php");
                if (file_exists($pathFile)){
                    require_once $pathFile;
                    $script = new \Script();
                    $script->uninstall($branch);
                }
            } catch (\Exception $e) {
                return $self->error($e->getMessage(). "\n" . $e->getTraceAsString() . "\n");
            }
        }

        if($option === 'false'){
            try {
                set_time_limit(0);
                $pathFile = strtolower(dirname(app_path())."/database/patchs/$branch.php");
                if (file_exists($pathFile)){
                    require_once $pathFile;
                    $script = new \Script();
                    $script->install($branch);
                }
            } catch (\Exception $e) {
                return $self->error($e->getMessage(). "\n" . $e->getTraceAsString() . "\n");
            }
        }
        return $self->info("Patching database Success\n");
    }
}