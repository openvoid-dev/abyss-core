<?php

/**
 * This is a custom ErrorsHandler class to handle all of
 * the errors that can occur in your application.
 *
 * It is set in app.php when initializin your application.
 *
 * Add later:
 * - storing all errors to a custom log.txt files
 * - errors handler
 * - fix type definition for exception handler
 *
 **/

namespace Abyss\Core;

use Abyss\Controller\Controller;

final class ErrorsHandler
{
    /**
     * Show custom error page
     *
     * @param mixed $message
     * @param mixed $file
     * @param mixed $line
     * @param mixed $file_content
     * @return void
     */
    public static function show_error_page(
        $message,
        $file,
        $line,
        $file_content
    ): void {
        Controller::view(
            page: "error",
            props: [
                "message" => $message,
                "file" => $file,
                "line" => $line,
                "file_content" => $file_content,
            ]
        );
    }
    /**
     * Log all errors to the log file
     *
     * Add this later
     *
     * @return void
     */
    public static function log_error(): void
    {
    }

    /**
     * Handle all of php errors
     *
     * Currently doesn't work fix later
     *
     * @param mixed $num
     * @param mixed $str
     * @param mixed $file
     * @param mixed $line
     * @param mixed $context
     * @return void
     */
    public static function handle_error(
        $num,
        $str,
        $file,
        $line,
        $context = null
    ): void {
    }

    /**
     * Handle all of php exceptions
     *
     * There is a problem with setting a type, will fix later
     *
     * @param mixed $exception
     * @return void
     */
    public static function handle_exception($exception): void
    {
        // * Get file content in lines
        $filename = $exception->getFile();
        $lines = [];

        $file_stream = fopen(filename: $filename, mode: "r");

        if (!$file_stream) {
            return;
        }

        while (!feof(stream: $file_stream)) {
            $line = fgets(stream: $file_stream);

            $lines[] = $line;
        }

        fclose(stream: $file_stream);

        self::show_error_page(
            message: $exception->getMessage(),
            file: $exception->getFile(),
            line: $exception->getLine(),
            file_content: $lines
        );
    }

    /**
     * Check for fatal error and handle it
     *
     * @return void
     */
    public static function check_for_fatal_error(): void
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

    /**
     * Set and register all of php error
     * handlers to custom handlers
     *
     * @return void
     */
    public static function watch(): void
    {
        register_shutdown_function([self::class, "check_for_fatal_error"]);

        set_error_handler([self::class, "handle_error"]);
        set_exception_handler([self::class, "handle_exception"]);

        ini_set("display_errors", "off");

        error_reporting(E_ALL);
    }
}
