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
	public function addRouter( RouterInterface $router ) {
		if( $router instanceof ReversibleRouterInterface ) {
			return parent::addRouter($router);
		}

		throw new \InvalidArgumentException('Expected ReversibleRouterInterface');
	}

	/**
	 * Loops over routers in the order they were added until a generated URL is found.
	 *
	 * @param string|object $controller Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
	 * @param string|null   $action
	 * @param array         $options
	 * @return string
	 * @throws \Corpus\Router\Exceptions\RouteGenerationFailedException
	 */
	public function generate( $controller, $action = null, array $options = array() ) {
		/**
		 * @var $router ReversibleRouterInterface
		 */
		foreach( $this->routers as $router ) {
			try {
				return $router->generate($controller, $action, $options);
			} catch(RouteGenerationFailedException $ex) {
				continue;
			}
		}

		throw new RouteGenerationFailedException('none of the routers available were able to generate a link');
	}
}
