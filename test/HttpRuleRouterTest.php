<?php

namespace Corpus\Test\Router;

use Corpus\Router\Exceptions\RouteGenerationFailedException;
use Corpus\Router\HttpRuleRouter;

class HttpRuleRouterTest extends \PHPUnit\Framework\TestCase {

	protected $namespaces = [ '\\Foo', '\\Foo\\Bar', '\\Foo\\Bar\\ClassName', '\\Fun\\With_Underscores', '\\日本の\\しい' ];

	public function testMatch() {

		$server_arrays = [ [], [ 'REQUEST_METHOD' => 'post' ], [ 'REQUEST_METHOD' => 'Get' ] ];
		$query_strings = [ '' => [], '?bob=ted' => [ 'bob' => 'ted' ], '?bob[]=1&bob[3]=5' => [ 'bob' => [ 1, 3 => 5 ] ] ];

		foreach( $this->namespaces as $ns ) {
			foreach( $server_arrays as $server_array ) {
				foreach( $query_strings as $query_string => $query_data ) {
					$router = new HttpRuleRouter($ns, $server_array);
					$router->addRule('what/{opt}/butt', 'index');
					$router->addRule('who/{_action}/what', 'login\\funkytown');
					$router->addRule('cat/{dog|d}/bar', 'login\\funkytown');

					$rm = isset($server_array['REQUEST_METHOD']) ? strtoupper($server_array['REQUEST_METHOD']) : null;

					$this->assertSame(false, $router->match('' . $query_string));

					$result = [
						'controller' => $ns . '\\index',
						'options'    => array_merge([ 'opt' => 'the' ], $query_data),
						'action'     => null,
					];
					if( $rm ) {
						$result['request']['method'] = $rm;
					}

					$this->assertEquals($result, $router->match('what/the/butt' . $query_string));

					$result = [
						'controller' => $ns . '\\login\\funkytown',
						'options'    => array_merge([ '_action' => 'myAction' ], $query_data),
						'action'     => 'myAction',
					];
					if( $rm ) {
						$result['request']['method'] = $rm;
					}

					$this->assertEquals($result, $router->match('who/myAction/what' . $query_string));

					$result = [
						'controller' => $ns . '\\login\\funkytown',
						'options'    => array_merge([ 'dog' => 10 ], $query_data),
						'action'     => null,
					];
					if( $rm ) {
						$result['request']['method'] = $rm;
					}

					$this->assertEquals($result, $router->match('cat/10/bar' . $query_string));
					$this->assertFalse($router->match('cat/string/bar' . $query_string));
				}
			}
		}
	}

	public function testGenerate() {
		foreach( $this->namespaces as $ns ) {
			$router = new HttpRuleRouter($ns);

			// Test Fully Qualified
			$router->addRule('who/{_action}/what', 'Monkey');
			$this->assertSame('/who/myAction/what', $router->generate($ns . '\\Monkey', 'myAction'));
			$this->assertSame('/who/myAction/what?param=whynot', $router->generate($ns . '\\Monkey', 'myAction', [ 'param' => 'whynot' ]));

			// Regression test -
			// y values kept, nulls removed.
			$this->assertSame('/who/foo/what?why=0&butt=', $router->generate($ns . '\\Monkey', 'foo', [ 'why' => 0, 'butt' => '', 'gut' => null ]));

			$router->addRule('foo/{bar}/baz', 'Donkey');
			$this->assertSame('/foo/bbq/baz', $router->generate($ns . '\\Donkey', null, [ 'bar' => 'bbq' ]));
			$this->assertSame('/foo/bbq/baz?extra=awesome', $router->generate($ns . '\\Donkey', null, [ 'bar' => 'bbq', 'extra' => 'awesome' ]));
			// test matched params must be scalar
			try {
				$router->generate($ns . '\\Donkey', null, [ 'bar' => [ 'baz', 'bum' ] ]);
				$this->fail('Should have thrown a GenerationFailedException');
			} catch(RouteGenerationFailedException $e) { 
				// noop
			}

			$router->addRule('bar/{qux|d}/bbq', 'Goose');
			$this->assertSame('/bar/1986/bbq', $router->generate($ns . '\\Goose', null, [ 'qux' => 1986 ]));
			// test matched parens must match type
			try {
				$router->generate($ns . '\\Goose', null, [ 'qux' => 'quux' ]);
				$this->fail('Should have thrown a GenerationFailedException');
			} catch(RouteGenerationFailedException $e) { 
				// noop
			}

			try {
				$router->generate($ns . '\\Goose', null, [ 'qux' => '-1000' ]);
				$this->fail('Should have thrown a GenerationFailedException');
			} catch(RouteGenerationFailedException $e) { 
				// noop
			}

			//doesn't do signed
			try {
				$router->generate($ns . '\\Goose', null, [ 'qux' => '1.1' ]);
				$this->fail('Should have thrown a GenerationFailedException');
			} catch(RouteGenerationFailedException $e) { 
				// noop
			}

			//doesn't do float

			$router->addRule('qux/{quux|a}/garply', 'Gander');
			$this->assertSame('/qux/string/garply', $router->generate($ns . '\\Gander', null, [ 'quux' => 'string' ]));
			// test matched parens must match type
			try {
				$router->generate($ns . '\\Gander', null, [ 'quux' => 1986 ]);
				$this->fail('Should have thrown a GenerationFailedException');
			} catch(RouteGenerationFailedException $e) { 
				// noop
			}

			try {
				$router->generate($ns . '\\Gander', null, [ 'quux' => 'alpha_underscore' ]);
				$this->fail('Should have thrown a GenerationFailedException');
			} catch(RouteGenerationFailedException $e) { 
				// noop
			}

			try {
				$router->generate($ns . '\\Gander', null, [ 'garply' => 'has-hyphen' ]);
				$this->fail('Should have thrown a GenerationFailedException');
			} catch(RouteGenerationFailedException $e) { 
				// noop
			}

			$router->addRule('quux/{garply|w}/fry', 'Meander');
			$this->assertSame('/quux/string/fry', $router->generate($ns . '\\Meander', null, [ 'garply' => 'string' ]));
			$this->assertSame('/quux/1986/fry', $router->generate($ns . '\\Meander', null, [ 'garply' => '1986' ]));
			$this->assertSame('/quux/alpha_underscore/fry', $router->generate($ns . '\\Meander', null, [ 'garply' => 'alpha_underscore' ]));

			try {
				$router->generate($ns . '\\Meander', null, [ 'garply' => 'has space' ]);
				$this->fail('Should have thrown a GenerationFailedException');
			} catch(RouteGenerationFailedException $e) { 
				// noop
			}

			try {
				$router->generate($ns . '\\Meander', null, [ 'garply' => 'has-hyphen' ]);
				$this->fail('Should have thrown a GenerationFailedException');
			} catch(RouteGenerationFailedException $e) { 
				// noop
			}

			$router = new HttpRuleRouter($ns);

			// Test Double Fully Qualified
			$router->addRule('who/{_action}/what', $ns . '\\Monkey');
			$this->assertSame('/who/otherAction/what', $router->generate($ns . '\\Monkey', 'otherAction'));
		}
	}

	public function testAddRuleException() {
		$this->expectException(\Corpus\Router\Exceptions\InvalidRuleException::class);
		$this->expectExceptionCode(\Corpus\Router\HttpRuleRouter::ERROR_DUPLICATED_KEY);

		$router = new HttpRuleRouter('\\Foo');
		$router->addRule('{what}/{what}', 'index');
	}

	public function testAddRuleException2() {
		$this->expectException(\Corpus\Router\Exceptions\InvalidRuleException::class);
		$this->expectExceptionCode(\Corpus\Router\HttpRuleRouter::ERROR_UNKNOWN_TYPE);

		$router = new HttpRuleRouter('\\Foo');
		$router->addRule('{what|q}', 'index');
	}

	public function testGenerateException() {
		$this->expectException(\Corpus\Router\Exceptions\NonRoutableException::class);

		$router = new HttpRuleRouter('\\Foo');
		$router->generate(7);
	}

}
