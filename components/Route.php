<?php
namespace Components;

use Exception;
use Constants\StatusCodes;
use Helpers\RequestHelper;

class Route
{
    const SUPPORTED_METHODS = ['GET', 'POST', 'PUT', 'DELETE'];

    private static $_ROUTES = [];

    private static function register($method, $url, $controller, $custom_handler=null)
    {
        if (! in_array($method, self::SUPPORTED_METHODS)) {
            throw new Exception("Route::$method, `$method` isn't supported.");
        }

        self::$_ROUTES[$url][$method]['controller'] = $controller;
        self::$_ROUTES[$url][$method]['custom_handler'] = $custom_handler;
    }

    /**
     * @param array $url_parts
     * @return array
     */
    private static function mapUrlWithParams(array $url_parts): array
    {
        /*
         * mapping target route (as Url) & detecting its route-params with passed url.
         * if mapping failed, an empty array will be the result.
        array structure like
            [
                'url' => "",
                'custom_handler' => "",
                'params' => []
            ]
        array can be empty if not route is found.
         */
        foreach (array_keys(self::$_ROUTES) as $route)
        {
            $registered_url_parts = explode("/", $route);
            $params = [];
            $break_loop = false;

            if (sizeof($registered_url_parts) != sizeof($url_parts))
            {
                continue;
            }

            foreach ($registered_url_parts as $index => $registered_url_part)
            {
                // handle {.whatever} as param.
                if (preg_match("/^{.*}$/", $registered_url_part))
                {
                    $params[] = $url_parts[$index];
                }
                elseif ($registered_url_part != $url_parts[$index])
                {
                    // reset params
                    $params = [];
                    $break_loop = true;
                    break;
                }
            }

            if (! $break_loop)
            {
                return [
                    'url' => $route,
                    'params' => $params
                ];
            }
        }

        return [];
    }

    public static function GET($url, $controller, $custom_handler=null)
    {
        self::register('GET', $url, $controller, $custom_handler);
    }

    public static function POST($url, $controller, $custom_handler=null)
    {
        self::register('POST', $url, $controller, $custom_handler);
    }

    public static function DELETE($url, $controller, $custom_handler=null) : void
    {
        self::register('DELETE', $url, $controller, $custom_handler);
    }

    public static function PUT($url, $controller, $custom_handler=null) : void
    {
        self::register('PUT', $url, $controller, $custom_handler);
    }

    public static function handleRequest()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $request_url_parts =
            RequestHelper::uriPathToArray(
                RequestHelper::getUriWithoutQueryParams($uri)
            );
        $request_method = $_SERVER['REQUEST_METHOD'];

        $url_params = self::mapUrlWithParams($request_url_parts);

        $url = $url_params['url'] ?? "";
        $params = $url_params['params'] ?? "";

        if (! $url_params || ! key_exists($url, self::$_ROUTES))
        {
            $response = [
                'message' => "not found.",
                'status_code' => StatusCodes::NOT_FOUND
            ];
        }
        elseif(! key_exists($request_method, self::$_ROUTES[$url]))
        {
            $response = [
                'message' => "$request_method request method isn't supported for this url.",
                'status_code' => StatusCodes::METHOD_NOT_ALLOWED
            ];
        }
        else
        {
            $controller = self::$_ROUTES[$url][$request_method]['controller'];
            $handler = self::$_ROUTES[$url][$request_method]['custom_handler'] ?: $request_method;
            $response = (new $controller())->$handler(... $params);
        }

        return $response;
    }
}