<?php

namespace Corpus\Test\Router;

use Corpus\Router\HttpRouter;

class HttpRouterTest extends \PHPUnit\Framework\TestCase {

	protected $namespaces = [ '\\Foo', '\\Foo\\Bar', '\\Foo\\Bar\\ClassName', '\\Fun\\With_Underscores', '\\日本の\\しい' ];

	public function testMatch() : void {

		$serverArrays = [ [], [ 'REQUEST_METHOD' => 'post' ], [ 'REQUEST_METHOD' => 'Get' ] ];
		$queryStrings = [
			''                  => [],
			'?bob=ted'          => [ 'bob' => 'ted' ],
			'?bob[]=1&bob[3]=5' => [ 'bob' => [ 1, 3 => 5 ] ],
			'?what=0'           => [ 'what' => 0 ],
		];

		foreach( $this->namespaces as $ns ) {
			foreach( $serverArrays as $serverArray ) {
				foreach( $queryStrings as $queryString => $queryData ) {
					$router = new HttpRouter($ns, $serverArray);

					$rm = isset($serverArray['REQUEST_METHOD']) ? strtoupper($serverArray['REQUEST_METHOD']) : null;

					$this->assertNull($router->match('' . $queryString));

					$result = [
						'controller' => $ns . '\\index',
						'options'    => $queryData,
						'action'     => null,
					];
					if( $rm ) {
						$result['request']['method'] = $rm;
					}

					$this->assertEquals($result, $router->match('/' . $queryString));

					$result = [
						'controller' => $ns . '\\Baz\\Qux',
						'options'    => $queryData,
						'action'     => null,
					];
					if( $rm ) {
						$result['request']['method'] = $rm;
					}

					$this->assertEquals($result, $router->match('/Baz/Qux' . $queryString));

					$result = [
						'controller' => $ns . '\\Baz\\Qux',
						'options'    => $queryData,
						'action'     => 'What',
					];
					if( $rm ) {
						$result['request']['method'] = $rm;
					}

					$this->assertEquals($result, $router->match('/Baz/Qux:What' . $queryString));

					$this->assertNull($router->match('/Baz/Qux.json:10' . $queryString)); //So we don't confuse the colon syntax with ports
				}
			}
		}
	}

	public function testGenerate() : void {

		foreach( $this->namespaces as $ns ) {
			$router = new HttpRouter($ns);

			// Test Fully Qualified
			$this->assertSame('/Monkey', $router->generate($ns . '\\Monkey'));
			$this->assertSame('/Monkeys/Hate/Prefixes', $router->generate(ltrim($ns, '\\') . '\\Monkeys\\Hate\\Prefixes'));
			$this->assertSame('/Monkeys/Love/Long/Paths', $router->generate($ns . '\\Monkeys\\Love\\Long\\Paths'));

			$this->assertSame('/Monkeys/Love/Long/Paths:list', $router->generate($ns . '\\Monkeys\\Love\\Long\\Paths', 'list'));

			$this->assertSame('/Monkeys/Love/Long/Paths:list?what=butt&crap%5Banimal%5D%5Btype%5D=fish',
				$router->generate($ns . '\\Monkeys\\Love\\Long\\Paths', 'list', [ 'what' => 'butt', 'crap' => [ 'animal' => [ 'type' => 'fish' ] ] ]));

			// Test Relative
			$this->assertSame('/Monkey', $router->generate('Monkey'));
			$this->assertSame('/Monkeys/Hate/Prefixes', $router->generate('Monkeys\\Hate\\Prefixes'));
			$this->assertSame('/Monkeys/Love/Long/Paths', $router->generate('Monkeys\\Love\\Long\\Paths'));

			$this->assertSame('/Monkeys/Love/Long/Paths:list', $router->generate('Monkeys\\Love\\Long\\Paths', 'list'));

			$this->assertSame('/Monkeys/Love/Long/Paths:list?what=butt&crap%5Banimal%5D%5Btype%5D=fish',
				$router->generate('Monkeys\\Love\\Long\\Paths', 'list', [ 'what' => 'butt', 'crap' => [ 'animal' => [ 'type' => 'fish' ] ] ]));
		}
	}

	public function testGenerateException() : void {
		$this->expectException(\Corpus\Router\Exceptions\NonRoutableException::class);

		$router = new HttpRouter('\\Foo');
		$router->generate(7);
	}

	public function testGenerateException2() : void {
		$this->expectException(\Corpus\Router\Exceptions\NonRoutableException::class);

		$router = new HttpRouter('\\Foo');
		$router->generate('\\Bar\\Loving');
	}

	public function testGetNamespace() : void {
		$router = new HttpRouter('\\Foo\\Bar');
		$this->assertEquals('Foo\\Bar', $router->getNamespace());

		$router = new HttpRouter('Foo\\Bar\\Baz');
		$this->assertEquals('Foo\\Bar\\Baz', $router->getNamespace());
	}

}
