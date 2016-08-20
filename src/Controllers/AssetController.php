<?php namespace DavinBao\PhpGit\Controllers;

use Illuminate\Http\Response;

/**
 * Class AssetController
 * @package DavinBao\PhpGit\Controllers
 *
 * @author davin.bao
 * @since 2016.8.18
 */
class AssetController extends BaseController
{
    /**
     * Return the javascript for the PhpGit
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function js($name){

        $content = $this->dumpAssetsToString('js', $name);

        $response = new Response(
            $content, 200, array(
                'Content-Type' => 'text/javascript',
            )
        );

        return $this->cacheResponse($response);
    }

    /**
     * Return the stylesheets for the PhpGit
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function css($name) {

        $content = $this->dumpAssetsToString('css', $name);

        $response = new Response(
            $content, 200, array(
                'Content-Type' => 'text/css',
            )
        );

        return $this->cacheResponse($response);
    }

    /**
     * Cache the response 1 year (31536000 sec)
     */
    protected function cacheResponse(Response $response) {
        $response->setSharedMaxAge(31536000);
        $response->setMaxAge(31536000);
        $response->setExpires(new \DateTime('+1 year'));

        return $response;
    }

    public function dumpAssetsToString($type, $name) {
        $file = $this->getAsset($type, $name);

        return file_get_contents($file);
    }

    public function getAsset($type, $name){
        return __DIR__ . '/../Resources/'. $type . '/' .$name . '.' . $type;
    }
}
