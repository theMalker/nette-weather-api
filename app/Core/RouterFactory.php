<?php

declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

        // API endpointy
        $router->addRoute('api/current/<location>', 'Api:current');
        $router->addRoute('api/forecast/<location>[/<days>]', 'Api:forecast');

		$router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');

		return $router;
	}
}
