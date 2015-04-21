<?php

namespace Corpus\Router;

use Corpus\Router\Interfaces\ReversibleRouterInterface;
use Corpus\Router\Interfaces\RouterInterface;

class MultiReversibleRouter extends MultiRouter {

	/**
	 * Add a router to the queue
	 *
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
	 * @return string|false
	 */
	public function generate( $controller, $action = null, array $options = array() ) {
		/**
		 * @var $router ReversibleRouterInterface
		 */
		foreach( $this->routers as $router ) {
			$link = $router->generate($controller, $action, $options);

			if( $link !== false ) {
				return $link;
			}
		}

		return false;
	}
}
