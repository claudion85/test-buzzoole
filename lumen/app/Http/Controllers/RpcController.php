<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Repositories\PharmacyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class RpcController extends Controller
{
    public function __construct(private readonly PharmacyRepository $pharmacyRepository)
    {
    }

    /**
     * @return JsonResponse $response
     */
    public function handle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'jsonrpc' => 'required|in:2.0',
            'method' => ['required', 'string', Rule::in(['searchNearestPharmacy'])],
            'params' => 'required|array',
            'id' => 'required',
            'params.range' => 'sometimes|nullable|numeric|min:0',
            'params.limit' => 'sometimes|nullable|numeric|min:1',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return response()->json([
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32600,
                    'message' => 'Invalid Request',
                    'data' => $errors,
                ],
                'id' => null,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var string $method */
        $method = $request['method'];
        $params = $request['params'];

        $response['jsonrpc'] = '2.0';
        $response['id'] = $request['id'];

        try {
            $code = Response::HTTP_OK;
            $response['result'] = $this->pharmacyRepository->$method($params, $method);

        } catch (ApiException $e) {
            $code = $e->getCode();
            $response['error'] = ['code' => -32601, 'message' => sprintf('API communication error: %s', $e->getMessage())];
        }

        return response()->json($response, $code);
    }
}
