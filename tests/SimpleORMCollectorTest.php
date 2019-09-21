<?php declare(strict_types=1);

namespace WyriHaximus\React\Tests\Inspector\SimpleORM;

use ApiClients\Tools\TestUtilities\TestCase;
use Rx\React\Promise;
use WyriHaximus\React\Inspector\Metric;
use WyriHaximus\React\Inspector\SimpleORM\SimpleORMCollector;
use WyriHaximus\React\SimpleORM\Middleware\QueryCountMiddleware;

/**
 * @internal
 */
final class SimpleORMCollectorTest extends TestCase
{
    public function testCollect(): void
    {
        $middleware =  new QueryCountMiddleware(1);
        $collector = new SimpleORMCollector();
        $collector->register('primairy', $middleware);
        $metrics = $this->await(Promise::fromObservable($collector->collect()->toArray()));

        self::assertCount(4, $metrics);
        /** @var Metric $metric */
        foreach ($metrics as $metric) {
            self::assertInstanceOf(Metric::class, $metric);
            self::assertSame(0.0, $metric->getValue());
        }
    }
}
