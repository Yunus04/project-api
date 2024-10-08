<?php
namespace App\Repositories;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * AuthRepository
 * 
 * This class implements the AuthRepositoryInterface and provides
 * methods for handling user authentication and token generation using JWT.
 */
class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Create a new user.
     * 
     * @param array $data An associative array containing user information (name, email, password).
     * @return User The newly created User instance.
     */
    public function createAuth(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Generate a JWT token for the authenticated user.
     * 
     * @param User $auth The authenticated User instance.
     * @return string The generated JWT token.
     */
    public function generateToken(User $auth): string
    {
        return JWTAuth::fromUser($auth);
    }

    /**
     * Authenticate a user using their credentials.
     * 
     * @param array $credentials An associative array containing 'email' and 'password'.
     * @return mixed Returns the token if authentication is successful, otherwise false.
     */
    public function authenticate(array $credentials)
    {
        return JWTAuth::attempt($credentials);
    }

    /**
     * Invalidate the current JWT token to log out the user.
     * 
     * @return void
     */
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
