<?php

namespace DavinBao\PhpGit;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class PhpGitServiceProvider extends ServiceProvider
{

    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $app = $this->app;

        $configPath = __DIR__ . '/../config/phpgit.php';
        $this->publishes([$configPath => config_path('phpgit.php')], 'config');

        // If enabled is null, set from the app.debug value
        $enabled = $this->app['config']->get('phpgit.enabled');

        if (is_null($enabled)) {
            $enabled = $this->checkAppDebug();
        }

        if (! $enabled) {
            return;
        }

        $routeConfig = [
            'namespace' => 'DavinBao\PhpGit\Controllers',
            'prefix' => $app['config']->get('phpgit.route_prefix'),
            'module'=>'',
        ];

        $this->getRouter()->group($routeConfig, function($router) {
            $router->get('git', [
                'uses' => 'GitController@index',
                'as' => 'git.index',
            ]);
            $router->get('git/repo-list', [
                'uses' => 'GitController@getRepoList',
                'as' => 'git.repo-list',
            ]);
            $router->get('git/branches', [
                'uses' => 'GitController@getBranches',
                'as' => 'git.branches',
            ]);
            $router->get('git/remote-branches', [
                'uses' => 'GitController@getRemoteBranches',
                'as' => 'git.remote-branches',
            ]);
            $router->post('git/checkout', [
                'uses' => 'GitController@postCheckout',
                'as' => 'git.checkout',
            ]);
            $router->post('git/remote-checkout', [
                'uses' => 'GitController@postRemoteCheckout',
                'as' => 'git.remote-checkout',
            ]);
            $router->post('git/delete', [
                'uses' => 'GitController@postDelete',
                'as' => 'git.delete',
            ]);

            $router->get('assets/css/{name}', [
                'uses' => 'AssetController@css',
                'as' => 'git.assets.css',
            ]);

            $router->get('assets/javascript/{name}', [
                'uses' => 'AssetController@js',
                'as' => 'git.assets.js',
            ]);
        });

        $this->loadViewsFrom(__DIR__.'/views', 'php_git');

        $this->registerMiddleware('php_git_catch_exception', 'DavinBao\PhpGit\Middleware\CatchException');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/phpgit.php';
        $this->mergeConfigFrom($configPath, 'phpgit');

    }

    protected function registerMiddleware($key, $middleware)
    {
        $this->app['router']->middleware($key, $middleware);
    }

    /**
     * Get the active router.
     *
     * @return Router
     */
    protected function getRouter()
    {
        return $this->app['router'];
    }

    /**
     * Check the App Debug status
     */
    protected function checkAppDebug()
    {
        return $this->app['config']->get('app.debug');
    }
}
