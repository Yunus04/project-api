<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        try {
            return User::findOrFail($id); // Will throw an exception if not found
        } catch (ModelNotFoundException $e) {
            // Handle the case when the user is not found
            return null; // or throw a custom exception
        } catch (Exception $e) {
            // Handle any other exceptions
            // You might want to log this exception and return null
            return null;
        }
    }

    /**
     * Update a user by ID with the provided data.
     *
     * @param int $id
     * @param array $data
     * @return User
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): User
    {
        try {
            $user = User::findOrFail($id);
            $user->update($data);
            return $user;
        } catch (ModelNotFoundException $e) {
            // Handle the case when the user is not found
            throw new ModelNotFoundException('User not found.'); // Or handle it as per your needs
        } catch (Exception $e) {
            // Handle any other exceptions (e.g., validation errors)
            // You might want to log this exception
            throw new Exception('An error occurred while updating the user.');
        }
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * @return void
     * @throws ModelNotFoundException
     */
    public function delete(int $id): void
    {
        try {
            $deleted = User::destroy($id);
            if ($deleted === 0) {
                throw new ModelNotFoundException('User not found.'); // Handle the case when no user was deleted
            }
        } catch (ModelNotFoundException $e) {
            // Handle the case when the user is not found
            throw new ModelNotFoundException('User not found.'); // Or handle it as per your needs
        } catch (Exception $e) {
            // Handle any other exceptions
            // You might want to log this exception
            throw new Exception('An error occurred while deleting the user.');
        }
    }
}
