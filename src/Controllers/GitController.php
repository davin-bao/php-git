<?php namespace DavinBao\PhpGit\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use DavinBao\PhpGit\Git;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

/**
 * Class GitController
 * @package DavinBao\PhpGit\Controllers
 *
 * @author davin.bao
 * @since 2016.8.18
 */
class GitController extends BaseController
{
    public $repo;

    public function __construct() {

        Git::$bin = app('config')->get('phpgit.git_path');
        parent::__construct();
    }
   
    public function index(Request $request)
    {
        return view('php_git::index');
    }

    public function getRepoList(Request $request){
        $repoList = app('config')->get('phpgit.repo_list');
        $currentRepo = $request->get('repo', current($repoList));
        try{
            $this->getRepo($request)->fetch();
        }catch (\Exception $e){
            $currentRepo = current($repoList);
        }

        return new JsonResponse(array_merge(['rows'=>$repoList, 'current'=> $currentRepo], ['msg'=>'', 'code'=>200]), 200, $headers = [], 0);
    }

    public function getBranches(Request $request){
        $branchList = $this->getRepo($request)->list_branches(true);
        $status = $this->getRepo($request)->status(true);

        return new JsonResponse(array_merge(['rows'=>$branchList, 'status' => $status], ['msg'=>'', 'code'=>200]), 200, $headers = [], 0);
    }

    public function getRemoteBranches(Request $request){
        $branchList = $this->getRepo($request)->list_remote_branches();
        $status = $this->getRepo($request)->status(true);

        return new JsonResponse(array_merge(['rows'=>$branchList, 'status' => $status], ['msg'=>'', 'code'=>200]), 200, $headers = [], 0);
    }

    public function postCheckout(Request $request){
        $branch = $request->get('branch', 'master');
        $repo = $this->getRepo($request);

        $repo->clean(true, true);
        $repo->checkout($branch);
        $result = $repo->pull('origin', $branch);

        $commands = app('config')->get('phpgit.command');
        foreach($commands as $command){
            $process = new Process($command);
            $workingDirectory = $process->getWorkingDirectory();
            $process->setWorkingDirectory(str_replace('/public', '', $workingDirectory));

            $commandOutput = '';
            $process->run(function ($type, $buffer) use(&$commandOutput) {
                $commandOutput = $commandOutput . nl2br($buffer);
            });
            if(!$process->isSuccessful() || strpos($commandOutput, 'Rebuild database Success') === false){
                return new JsonResponse(['msg'=>$commandOutput, 'code'=>500], 500, $headers = [], 0);
            }
        }

        return new JsonResponse(['msg'=>$result, 'code'=>200], 200, $headers = [], 0);
    }

    public function postRemoteCheckout(Request $request){
        $branch = $request->get('branch', 'origin/master');
        $repo = $this->getRepo($request);

        $repo->clean(true, true);
        $result = $repo->remote_checkout($branch);

        $commands = app('config')->get('phpgit.command');
        foreach($commands as $command){
            $process = new Process($command);
            $process->run();
        }

        return new JsonResponse(['msg'=>$result, 'code'=>200], 200, $headers = [], 0);
    }

    public function postDelete(Request $request){
        $branch = $request->get('branch', '');
        $result = $this->getRepo($request)->delete_branch($branch);

        return new JsonResponse(['msg'=>$result, 'code'=>200], 200, $headers = [], 0);
    }

    private function getRepo(Request $request){
        if(is_null($this->repo)){
            $repoList = app('config')->get('phpgit.repo_list');
            $currentRepo = $request->get('repo', current($repoList));
            $this->repo = Git::open($currentRepo);
        }
        return $this->repo;
    }
}
