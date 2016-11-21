<?php
/**
 * This file is part of the prooph/service-bus.
 * (c) 2014-%year% prooph software GmbH <contact@prooph.de>
 * (c) 2015-%year% Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\EventStoreBusBridge\Container;

use Interop\Container\ContainerInterface;
use Prooph\EventStoreBusBridge\Container\TransactionManagerFactory;
use Prooph\EventStoreBusBridge\Exception\InvalidArgumentException;
use Prooph\EventStoreBusBridge\TransactionManager;
use Prooph\ServiceBus\CommandBus;
use Prophecy\Argument;

class TransactionManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_creates_a_transaction_manager(): void
    {
        $commandBus = $this->prophesize(CommandBus::class);

        $commandBus->utilize(Argument::type(TransactionManager::class))->shouldBeCalled();

        $container = $this->prophesize(ContainerInterface::class);

        $container->get(CommandBus::class)->willReturn($commandBus->reveal());

        $factory = new TransactionManagerFactory();

        $transactionManager = $factory($container->reveal());

        $this->assertInstanceOf(TransactionManager::class, $transactionManager);
    }

    /**
     * @test
     */
    public function it_creates_a_transaction_manager_via_callstatic(): void
    {
        $commandBus = $this->prophesize(CommandBus::class);

        $commandBus->utilize(Argument::type(TransactionManager::class))->shouldBeCalled();

        $container = $this->prophesize(ContainerInterface::class);

        $container->get('foo')->willReturn($commandBus->reveal());

        $type = 'foo';
        $transactionManager = TransactionManagerFactory::$type($container->reveal());

        $this->assertInstanceOf(TransactionManager::class, $transactionManager);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_invalid_container_passed_to_callstatic(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $type = 'foo';
        TransactionManagerFactory::$type('invalid container');
    }
}
