<?php

// /**
//  * Error handler, passes flow over the exception logger with new ErrorException.
//  */
// function log_error($num, $str, $file, $line, $context = null)
// {
//     log_exception(new ErrorException($str, 0, $num, $file, $line));
// }

// /**
//  * Uncaught exception handler.
//  */
// function log_exception($e)
// {
//     if (true) {
//         print "<div style='text-align: center;'>";
//         print "<h2 style='color: rgb(190, 50, 50);'>Exception Occured:</h2>";
//         print "<table style='width: 800px; display: inline-block;'>";
//         print "<tr style='background-color:rgb(230,230,230);'><th style='width: 80px;'>Type</th><td>" .
//             get_class($e) .
//             "</td></tr>";
//         print "<tr style='background-color:rgb(240,240,240);'><th>Message</th><td>{$e->getMessage()}</td></tr>";
//         print "<tr style='background-color:rgb(230,230,230);'><th>File</th><td>{$e->getFile()}</td></tr>";
//         print "<tr style='background-color:rgb(240,240,240);'><th>Line</th><td>{$e->getLine()}</td></tr>";
//         print "</table></div>";
//     } else {
//         $message =
//             "Type: " .
//             get_class($e) .
//             "; Message: {$e->getMessage()}; File: {$e->getFile()}; Line: {$e->getLine()};";
//         file_put_contents(
//             $config["app_dir"] . "/tmp/logs/exceptions.log",
//             $message . PHP_EOL,
//             FILE_APPEND
//         );
//         header("Location: {$config["error_page"]}");
//     }

//     exit();
// }

// /**
//  * Checks for a fatal error, work around for set_error_handler not working on fatal errors.
//  */
// function check_for_fatal()
// {
//     $error = error_get_last();

//     if ($error === null) {
//         return;
//     }

//     if ($error["type"] == E_ERROR) {
//         log_error(
//             $error["type"],
//             $error["message"],
//             $error["file"],
//             $error["line"]
//         );
//     }
// }

// register_shutdown_function("check_for_fatal");
// set_error_handler("log_error");
// set_exception_handler("log_exception");
// ini_set("display_errors", "off");
// error_reporting(E_ALL);

namespace Abyss\Core;

use Abyss\Controller\Controller;

final class ErrorsHandler
{
    public static function show_error_page($message, $file, $line)
    {
        Controller::view(
            page: "error",
            props: [
                "message" => $message,
                "file" => $file,
                "line" => $line,
            ]
        );
    }

    public static function log_error()
    {
    }

    public static function handle_error(
        $num,
        $str,
        $file,
        $line,
        $context = null
    ) {
        exit();
    }

    public static function handle_exception($exception)
    {
        var_dump($exception);
        self::show_error_page();

        exit();
    }

    public static function check_for_fatal_error()
    {
        $error = error_get_last();

        if ($error === null) {
            return;
        }

        if ($error["type"] == E_ERROR) {
            self::handle_error(
                $error["type"],
                $error["message"],
                $error["file"],
                $error["line"]
            );
        }
    }

    public static function watch()
    {
        register_shutdown_function([self::class, "check_for_fatal_error"]);
        set_error_handler([self::class, "handle_error"]);
        set_exception_handler([self::class, "handle_exception"]);
        ini_set("display_errors", "off");
        error_reporting(E_ALL);
    }
}
