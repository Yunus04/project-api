<?php

namespace App\Repositories;

use App\Models\User;

/**
 * AuthRepositoryInterface
 * 
 * This interface defines the methods for user authentication and token management.
 * It serves as a contract for any class implementing user authentication logic.
 */
interface AuthRepositoryInterface
{
    /**
     * Create a new user.
     * 
     * @param array $data An associative array containing user information (name, email, password).
     * @return User The newly created User instance.
     */
    public function createAuth(array $data): User;

    /**
     * Generate a JWT token for the authenticated user.
     * 
     * @param User $auth The authenticated User instance.
     * @return string The generated JWT token as a string.
     */
    public function generateToken(User $auth): string;

    /**
     * Authenticate a user using their credentials.
     * 
     * @param array $credentials An associative array containing 'email' and 'password'.
     * @return mixed The token if authentication is successful, otherwise false.
     */
    public function authenticate(array $credentials);

    /**
     * Invalidate the current JWT token to log out the user.
     * 
     * @return void
     */
    public function logout(): void;
}
