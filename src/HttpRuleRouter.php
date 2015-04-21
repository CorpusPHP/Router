<?php

namespace Corpus\Router;

use Corpus\Router\Exceptions\InvalidRuleException;
use Corpus\Router\Exceptions\NonRoutableException;
use Corpus\Router\Interfaces\ReversibleRouterInterface;

class HttpRuleRouter extends AbstractRouter implements ReversibleRouterInterface {

	const ERROR_UNKNOWN_TYPE   = 10;
	const ERROR_DUPLICATED_KEY = 20;

	protected $server;

	/**
	 * @param string $root_namespace
	 * @param array  $server The $_SERVER array - optional
	 */
	public function __construct( $root_namespace, $server = array() ) {
		$this->server = $server;
		parent::__construct($root_namespace);
	}

	/**
	 * @var array[]
	 */
	protected $rules = array();

	public function addRule( $rule, $route ) {
		$route = $this->classNameC14N($route);

		$match = '/\{(?P<name>[a-z0-9_]+)(\|(?P<set>[a-z]))?\}/ix';
		$parts = preg_split($match, $rule);
		preg_match_all($match, $rule, $matches);

		$keys   = array();
		$tokens = array();
		foreach( $parts as $i => $part ) {
			$tokens [] = $part;

			if( isset($matches[0][$i]) ) {
				$name = $matches['name'][$i];
				$set  = $matches['set'][$i];

				switch( $set ) {
					case 'a':
					case 'd':
					case 's':
					case 'w':
						$tokens[] = array( $name, $set );
						break;
					case '':
						$tokens[] = array( $name, 's' );
						break;
					default:
						throw new InvalidRuleException('Unknown type ' . $matches['set'][$i], self::ERROR_UNKNOWN_TYPE);
				}

				if( !in_array($name, $keys) ) {
					$keys[] = $name;
				} else {
					throw new InvalidRuleException('Keys may not be duplicated', self::ERROR_DUPLICATED_KEY);
				}
			}
		}

		$this->rules[] = [
			'tokens' => $tokens,
			'route'  => $route,
			'keys'   => $keys,
		];
	}

	protected function tokensToRegex( array $tokens ) {
		$out   = '%^';
		$trans = [
			'a' => '[a-zA-Z]+?',
			'd' => '\d+',
			's' => '\S+?',
			'w' => '\w+?',
		];

		foreach( $tokens as $token ) {
			if( is_array($token) && count($token) == 2 ) {
				$name  = preg_quote($token[0], '%');
				$match = $trans[$token[1]];
				$out .= "(?P<{$name}>{$match})";
			} elseif( is_string($token) ) {
				$out .= preg_quote($token, '%');
			} else {
				throw new \RuntimeException('Invalid Token');
			}
		}
		$out .= '$%';

		return $out;
	}

	/**
	 * @param string $path
	 * @return array|false
	 */
	public function match( $path ) {
		$parts = parse_url($path);

		$path = empty($parts['path']) ? '' : $parts['path'];
		$args = empty($parts['query']) ? array() : $this->parseStr($parts['query']);

		$path = $this->trimSlashes($path);

		foreach( $this->rules as $rule ) {
			$pattern = $this->tokensToRegex($rule['tokens']);

			if( preg_match($pattern, $path, $matches) ) {
				$xargs = $args;
				foreach( $rule['keys'] as $key ) {
					$xargs[$key] = urldecode($matches[$key]);
				}

				$return = [
					'controller' => $rule['route'],
					'action'     => null,
					'options'    => $xargs,
				];

				if( !empty($xargs['_action']) ) {
					$return['action'] = $xargs['_action'];
				}

				if( !empty($this->server['REQUEST_METHOD']) ) {
					$return['request']['method'] = strtoupper($this->server['REQUEST_METHOD']);
				}

				return $return;
			}
		}

		return false;
	}

	/**
	 * @param string|object $controller Instance or Relative 'admin\index' or absolute '\Controllers\www\admin\index'
	 * @param string|null   $action
	 * @param array         $options
	 * @return string|false
	 * @throws Exceptions\NonRoutableException
	 */
	public function generate( $controller, $action = null, array $options = array() ) {
		$class_name = $this->classNameC14N($controller);

		if( !$class_name ) {
			throw new NonRoutableException("Controller '{$controller}' should be a valid controller class/classname and of namespace {$this->namespace}");
		}

		if( !empty($action) ) {
			$options['_action'] = $action;
		}
		foreach( $this->rules as $rule ) {
			$xoptions = $options;

			if( $rule['route'] == $class_name ) {
				$path = '';
				foreach( $rule['tokens'] as $token ) {
					if( is_array($token) && count($token) == 2 ) {
						$name = $token[0];

						if( isset($xoptions[$name]) ) {
							if( !is_scalar($xoptions[$name]) || is_null($xoptions[$name]) ) {
								//Inline options must be scalar
								break 2;
							}
							$val = (string)$xoptions[$name];
							if( !$this->validateTokenValue($token, $val) ) {
								continue 2;
							}

							$path .= urlencode($val);
							unset($xoptions[$name]);
						} else {
							continue 2;
						}
					} elseif( is_string($token) ) {
						$path .= $token;
					}
				}

				$options = array_filter($xoptions);
				if( count($options) > 0 ) {
					$path .= '?' . http_build_query($xoptions);
				}

				return '/' . ltrim($path, '/');
			}
		}

		return false;
	}

	/**
	 * Validate that a potential value matches against a token
	 *
	 * @param array  $token
	 * @param string $value
	 * @return bool
	 */
	protected function validateTokenValue( array $token, $value ) {
		switch( $token[1] ) {
			case 'a':
				return ctype_alpha($value);
			case 'd':
				return ctype_digit($value);
			case 's':
				return ctype_graph($value);
			case 'w':
				$value = str_replace('_', '', $value);

				return ctype_alnum($value);
		}

		throw new \RuntimeException('Invalid Token'); //should never be reached
	}
}
