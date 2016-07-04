<?php

use Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		
        $router[] = $adminRouter = new RouteList('Admin');

    	$adminRouter[] = new Route('admin/<presenter>/<action>[/<id>]', 'Homepage:default');

    	$router[] = $frontRouter = new RouteList('Public');
        
        $frontRouter[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
        
		return $router;
	}

}
