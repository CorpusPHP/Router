<?php

namespace Corpus\Router\Interfaces;

interface RouterInterface {

	const ACTION     = 'action';
	const CONTROLLER = 'controller';
	const OPTIONS    = 'options';

	/**
	 * @param string $path
	 * @return array|false
	 */
	public function match( $path );
}
