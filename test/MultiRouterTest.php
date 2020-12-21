<?php

namespace Corpus\Test\Router;

use Corpus\Router\Interfaces\RouterInterface;
use Corpus\Router\MultiRouter;

class MultiRouterTest extends \PHPUnit\Framework\TestCase {

	public function testEmpty() : void {
		$router = new MultiRouter;

		$this->assertNull($router->match('index.html'));
	}

	public function testMatch_None() : void {
		$router = new MultiRouter;

		/**
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri1
		 */
		$ri1 = $this->createMock(RouterInterface::class);

		$ri1->expects($this->exactly(3))->method('match')->with(
			$this->equalTo('index.html')
		)->willReturn(null);

		$router->addRouter($ri1);
		$router->addRouter($ri1);
		$router->addRouter($ri1);

		$this->assertNull($router->match('index.html'));
	}

	public function testMatch_MidStream() : void {
		$router = new MultiRouter;

		/**
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri1
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri2
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri3
		 */
		$ri1 = $this->createMock(RouterInterface::class);
		$ri2 = $this->createMock(RouterInterface::class);
		$ri3 = $this->createMock(RouterInterface::class);

		$ri1->expects($this->once())->method('match')->with(
			$this->equalTo('index.html')
		)->willReturn(null);

		$ri2->expects($this->once())->method('match')->with(
			$this->equalTo('index.html')
		)->willReturn([ true ]);

		$ri3->expects($this->never())->method('match');

		$router->addRouter($ri1);
		$router->addRouter($ri2);
		$router->addRouter($ri3);

		$this->assertSame([ true ], $router->match('index.html'));
	}

	public function testConstruct() : void {
		/**
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri1
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri2
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit\Framework\MockObject\MockObject $ri3
		 */
		$ri1 = $this->createMock(RouterInterface::class);
		$ri2 = $this->createMock(RouterInterface::class);
		$ri3 = $this->createMock(RouterInterface::class);

		$router = new MultiRouter($ri1, $ri2, $ri3);
		$this->assertSame([ $ri1, $ri2, $ri3 ], $router->getRouters());
	}

}
