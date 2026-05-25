<?php
// Lightweight router.
class Router {
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void {
        $this->routes[] = [strtoupper($method), $pattern, $handler];
    }
    public function get(string $p, callable $h): void  { $this->add('GET',  $p, $h); }
    public function post(string $p, callable $h): void { $this->add('POST', $p, $h); }

    public function dispatch(string $method, string $uri): void {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = '/' . trim($path, '/');
        if ($path === '/') $path = '/';

        foreach ($this->routes as [$m, $pat, $h]) {
            if ($m !== strtoupper($method)) continue;
            $regex = '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pat) . '$#';
            if (preg_match($regex, $path, $matches)) {
                $args = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $h($args);
                return;
            }
        }

        http_response_code(404);
        view('errors/404', ['path' => $path]);
    }
}
