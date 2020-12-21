<?php

namespace Corpus\Router;

use Corpus\Router\Exceptions\RouteGenerationFailedException;
use Corpus\Router\Interfaces\ReversibleRouterInterface;
use Corpus\Router\Interfaces\RouterInterface;

class MultiReversibleRouter extends MultiRouter implements ReversibleRouterInterface {

	/**
	 * Add a router to the queue
	 *
	 * @inheritdoc
	 * @param \Corpus\Router\Interfaces\ReversibleRouterInterface $router
	 */
	public function addRouter( RouterInterface $router ) : void {
		if( $router instanceof ReversibleRouterInterface ) {
			parent::addRouter($router);

			return;
		}

		throw new \InvalidArgumentException('Expected ReversibleRouterInterface');
	}

	/**
	 * Loops over routers in the order they were added until a generated URL is found.
	 *
	 * @param object|string $controller Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
	 * @throws \Corpus\Router\Exceptions\RouteGenerationFailedException
	 */
	public function generate( $controller, ?string $action = null, array $options = [] ) : string {
		/**
		 * @var ReversibleRouterInterface $router
		 */
		foreach( $this->routers as $router ) {
			try {
				return $router->generate($controller, $action, $options);
			} catch( RouteGenerationFailedException $ex ) {
				continue;
			}
		}

		throw new RouteGenerationFailedException('none of the routers available were able to generate a link');
	}

}
