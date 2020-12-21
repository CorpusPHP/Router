<?php

namespace Corpus\Router;

class CliRouter extends AbstractRouter {

	const ARGUMENTS = 'arguments';

	protected $arguments;

	public function __construct( $rootNamespace, array $arguments = [] ) {
		$this->arguments = $arguments;

		parent::__construct($rootNamespace);
	}

	public function match( string $path ) : ?array {
		if( substr($path, -1) == '/' ) {
			$path .= 'index';
		}

		$path = $this->trimSlashes($path);

		if( preg_match(
			'%^
				# official namespace/class_name regex
				(?P<namespace>(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/)*)
				(?P<class_name>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)
				(?::(?P<action>[a-zA-Z]+))?
			$%sx',
			$path, $regs)
		) {
			$parts = explode('/', $regs['namespace'] . $regs['class_name']);
			array_unshift($parts, $this->namespace);
			$className = '\\' . implode('\\', $parts);

			$return = [
				self::CONTROLLER => $className,
				self::ARGUMENTS  => $this->arguments,
				self::ACTION     => null,
			];

			if( !empty($regs['action']) && ctype_alpha($regs['action']) ) {
				$return['action'] = $regs['action'];
			}

			return $return;
		}

		return null;
	}

}
