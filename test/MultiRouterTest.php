<?php

namespace Corpus\Test\Router;

use Corpus\Router\MultiRouter;

class MultiRouterTest extends \PHPUnit_Framework_TestCase {

	public function testEmpty() {
		$router = new MultiRouter();

		$this->assertFalse($router->match('index.html'));
	}

	public function testMatch_None() {
		$router = new MultiRouter();

		/**
		 * @var $ri1 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\RouterInterface
		 */
		$ri1 = $this->getMock('\Corpus\Router\Interfaces\RouterInterface');

		$ri1->expects($this->exactly(3))->method('match')->with(
			$this->equalTo('index.html')
		)->will($this->returnValue(false));

		$router->addRouter($ri1);
		$router->addRouter($ri1);
		$router->addRouter($ri1);

		$this->assertFalse($router->match('index.html'));
	}

	public function testMatch_MidStream() {
		$router = new MultiRouter();

		/**
		 * @var $ri1 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\RouterInterface
		 * @var $ri2 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\RouterInterface
		 * @var $ri3 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\RouterInterface
		 */
		$ri1 = $this->getMock('\Corpus\Router\Interfaces\RouterInterface');
		$ri2 = $this->getMock('\Corpus\Router\Interfaces\RouterInterface');
		$ri3 = $this->getMock('\Corpus\Router\Interfaces\RouterInterface');

		$ri1->expects($this->once())->method('match')->with(
			$this->equalTo('index.html')
		)->will($this->returnValue(false));

		$ri2->expects($this->once())->method('match')->with(
			$this->equalTo('index.html')
		)->will($this->returnValue(array( true )));

		$ri3->expects($this->never())->method('match');

		$router->addRouter($ri1);
		$router->addRouter($ri2);
		$router->addRouter($ri3);

		$this->assertSame(array( true ), $router->match('index.html'));
	}

	public function testConstruct() {
		/**
		 * @var $ri1 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\RouterInterface
		 * @var $ri2 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\RouterInterface
		 * @var $ri3 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\RouterInterface
		 */
		$ri1 = $this->getMock('\Corpus\Router\Interfaces\RouterInterface');
		$ri2 = $this->getMock('\Corpus\Router\Interfaces\RouterInterface');
		$ri3 = $this->getMock('\Corpus\Router\Interfaces\RouterInterface');

		$router = new MultiRouter($ri1, $ri2, $ri3);
		$this->assertSame([ $ri1, $ri2, $ri3 ], $router->getRouters());
	}
}
