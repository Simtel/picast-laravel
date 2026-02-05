<?php

declare(strict_types=1);

namespace Tests\Unit\Common;

use App;
use App\Common\CommandBus;
use Illuminate\Contracts\Container\Container;
use Mockery;
use Tests\TestCase;

class CommandBusTest extends TestCase
{
    private CommandBus $commandBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commandBus = new CommandBus();

        // Имитируем контейнер Laravel для тестов
        $container = Mockery::mock(Container::class);
        App::swap($container);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_register_and_execute_command_successfully(): void
    {

        $command = $this->getMockBuilder(App\Common\CommandInterface::class)->getMock();
        $handler = $this->getMockBuilder(App\Common\CommandHandlerInterface::class)->getMock();
        $handler->expects($this->once())->method('handle')->with($command);

        App::shouldReceive('make')->with('TestHandler')->andReturn($handler)->once();


        $this->commandBus->register(get_class($command), 'TestHandler');
        $this->commandBus->execute($command);

    }

    public function test_execute_throws_exception_when_handler_not_registered(): void
    {
        $command = new class () {};

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Handler not found for ' . get_class($command));

        $this->commandBus->execute($command);
    }

    public function test_execute_calls_correct_handler_for_registered_command(): void
    {

        $command1 = new class () implements App\Common\CommandInterface {};
        $command2 = new class () implements App\Common\CommandInterface {};

        $handler1 = $this->getMockBuilder(App\Common\CommandHandlerInterface::class)->getMock();
        $handler1->expects($this->once())->method('handle')->with($command1);

        $handler2 = $this->getMockBuilder(App\Common\CommandHandlerInterface::class)->getMock();
        $handler2->expects($this->once())->method('handle')->with($command2);

        App::shouldReceive('make')->with('Handler1')->andReturn($handler1)->once();
        App::shouldReceive('make')->with('Handler2')->andReturn($handler2)->once();


        $this->commandBus->register(get_class($command1), 'Handler1');
        $this->commandBus->register(get_class($command2), 'Handler2');

        $this->commandBus->execute($command1);
        $this->commandBus->execute($command2);


    }
}
