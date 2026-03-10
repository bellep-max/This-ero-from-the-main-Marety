<?php

namespace App\Http\Controllers\Backend\Encore;

use App\Models\User;
use Closure;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class ApiTester
{
    /**
     * The Illuminate application instance.
     *
     * @var Application
     */
    protected mixed $app;

    public static array $methodColors = [
        'GET' => 'btn-success',
        'HEAD' => 'btn-secondary',
        'POST' => 'btn-info',
        'PUT' => 'btn-warning',
        'DELETE' => 'btn-danger',
        'PATCH' => 'btn-info',
    ];

    /**
     * ApiTester constructor.
     */
    public function __construct(?Application $app = null)
    {
        $this->app = $app ?: app();
    }

    /**
     * @param  null  $user
     *
     * @throws BindingResolutionException
     * @throws \Throwable
     */
    public function call(string $method, string $uri, array $parameters = [], $user = null): Response
    {
        if ($user) {
            $this->loginUsing($user);
        }
        $kernel = $this->app->make('Illuminate\Contracts\Http\Kernel');
        $uri = $this->prepareUrlForRequest($uri);
        $files = [];
        foreach ($parameters as $key => $val) {
            if ($val instanceof UploadedFile) {
                $files[$key] = $val;
                unset($parameters[$key]);
            }
        }
        $symfonyRequest = SymfonyRequest::create(
            $uri,
            $method,
            $parameters,
            [],
            $files,
            ['HTTP_ACCEPT' => 'application/json']
        );
        $request = Request::createFromBase($symfonyRequest);
        try {
            $response = $kernel->handle($request);
        } catch (Exception $e) {
            $response = app('Illuminate\Contracts\Debug\ExceptionHandler')->render($request, $e);
        }
        $kernel->terminate($request, $response);

        return $response;
    }

    /**
     * Login a user by giving userid.
     */
    protected function loginUsing($userId): void
    {
        $guard = 'api';
        $user = User::find($userId);
        $this->app['auth']->guard($guard)->setUser($user);
    }

    public function parseResponse(Response $response): JsonResponse
    {
        $content = $response->getContent();
        /*$jsoned = json_decode($content);

        if (json_last_error() == JSON_ERROR_NONE) {
            $content = json_encode($jsoned, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        $lang = 'json';
        $contentType = $response->headers->get('content-type');
        if (Str::contains($contentType, 'html')) {
            $lang = 'html';
        }*/

        $contentType = $response->headers->get('content-type');

        return response()->json([
            'response_headers' => $response->headers->all(),
            'content' => $content,
            'contentType' => $contentType,
        ]);

        /*return [
            'headers'    => json_encode($response->headers->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'cookies'    => json_encode($response->headers->getCookies(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            'content'    => $content,
            'language'   => $lang,
            'status'     => [
                'code'  => $response->getStatusCode(),
                'text'  => $this->getStatusText($response),
            ],
        ];*/
    }

    /**
     * @throws \ReflectionException
     */
    protected function getStatusText(Response $response): string
    {
        $statusText = new ReflectionProperty($response, 'statusText');
        $statusText->setAccessible(true);

        return $statusText->getValue($response);
    }

    /**
     * Filter the given array of files, removing any empty values.
     *
     * @param  array  $files
     * @return mixed
     */
    protected function filterFiles($files)
    {
        foreach ($files as $key => $file) {
            if ($file instanceof UploadedFile) {
                continue;
            }
            if (is_array($file)) {
                if (!isset($file['name'])) {
                    $files[$key] = $this->filterFiles($file);
                } elseif (isset($file) && $file !== 0) {
                    unset($files[$key]);
                }

                continue;
            }
            unset($files[$key]);
        }

        return $files;
    }

    /**
     * Turn the given URI into a fully qualified URL.
     *
     * @param  string  $uri
     * @return string
     */
    protected function prepareUrlForRequest($uri)
    {
        if (Str::startsWith($uri, '/')) {
            $uri = substr($uri, 1);
        }
        if (!Str::startsWith($uri, 'http')) {
            $uri = config('app.url') . '/' . $uri;
        }

        return trim($uri, '/');
    }

    /**
     * Get all api routes.
     *
     * @return array
     */
    public function getRoutes()
    {
        $routes = app('router')->getRoutes();
        $prefix = 'api';
        $routes = collect($routes)->filter(function ($route) use ($prefix) {
            return Str::startsWith($route->uri, $prefix);
        })->map(function ($route) {
            return $this->getRouteInformation($route);
        })->all();
        if ($sort = request('_sort')) {
            $routes = $this->sortRoutes($sort, $routes);
        }
        $routes = collect($routes)->filter()->map(function ($route) {
            $route['parameters'] = json_encode($this->getRouteParameters($route['action']));
            unset($route['middleware'], $route['host'], $route['name'], $route['action']);

            return $route;
        })->toArray();

        return array_filter($routes);
    }

    /**
     * Get parameters info of route.
     */
    protected function getRouteParameters($action): array
    {
        if (is_callable($action) || $action === 'Closure') {
            return [];
        }
        if (is_string($action) && !Str::contains($action, '@')) {
            [$class, $method] = static::makeInvokable($action);
        } else {
            [$class, $method] = explode('@', $action);
        }
        $classReflector = new ReflectionClass($class);

        if ($comment = $classReflector->getMethod($method)->getDocComment()) {
            $parameters = [];
            preg_match_all('/\@SWG\\\Parameter\(\n(.*?)\)\n/s', $comment, $matches);
            foreach (array_get($matches, 1, []) as $item) {
                preg_match_all('/(\w+)=[\'"]?([^\r\n"]+)[\'"]?,?\n/s', $item, $match);
                if (count($match) == 3) {
                    $match[2] = array_map(function ($val) {
                        return trim($val, ',');
                    }, $match[2]);
                    $parameters[] = array_combine($match[1], $match[2]);
                }
            }

            return $parameters;
        }

        return [];
    }

    protected static function makeInvokable($action): array
    {
        if (!method_exists($action, '__invoke')) {
            throw new UnexpectedValueException("Invalid route action: [$action].");
        }

        return [$action, '__invoke'];
    }

    /**
     * Get the route information for a given route.
     *
     *
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        return [
            'host' => $route->domain(),
            'method' => $route->methods()[0],
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'middleware' => $this->getRouteMiddleware($route),
        ];
    }

    /**
     * Sort the routes by a given element.
     *
     * @param  string  $sort
     * @param  array  $routes
     * @return array
     */
    protected function sortRoutes($sort, $routes)
    {
        return Arr::sort($routes, function ($route) use ($sort) {
            return $route[$sort];
        });
    }

    /**
     * Get before filters.
     *
     * @param  Route  $route
     * @return string
     */
    protected function getRouteMiddleware($route)
    {
        return collect($route->gatherMiddleware())->map(function ($middleware) {
            return $middleware instanceof Closure ? 'Closure' : $middleware;
        });
    }
}
