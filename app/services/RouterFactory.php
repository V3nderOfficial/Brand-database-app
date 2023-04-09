<?php

namespace App\Services;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class RouterFactory
{

    public static function createRouter(): RouteList
    {
        $router = new RouteList;
        $router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
        $router[] = new Route('<presenter>/<action>[/<id>]', 'AddNewBrand:default');
        return $router;
    }

}
