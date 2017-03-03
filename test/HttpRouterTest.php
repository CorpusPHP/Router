<?php

namespace Corpus\Test\Router;

use Corpus\Router\HttpRouter;

class HttpRouterTest extends \PHPUnit_Framework_TestCase {

	protected $namespaces = array( '\\Foo', '\\Foo\\Bar', '\\Foo\\Bar\\ClassName', '\\Fun\\With_Underscores', '\\日本の\\しい' );

	public function testMatch() {

		$server_arrays = array( array(), array( 'REQUEST_METHOD' => 'post' ), array( 'REQUEST_METHOD' => 'Get' ) );
		$query_strings = array(
			''                  => array(),
			'?bob=ted'          => array( 'bob' => 'ted' ),
			'?bob[]=1&bob[3]=5' => array( 'bob' => array( 1, 3 => 5 ) ),
			'?what=0'           => array( 'what' => 0 ),
		);

		foreach( $this->namespaces as $ns ) {
			foreach( $server_arrays as $server_array ) {
				foreach( $query_strings as $query_string => $query_data ) {
					$router = new HttpRouter($ns, $server_array);

					$rm = isset($server_array['REQUEST_METHOD']) ? strtoupper($server_array['REQUEST_METHOD']) : null;

					$this->assertSame(false, $router->match('' . $query_string));

					$result = array(
						'controller' => $ns . '\\index',
						'options'    => $query_data,
						'action'     => null,
					);
					if( $rm ) {
						$result['request']['method'] = $rm;
					}
					$this->assertEquals($result, $router->match('/' . $query_string));

					$result = array(
						'controller' => $ns . '\\Baz\\Qux',
						'options'    => $query_data,
						'action'     => null,
					);
					if( $rm ) {
						$result['request']['method'] = $rm;
					}
					$this->assertEquals($result, $router->match('/Baz/Qux' . $query_string));

					$result = array(
						'controller' => $ns . '\\Baz\\Qux',
						'options'    => $query_data,
						'action'     => 'What',
					);
					if( $rm ) {
						$result['request']['method'] = $rm;
					}
					$this->assertEquals($result, $router->match('/Baz/Qux:What' . $query_string));

					$this->assertSame(false, $router->match('/Baz/Qux.json:10' . $query_string)); //So we don't confuse the colon syntax with ports
				}
			}
		}
	}

	public function testGenerate() {

		foreach( $this->namespaces as $ns ) {
			$router = new HttpRouter($ns);

			// Test Fully Qualified
			$this->assertSame('/Monkey', $router->generate($ns . '\\Monkey'));
			$this->assertSame('/Monkeys/Hate/Prefixes', $router->generate(ltrim($ns, '\\') . '\\Monkeys\\Hate\\Prefixes'));
			$this->assertSame('/Monkeys/Love/Long/Paths', $router->generate($ns . '\\Monkeys\\Love\\Long\\Paths'));

			$this->assertSame('/Monkeys/Love/Long/Paths:list', $router->generate($ns . '\\Monkeys\\Love\\Long\\Paths', 'list'));

			$this->assertSame('/Monkeys/Love/Long/Paths:list?what=butt&crap%5Banimal%5D%5Btype%5D=fish',
				$router->generate($ns . '\\Monkeys\\Love\\Long\\Paths', 'list', array( 'what' => 'butt', 'crap' => array( 'animal' => array( 'type' => 'fish' ) ) )));

			// Test Relative
			$this->assertSame('/Monkey', $router->generate('Monkey'));
			$this->assertSame('/Monkeys/Hate/Prefixes', $router->generate('Monkeys\\Hate\\Prefixes'));
			$this->assertSame('/Monkeys/Love/Long/Paths', $router->generate('Monkeys\\Love\\Long\\Paths'));

			$this->assertSame('/Monkeys/Love/Long/Paths:list', $router->generate('Monkeys\\Love\\Long\\Paths', 'list'));

			$this->assertSame('/Monkeys/Love/Long/Paths:list?what=butt&crap%5Banimal%5D%5Btype%5D=fish',
				$router->generate('Monkeys\\Love\\Long\\Paths', 'list', array( 'what' => 'butt', 'crap' => array( 'animal' => array( 'type' => 'fish' ) ) )));
		}
	}

	/**
	 * @expectedException \Corpus\Router\Exceptions\NonRoutableException
	 */
	public function testGenerateException() {
		$router = new HttpRouter('\\Foo');
		$router->generate(7);
	}

	/**
	 * @expectedException \Corpus\Router\Exceptions\NonRoutableException
	 */
	public function testGenerateException2() {
		$router = new HttpRouter('\\Foo');
		$router->generate('\\Bar\\Loving');
	}

	public function testGetNamespace() {
		$router = new HttpRouter('\\Foo\\Bar');
		$this->assertEquals('Foo\\Bar', $router->getNamespace());

		$router = new HttpRouter('Foo\\Bar\\Baz');
		$this->assertEquals('Foo\\Bar\\Baz', $router->getNamespace());
	}

}
