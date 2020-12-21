<?php

namespace Corpus\Router\Interfaces;

interface RouterInterface {

	public const ACTION     = 'action';
	public const CONTROLLER = 'controller';
	public const OPTIONS    = 'options';

	public function match( string $path ) : ?array;

}
