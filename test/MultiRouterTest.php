<?php

namespace Corpus\Test\Router;

use Corpus\Router\MultiRouter;

class MultiRouterTest extends \PHPUnit\Framework\TestCase {

	public function testEmpty() {
		$router = new MultiRouter;

		$this->assertNull($router->match('index.html'));
	}

	public function testMatch_None() {
		$router = new MultiRouter;

		/**
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit_Framework_MockObject_MockObject $ri1
		 */
		$ri1 = $this->createMock('\Corpus\Router\Interfaces\RouterInterface');

		$ri1->expects($this->exactly(3))->method('match')->with(
			$this->equalTo('index.html')
		)->willReturn(null);

		$router->addRouter($ri1);
		$router->addRouter($ri1);
		$router->addRouter($ri1);

		$this->assertNull($router->match('index.html'));
	}

	public function testMatch_MidStream() {
		$router = new MultiRouter;

		/**
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit_Framework_MockObject_MockObject $ri1
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit_Framework_MockObject_MockObject $ri2
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit_Framework_MockObject_MockObject $ri3
		 */
		$ri1 = $this->createMock('\Corpus\Router\Interfaces\RouterInterface');
		$ri2 = $this->createMock('\Corpus\Router\Interfaces\RouterInterface');
		$ri3 = $this->createMock('\Corpus\Router\Interfaces\RouterInterface');

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

	public function testConstruct() {
		/**
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit_Framework_MockObject_MockObject $ri1
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit_Framework_MockObject_MockObject $ri2
		 * @var \Corpus\Router\Interfaces\RouterInterface|\PHPUnit_Framework_MockObject_MockObject $ri3
		 */
		$ri1 = $this->createMock('\Corpus\Router\Interfaces\RouterInterface');
		$ri2 = $this->createMock('\Corpus\Router\Interfaces\RouterInterface');
		$ri3 = $this->createMock('\Corpus\Router\Interfaces\RouterInterface');

		$router = new MultiRouter($ri1, $ri2, $ri3);
		$this->assertSame([ $ri1, $ri2, $ri3 ], $router->getRouters());
	}

}
