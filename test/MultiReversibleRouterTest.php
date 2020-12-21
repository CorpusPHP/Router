<?php

namespace Corpus\Test\Router;

use Corpus\Router\Exceptions\RouteGenerationFailedException;
use Corpus\Router\Interfaces\ReversibleRouterInterface;
use Corpus\Router\MultiReversibleRouter;

class MultiReversibleRouterTest extends \PHPUnit\Framework\TestCase {

	public function testEmpty() {
		$this->expectException(\Corpus\Router\Exceptions\RouteGenerationFailedException::class);

		$router = new MultiReversibleRouter;

		$router->generate('index');
	}

	public function testMatch_None() {
		$this->expectException(\Corpus\Router\Exceptions\RouteGenerationFailedException::class);

		$router = new MultiReversibleRouter;

		/**
		 * @var \Corpus\Router\Interfaces\ReversibleRouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri1
		 */
		$ri1 = $this->createMock(ReversibleRouterInterface::class);

		$ri1->expects($this->exactly(3))->method('generate')->with(
			$this->equalTo('index'),
			$this->equalTo('bbq'),
			$this->equalTo([ 'foo' => 'bar' ])
		)->will($this->throwException(new RouteGenerationFailedException));

		$router->addRouter($ri1);
		$router->addRouter($ri1);
		$router->addRouter($ri1);

		$this->assertFalse($router->generate('index', 'bbq', [ 'foo' => 'bar' ]));
	}

	public function testMatch_MidStream() {
		$router = new MultiReversibleRouter;

		/**
		 * @var \Corpus\Router\Interfaces\ReversibleRouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri1
		 * @var \Corpus\Router\Interfaces\ReversibleRouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri2
		 * @var \Corpus\Router\Interfaces\ReversibleRouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri3
		 */
		$ri1 = $this->createMock(ReversibleRouterInterface::class);
		$ri2 = $this->createMock(ReversibleRouterInterface::class);
		$ri3 = $this->createMock(ReversibleRouterInterface::class);

		$ri1->expects($this->once())->method('generate')->with(
			$this->equalTo('index'),
			$this->equalTo('bbq'),
			$this->equalTo([ 'foo' => 'bar' ])
		)->will($this->throwException(new RouteGenerationFailedException));

		$ri2->expects($this->once())->method('generate')->with(
			$this->equalTo('index'),
			$this->equalTo('bbq'),
			$this->equalTo([ 'foo' => 'bar' ])
		)->willReturn('index.html');

		$ri3->expects($this->never())->method('match');

		$router->addRouter($ri1);
		$router->addRouter($ri2);
		$router->addRouter($ri3);

		$this->assertSame('index.html', $router->generate('index', 'bbq', [ 'foo' => 'bar' ]));
	}

	public function testConstruct() {
		/**
		 * @var \Corpus\Router\Interfaces\ReversibleRouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri1
		 * @var \Corpus\Router\Interfaces\ReversibleRouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri2
		 * @var \Corpus\Router\Interfaces\ReversibleRouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri3
		 */
		$ri1 = $this->createMock(ReversibleRouterInterface::class);
		$ri2 = $this->createMock(ReversibleRouterInterface::class);
		$ri3 = $this->createMock(ReversibleRouterInterface::class);

		$router = new MultiReversibleRouter($ri1, $ri2, $ri3);
		$this->assertSame([ $ri1, $ri2, $ri3 ], $router->getRouters());
	}

}
