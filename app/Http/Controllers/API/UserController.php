<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Helpers\ApiResponseHelper;

/**
 * @OA\SecurityScheme(
 *     securityScheme="BearerToken",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * UserController
 */
class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     summary="Get a user by ID",
     *     security={{"BearerToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, token not found or invalid"
     *     )
     * )
     */
    public function show($id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return response()->json([
                'statuscode' => 404,
                'data' => null,
                'message' => 'User not found',
            ], 404);
        }

        return (new UserResource($user))->additional([
            'statuscode' => 200,
            'message' => 'User retrieved successfully',
        ])->response()->setStatusCode(200);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     summary="Update a user's information",
     *     security={{"BearerToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="jwt.token.here"),
     *             ref="#/components/schemas/UserRequest"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, token not found or invalid"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to update user"
     *     )
     * )
     */
    public function update(UserRequest $request, $id): JsonResponse
    {
        try {
            $user = $this->userRepository->update($id, $request->all());

            return (new UserResource($user))->additional([
                'statuscode' => 200,
                'message' => 'User updated successfully'
            ])->response()->setStatusCode(200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'statuscode' => 404,
                'message' => 'User not found',
                'data' => null
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'statuscode' => 500,
                'message' => 'Failed to update user',
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"User"},
     *     summary="Delete a user by ID",
     *     security={{"BearerToken": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, token not found or invalid"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete user"
     *     )
     * )
     */
    public function destroy($id, UserRequest $request): JsonResponse
    {
        try {
            $this->userRepository->delete($id);
            return response()->json([
                'statuscode' => 200,
                'message' => 'User deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'statuscode' => 404,
                'message' => 'User not found',
                'data' => null
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'statuscode' => 500,
                'message' => 'Failed to delete user',
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/search/name",
     *     tags={"User"},
     *     summary="Search user by name",
     *     security={{"BearerToken": {}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Name to search for",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statuscode", type="integer", example=200),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string", example="User found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statuscode", type="integer", example=404),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function searchByName(Request $request)
    {
        $request->validate(['name' => 'required|string']);

        $name = $request->query('name');
        $dataCollection = $this->fetchDataFromApi();

        if ($dataCollection) {
            $filteredData = $this->filterData($dataCollection, 'name', $name);

            \Log::info('Filtered Data:', $filteredData->values()->toArray());

            if ($filteredData->isNotEmpty()) {
                return ApiResponseHelper::apiResponse(200, $filteredData->values(), 'User found');
            }

            return ApiResponseHelper::apiResponse(404, null, 'User not found');
        }

        return ApiResponseHelper::apiResponse(500, null, 'Failed to retrieve data');
    }

    /**
     * @OA\Get(
     *     path="/api/search/nim",
     *     tags={"User"},
     *     summary="Search user by NIM",
     *     security={{"BearerToken": {}}},
     *     @OA\Parameter(
     *         name="nim",
     *         in="query",
     *         description="NIM to search for",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statuscode", type="integer", example=200),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string", example="User found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statuscode", type="integer", example=404),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function searchByNim(Request $request)
    {
        $request->validate(['nim' => 'required|string']);
        $nim = $request->query('nim');
        $dataCollection = $this->fetchDataFromApi();

        if ($dataCollection) {
            $filteredData = $this->filterData($dataCollection, 'NIM', $nim);

            \Log::info('Filtered Data:', $filteredData->values()->toArray());

            if ($filteredData->isNotEmpty()) {
                return ApiResponseHelper::apiResponse(200, $filteredData->values(), 'User found');
            }

            return ApiResponseHelper::apiResponse(404, null, 'User not found');
        }

        return ApiResponseHelper::apiResponse(500, null, 'Failed to retrieve data');
    }

    /**
     * @OA\Get(
     *     path="/api/search/ymd",
     *     tags={"User"},
     *     summary="Search user by YMD",
     *     security={{"BearerToken": {}}},
     *     @OA\Parameter(
     *         name="ymd",
     *         in="query",
     *         description="YMD to search for",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statuscode", type="integer", example=200),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string", example="User found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="statuscode", type="integer", example=404),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function searchByYmd(Request $request)
    {
        $request->validate(['ymd' => 'required|string']);
        $ymd = $request->query('ymd');
        $dataCollection = $this->fetchDataFromApi();

        if ($dataCollection) {
            $filteredData = $this->filterData($dataCollection, 'YMD', $ymd);

            \Log::info('Filtered Data:', $filteredData->values()->toArray());

            if ($filteredData->isNotEmpty()) {
                return ApiResponseHelper::apiResponse(200, $filteredData->values(), 'User found');
            }

            return ApiResponseHelper::apiResponse(404, null, 'User not found');
        }

        return ApiResponseHelper::apiResponse(500, null, 'Failed to retrieve data');
    }

    protected function fetchDataFromApi()
    {
        $response = Http::get('https://bit.ly/48ejMhW');

        if ($response->successful()) {
            $data = collect($response->json());
            $dataString = $data->get('DATA');

            $rows = explode("\n", $dataString);
            array_shift($rows);

            return collect($rows)->map(function ($row) {
                $columns = explode('|', $row);
                if (count($columns) === 3) {
                    return [
                        'name' => trim($columns[0]),
                        'YMD' => trim($columns[1]),
                        'NIM' => trim($columns[2]),
                    ];
                }
                return null;
            })->filter();
        }

        return null;
    }

    protected function filterData($dataCollection, $key, $value)
    {
        return $dataCollection->filter(function ($item) use ($key, $value) {
            return isset($item[$key]) && stripos($item[$key], $value) !== false;
        });
    }

}