<?php

namespace Corpus\Test\Router;

use Corpus\Router\CliRouter;

class CliRouterTest extends \PHPUnit_Framework_TestCase {

	protected $namespaces = array( '\\Foo', '\\Foo\\Bar', '\\Foo\\Bar\\ClassName', '\\Fun\\With_Underscores', '\\日本の\\しい' );

	public function testMatch() {

		foreach( $this->namespaces as $ns ) {
			$router = new CliRouter($ns);

			$this->assertSame(false, $router->match(''));

			$this->assertEquals(array(
					'controller' => $ns . '\\index',
					'action'     => null,
					'arguments'  => [],
				),
				$router->match('/')
			);

			$this->assertEquals(false, $router->match('/:myAction'));

			$this->assertEquals(array(
					'controller' => $ns . '\\help',
					'action'     => null,
					'arguments'  => [],
				),
				$router->match('help')
			);

			$this->assertEquals(array(
					'controller' => $ns . '\\help',
					'action'     => 'otherAction',
					'arguments'  => [],
				),
				$router->match('help:otherAction')
			);

			$this->assertEquals(array(
					'controller' => $ns . '\\help\\i\\am\\stuck',
					'action'     => null,
					'arguments'  => [],
				),
				$router->match('help/i/am/stuck')
			);

			$this->assertEquals(array(
					'controller' => $ns . '\\help\\i\\am\\stuck',
					'action'     => 'funkyfuntimes',
					'arguments'  => [],
				),
				$router->match('help/i/am/stuck:funkyfuntimes')
			);

			$this->assertEquals(false, $router->match('/Baz/Qux.json'));

			$this->assertEquals(false, $router->match('/Baz/Qux.json:What'));

			$this->assertSame(false, $router->match('/Baz/Qux.json:10')); //So we don't confuse the colon syntax with ports
		}

	}

}
 