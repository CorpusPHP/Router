<?php

namespace Corpus\Router\Interfaces;

interface ReversibleRouterInterface extends RouterInterface {

	/**
	 * @param string|object $controller Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
	 * @param string|null $action
	 * @param array|null $options
	 * @return string
	 */
	public function generate( $controller, $action = null, array $options = null );

}