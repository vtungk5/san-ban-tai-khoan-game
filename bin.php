<?php
error_reporting(E_ALL);

use Dotenv\Dotenv;
use MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException;
use Laminas\Diactoros\Response\HtmlResponse;

class Bin
{

    public static function load($files = [])
    {
        foreach ($files as $path):
            require $_SERVER['DOCUMENT_ROOT'] . $path;
        endforeach;
        return new static;
    }

    public static function direct($path_s = [], $depth = 0)
    {
        foreach ((array) $path_s as $path):

            $dirhandle = @opendir($path);
            if ($dirhandle === false) {
                return;
            }

            while (($file = readdir($dirhandle)) !== false) {
                if ($file !== '.' && $file !== '..') {
                    $fullfile = $path . '/' . $file;
                    if (is_dir($fullfile)) {
                        static::direct($fullfile, $depth + 1);
                    } else if (strlen($fullfile) > 4 && substr($fullfile, -4) == '.php') {
                    require $fullfile;
                }
            }
        }

        closedir($dirhandle);
        endforeach;
    }

    static public function Router($Router)
    {

        $Router->setupView($_SERVER['DOCUMENT_ROOT'] . '/views');

        try {
            $Router->dispatch();
        } catch (RouteNotFoundException $e) {
            $Router->getPublisher()->publish(include_once __DIR__.'/views/404.phtml'); //Not found
        }
        // catch (Throwable $e) {
        //     $Router->getPublisher()->publish(new HtmlResponse('có lỗi hãy báo admin', 500));
        // }
    }

    public static function run()
    {
        Dotenv::createImmutable(__DIR__)->load();
        return new static;
    }

}
