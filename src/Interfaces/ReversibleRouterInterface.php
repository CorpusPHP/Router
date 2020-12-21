<?php

namespace Corpus\Router\Interfaces;

interface ReversibleRouterInterface extends RouterInterface {

	/**
	 * Generate a URL for the given controller, action and options
	 *
	 * @param object|string $controller Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
	 * @throws \Corpus\Router\Exceptions\RouteGenerationFailedException
	 */
	public function generate( $controller, ?string $action = null, array $options = [] ) : string;

}
