<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * AuthResource
 * 
 * This resource transforms the user data and includes the JWT token when available.
 */
class AuthResource extends JsonResource
{
    protected $token;

    /**
     * AuthResource constructor.
     * 
     * @param mixed $resource The resource instance (user data).
     * @param string|null $token The JWT token, if available.
     */
    public function __construct($resource, $token = null)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    /**
     * @OA\Schema(
     *     schema="AuthResource",
     *     type="object",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="John Doe"),
     *     @OA\Property(property="email", type="string", example="user@example.com"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-08T00:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-08T00:00:00Z"),
     *     @OA\Property(property="token", type="string", example="jwt.token.here")
     * )
     */

    /**
     * Transform the resource into an array.
     * 
     * @param \Illuminate\Http\Request $request The request instance.
     * @return array The transformed user data with token.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'token' => $this->token,
        ];
    }
}
