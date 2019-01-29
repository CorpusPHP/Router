<?php

namespace Corpus\Router\Interfaces;

use Corpus\Router\Exceptions\RouteGenerationFailedException;

interface ReversibleRouterInterface extends RouterInterface {

	/**
	 * @param object|string $controller Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
	 * @param string|null   $action
	 * @param array         $options
	 * @throws RouteGenerationFailedException
	 * @return string
	 */
	public function generate( $controller, $action = null, array $options = [] );
}
