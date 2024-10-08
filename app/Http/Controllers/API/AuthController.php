<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Repositories\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;


/**
 * @OA\Info(
 *      title="API Documentation",
 *      version="1.0"),
 * 
 * AuthController
 * 
 * This controller handles authentication actions such as registration, login, and logout.
 * It uses the AuthRepositoryInterface for repository-based authentication logic.
 */
class AuthController extends Controller
{
    protected $authRepository;

    /**
     * AuthController constructor.
     * 
     * @param AuthRepositoryInterface $authRepository The repository implementation for authentication.
     */
    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="Creates a new user and generates a JWT token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="statuscode", type="integer", example=201),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Registration successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="statuscode", type="integer", example=400),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $auth = $this->authRepository->createAuth($request->all());
        $token = $this->authRepository->generateToken($auth);

        return (new AuthResource($auth, $token))->additional([
            'statuscode' => 201,
            'message' => 'Registration successful'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="Login a user",
     *     description="Authenticate user and return a JWT token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="statuscode", type="integer", example=200),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Login successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="statuscode", type="integer", example=401),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Could not create token",
     *         @OA\JsonContent(
     *             @OA\Property(property="statuscode", type="integer", example=500),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Could not create token")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = $this->authRepository->authenticate($credentials)) {
                return response()->json([
                    'statuscode' => 401,
                    'data' => null,
                    'message' => 'Invalid credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'statuscode' => 500,
                'data' => null,
                'message' => 'Could not create token'
            ], 500);
        }

        return (new AuthResource(Auth::user(), $token))->additional([
            'statuscode' => 200,
            'message' => 'Login successful'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="Logout the authenticated user",
     *     description="Invalidate the JWT token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="jwt.token.here"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="statuscode", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="statuscode", type="integer", example=500),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Failed to logout, please try again")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        try {
            $this->authRepository->logout();
            return response()->json([
                'statuscode' => 200,
                'message' => 'Successfully logged out'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'statuscode' => 500,
                'data' => null,
                'message' => 'Failed to logout, please try again'
            ], 500);
        }
    }
}