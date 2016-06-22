<?php
/*******************             start        ****************************
 * $ php -S localhost:8000 ./README.php
 *
 * @author Evgeniy Blinov <evgeniy_blinov@mail.ru>
 * @require "michelf/php-markdown": "1.6.0"
 *******************             start        ****************************/
use \Michelf\MarkdownExtra;

defined('APP_ROOT_PATH') or define('APP_ROOT_PATH', dirname(__FILE__));
defined('APP_README_PATH') or define('APP_README_PATH', '/README.md');
require APP_ROOT_PATH . '/vendor/autoload.php';

function get_file_path($url)
{
    return APP_ROOT_PATH .
        (($url == '/') ? APP_README_PATH : $url);
}

function get_file($url)
{
    $path = get_file_path($url);
    $ext  = pathinfo($path, PATHINFO_EXTENSION);
    $pattern = '~^' .
        str_replace('/', '\/', APP_ROOT_PATH) .
        '\/(doc\/.+)?[A-Z0-9\.]+\.(' .
        implode('|', array('md', 'markdown', 'css', 'js')) .
        ')$~i';
    if (preg_match($pattern, $path, $matches) && file_exists($path)) {
        switch ($ext) {
            case 'md':
            case 'markdown':
                header('Content-Type: text/html');
                $result = MarkdownExtra::defaultTransform(file_get_contents($path));
                return "
                <html>
                <head>
                    <link rel='stylesheet' type='text/css' href='/doc/css/highlight/github.css'>
                </head>
                <body>
                <code class='markdown'>$result</code>

                <script src='/doc/js/highlight/highlight.pack.js'></script>
                <script>hljs.initHighlightingOnLoad();</script>
                </body>
                </html>
                ";
            default:
                header("Content-Type: text/$ext");
                die(file_get_contents($path));
        }
    }
    return get_file(APP_README_PATH);
}

echo get_file($_SERVER['REQUEST_URI']);
