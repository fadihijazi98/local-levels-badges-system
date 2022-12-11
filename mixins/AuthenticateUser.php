<?php
namespace Mixins;


use CustomExceptions\AuthenticationException;
use Models\User;

/**
 * only compatible with Controllers.
 */
trait AuthenticateUser
{
    /**
     * @var User $authenticatedUser
     */
    protected User $authenticatedUser;

    /**
     * @var array $handlerSkipAuthenticat
     */
    protected array $handlerSkipAuthentication = [];

    /**
     * @throws AuthenticationException if there's no authenticated user.
     */
    public function authenticateUser(): void
    {
        if(! isset($_SERVER['PHP_AUTH_USER']) && ! isset($_SERVER['PHP_AUTH_PW']))
        {
            throw new AuthenticationException("use basic authentication with your credentials please.");
        }

        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        /**
         * @var User $user
         */
        $user =
            User::query()
                ->where('username', $username)
                ->first();

        if(! $user || $user->password != md5($password))
        {
            throw new AuthenticationException("incorrect credentials.");
        }

        $this->authenticatedUser = $user;
    }

    public function __call(string $name, array $arguments)
    {
        $handler = $this->handlerMap[$name] ?? $name;

        if (! in_array($handler, $this->handlerSkipAuthentication))
        {
            // if sub Controller use AuthenticationMixin, then we need to authenticate the user
            $this->authenticateUser();
        }

        return parent::__call($name, $arguments);
    }
}