<?php

namespace Corpus\Router\Interfaces;

interface ReversibleRouterInterface extends RouterInterface {

	/**
	 * @param object|string $controller Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
	 * @param string|null   $action
	 * @throws \Corpus\Router\Exceptions\RouteGenerationFailedException
	 */
	public function generate( $controller, $action = null, array $options = [] ) : string;

}
