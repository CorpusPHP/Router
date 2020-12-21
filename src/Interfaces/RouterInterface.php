<?php

namespace Corpus\Router\Interfaces;

interface RouterInterface {

	public const ACTION     = 'action';
	public const CONTROLLER = 'controller';
	public const OPTIONS    = 'options';

	/**
	 * Match given path to a route array.
	 *
	 * A non-null route is not guaranteed to _exist_ - just to be well formed.
	 * It is up the implementations dispatch mechanism to decide it the route exists
	 *
	 * The returned route array the the a shape of
	 *
	 * ```php
	 * [
	 *     // The controller action. Definition varies by router.
	 *     RouterInterface:ACTION     => 'action',
	 *
	 *     // An expected class name based on given rules. Not guaranteed to exist.
	 *     RouterInterface:CONTROLLER => '\Controller\www\index',
	 *
	 *     // Router specific but akin to $_GET - may contain additional options
	 *     RouterInterface:OPTIONS    => ['key' => 'value'],
	 * ]
	 * ```
	 *
	 * @param string $path The path to match against including query string ala `foo/bar.html?param=woo`
	 * @return array|null route array or null on failure to route
	 */
	public function match( string $path ) : ?array;

}
