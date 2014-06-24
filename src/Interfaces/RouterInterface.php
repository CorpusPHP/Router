<?php

namespace Corpus\Router\Interfaces;

interface RouterInterface {

	function __construct( $root_namespace );

	/**
	 * @param string $path
	 * @return array
	 */
	public function match( $path );

}
