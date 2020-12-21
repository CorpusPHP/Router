<?php

namespace Corpus\Test\Router;

use Corpus\Router\CliRouter;

class CliRouterTest extends \PHPUnit\Framework\TestCase {

	protected $namespaces = [ '\\Foo', '\\Foo\\Bar', '\\Foo\\Bar\\ClassName', '\\Fun\\With_Underscores', '\\日本の\\しい' ];

	public function testMatch() : void {

		foreach( $this->namespaces as $ns ) {
			$router = new CliRouter($ns);

			$this->assertNull($router->match(''));

			$this->assertEquals([
					'controller' => $ns . '\\index',
					'action'     => null,
					'arguments'  => [],
				],
				$router->match('/')
			);

			$this->assertNull($router->match('/:myAction'));

			$this->assertEquals([
					'controller' => $ns . '\\help',
					'action'     => null,
					'arguments'  => [],
				],
				$router->match('help')
			);

			$this->assertEquals([
					'controller' => $ns . '\\help',
					'action'     => 'otherAction',
					'arguments'  => [],
				],
				$router->match('help:otherAction')
			);

			$this->assertEquals([
					'controller' => $ns . '\\help\\i\\am\\stuck',
					'action'     => null,
					'arguments'  => [],
				],
				$router->match('help/i/am/stuck')
			);

			$this->assertEquals([
					'controller' => $ns . '\\help\\i\\am\\stuck',
					'action'     => 'funkyfuntimes',
					'arguments'  => [],
				],
				$router->match('help/i/am/stuck:funkyfuntimes')
			);

			$this->assertNull($router->match('/Baz/Qux.json'));
			$this->assertNull($router->match('/Baz/Qux.json:What'));
			$this->assertNull($router->match('/Baz/Qux.json:10')); //So we don't confuse the colon syntax with ports
		}

	}

}
