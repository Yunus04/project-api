<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * UserResource
 * 
 * This resource transforms the user data for API responses.
 * 
 * @OA\Schema(
 *     schema="UserResource",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-08T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-08T12:00:00Z")
 * )
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * 
     * @param \Illuminate\Http\Request $request The request instance.
     * @return array The transformed user data.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
