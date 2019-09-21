<?php declare(strict_types=1);

namespace WyriHaximus\React\Inspector\SimpleORM;

use function ApiClients\Tools\Rx\observableFromArray;
use Rx\ObservableInterface;
use WyriHaximus\React\Http\Middleware\MeasureMiddleware;
use WyriHaximus\React\Inspector\CollectorInterface;
use WyriHaximus\React\Inspector\Metric;
use WyriHaximus\React\SimpleORM\Middleware\QueryCountMiddleware;

final class SimpleORMCollector implements CollectorInterface
{
    /** @var QueryCountMiddleware[] */
    private $middleware = [];

    public function register(string $key, QueryCountMiddleware $middleware): void
    {
        $this->middleware[$key] = $middleware;
    }

    public function collect(): ObservableInterface
    {
        $metrics = [];

        /**
         * @var string            $key
         * @var MeasureMiddleware $middleware
         */
        foreach ($this->middleware as $key => $middleware) {
            /** @var Metric $metric */
            $metrics[] = new Metric(
                $key . '.query.total',
                $middleware->getCount()
            );
            $middleware->resetCount();
        }

        return observableFromArray($metrics);
    }

    public function cancel(): void
    {
        $this->middleware = [];
    }
}
