<?php

namespace Corpus\Test\Router;

use Corpus\Router\Exceptions\RouteGenerationFailedException;
use Corpus\Router\Interfaces\ReversibleRouterInterface;
use Corpus\Router\MultiReversibleRouter;

class MultiReversibleRouterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException \Corpus\Router\Exceptions\RouteGenerationFailedException
	 */
	public function testEmpty() {
		$router = new MultiReversibleRouter();

		$router->generate('index');
	}

	/**
	 * @expectedException \Corpus\Router\Exceptions\RouteGenerationFailedException
	 */
	public function testMatch_None() {
		$router = new MultiReversibleRouter();

		/**
		 * @var $ri1 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\ReversibleRouterInterface
		 */
		$ri1 = $this->getMock('\Corpus\Router\Interfaces\ReversibleRouterInterface');

		$ri1->expects($this->exactly(3))->method('generate')->with(
			$this->equalTo('index'),
			$this->equalTo('bbq'),
			$this->equalTo(array( 'foo' => 'bar' ))
		)->will($this->throwException(new RouteGenerationFailedException));

		$router->addRouter($ri1);
		$router->addRouter($ri1);
		$router->addRouter($ri1);

		$this->assertFalse($router->generate('index', 'bbq', array( 'foo' => 'bar' )));
	}

	public function testMatch_MidStream() {
		$router = new MultiReversibleRouter();

		/**
		 * @var $ri1 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\ReversibleRouterInterface
		 * @var $ri2 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\ReversibleRouterInterface
		 * @var $ri3 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\ReversibleRouterInterface
		 */
		$ri1 = $this->getMock('\Corpus\Router\Interfaces\ReversibleRouterInterface');
		$ri2 = $this->getMock('\Corpus\Router\Interfaces\ReversibleRouterInterface');
		$ri3 = $this->getMock('\Corpus\Router\Interfaces\ReversibleRouterInterface');

		$ri1->expects($this->once())->method('generate')->with(
			$this->equalTo('index'),
			$this->equalTo('bbq'),
			$this->equalTo(array( 'foo' => 'bar' ))
		)->will($this->throwException(new RouteGenerationFailedException));

		$ri2->expects($this->once())->method('generate')->with(
			$this->equalTo('index'),
			$this->equalTo('bbq'),
			$this->equalTo(array( 'foo' => 'bar' ))
		)->will($this->returnValue('index.html'));

		$ri3->expects($this->never())->method('match');

		$router->addRouter($ri1);
		$router->addRouter($ri2);
		$router->addRouter($ri3);

		$this->assertSame('index.html', $router->generate('index', 'bbq', array( 'foo' => 'bar' )));
	}

	public function testConstruct() {
		/**
		 * @var $ri1 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\ReversibleRouterInterface
		 * @var $ri2 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\ReversibleRouterInterface
		 * @var $ri3 \PHPUnit_Framework_MockObject_MockObject|\Corpus\Router\Interfaces\ReversibleRouterInterface
		 */
		$ri1 = $this->getMock('\Corpus\Router\Interfaces\ReversibleRouterInterface');
		$ri2 = $this->getMock('\Corpus\Router\Interfaces\ReversibleRouterInterface');
		$ri3 = $this->getMock('\Corpus\Router\Interfaces\ReversibleRouterInterface');

		$router = new MultiReversibleRouter($ri1, $ri2, $ri3);
		$this->assertSame(array( $ri1, $ri2, $ri3 ), $router->getRouters());
	}
}
