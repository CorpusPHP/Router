<?php

namespace Corpus\Router\Interfaces;

interface RouterInterface {

	/**
	 * @param string $path
	 * @return array|false
	 */
	public function match( $path );
}
