<?php namespace DavinBao\PhpGit\Controllers;

use Illuminate\Routing\Controller;

if (class_exists('Illuminate\Routing\Controller')) {

    /**
     * Class BaseController
     * @package DavinBao\PhpGit\Controllers
     *
     * @author davin.bao
     * @since 2016.8.18
     */
    class BaseController extends Controller
    {

        public function __construct() {
            $this->middleware('php_git_catch_exception');
        }
    }

} else {

    /**
     * Class BaseController
     * @package DavinBao\PhpGit\Controllers
     *
     * @author davin.bao
     * @since 2016.8.18
     */
    class BaseController
    {

    }
}