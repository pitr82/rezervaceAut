<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router = new RouteList();
		
		$router[] = new Route('help/[<filename>]', 'Help:default');
		$router[] = new Route('user/<action>[/<id [0-9]+>]', 'user:default');
		$router[] = new Route('unit/<action>[/<id [0-9]+>]', 'unit:default');
		$router[] = new Route('auto/<action>[/<id [0-9]+>]', 'auto:default');
		$router[] = new Route('<presenter>/<action>[/<datum>[/<id [0-9]+>]]', 'Homepage:default');
//		$router[] = new Route("[<page>]", array(
//			'presenter' => 'user',
//			'action' => 'list',
//			'page' => 5
//		));
		
		$router[] = $adminRouter = new RouteList('Admin');
		$adminRouter[] = new Route('admin/<presenter>/<action>[/<id>]', 'Homepage:default');
		
		$router[] = $frontRouter = new RouteList('Front');
		$frontRouter[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
				
		return $router;
	}

}
