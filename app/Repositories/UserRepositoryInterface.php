<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function update(int $id, array $data): User;
    public function delete(int $id): void;
}
