<?php

/**
 * Simple AUTH system to get you started using JWT
 *
 * It uses UserModel and its migration configuration
 *
 * It includes logging with email and password,
 * forgoten password and password reset.
 *
 */

namespace Abyss\Nebula;

use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

final class Nebula
{
    /**
     * User instance
     *
     * @var array|bool
     **/
    private static $user = null;

    /**
     * JWT secret set in Nebula config
     *
     * @var string
     **/
    private static $jwt_secret;

    /**
     * Payload that is set in jwt token
     *
     * @var array
     **/
    private static $payload;

    /**
     * Configure Nebula
     *
     * @param array $nebula_config
     * @return void
     */
    public static function configure(array $nebula_config): void
    {
        self::$jwt_secret = $nebula_config["jwt_secret"];
    }

    /**
     * Get user with remember token
     *
     * @return array|bool
     **/
    public static function get_user(): array|bool
    {
        if (!empty(self::$user)) {
            return self::$user;
        }

        // * Get remember token
        if (!isset($_COOKIE["remember_token"])) {
            return false;
        }

        // * Get user email from remember_token cookie
        $remember_token = $_COOKIE["remember_token"];
        $user_data = self::_authenticate_jwt_token($remember_token);

        if (!$user_data) {
            return false;
        }

        // * Get user from db
        $user = UserModel::query()
            ->where("email", $user_data->user_email)
            ->find();

        self::$user = $user;

        return $user;
    }

    /**
     * Login user
     *
     * @param string $user_email
     * @param string $user_password
     * @return bool
     **/
    public static function login_user(
        string $user_email,
        string $user_password
    ): bool {
        // * Verify user exists
        $user = UserModel::query()
            ->where("email", $user_email)
            ->show_hidden()
            ->find();

        if (empty($user)) {
            // * Add some custom error, do later
            return false;
        }

        // * Verify password
        $is_password_verified = password_verify(
            $user_password,
            $user["password"]
        );

        if (!$is_password_verified) {
            return false;
        }

        // * Set payload
        self::$payload = [
            "user_email" => $user["email"],
            "website" => "http://localhost:8383",
        ];

        // * Generate new jwt token
        $jwt_token = self::_generate_jwt_token();

        // Set session expiration to 60 days (60 * 60 * 24 * 60 seconds)
        $session_expiration = 60 * 60 * 24 * 60;
        ini_set("session.gc_maxlifetime", $session_expiration);

        // Set cookie expiration to 60 days
        session_set_cookie_params($session_expiration);

        session_start();

        // * Set remember token
        setcookie("remember_token", $jwt_token, time() + 60 * 60 * 24 * 60);

        return true;
    }

    /**
     * Logout user from the session
     *
     * @return void
     **/
    public static function logout_user(): void
    {
        session_start();
        session_destroy();

        // * Clear remember token cookie
        setcookie("remember_token", "", time() - 3600); // * Expire cookie immediately

        self::$user = null;
    }

    /**
     * Hash user password
     *
     * @param string $password
     * @return string
     */
    public static function hash_password(string $password): string
    {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        return $hashed_password;
    }

    /**
     * Generate new JWT token
     *
     * @return string
     **/
    private static function _generate_jwt_token(): string
    {
        $jwt_token = JWT::encode(self::$payload, self::$jwt_secret, "HS256");

        return $jwt_token;
    }

    /**
     * Authenticate JWT token
     *
     * @param string $token
     * @return stdClass
     */
    private static function _authenticate_jwt_token(string $token): stdClass
    {
        $decoded = JWT::decode($token, new Key(self::$jwt_secret, "HS256"));

        return $decoded;
    }
}
