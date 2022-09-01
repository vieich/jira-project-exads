<?php

namespace App\Application\Actions\Items;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class DeleteItemAction extends ItemAction
{
    /**
     * @OA\Delete (
     *     path="/items/{id}",
     *     tags= {"Items"},
     *     summary="Requires Authentication",
     *     description="Delete a Item, if success return it",
     *     @OA\Parameter (
     *          name = "Auth-Token",
     *          in = "header",
     *          @OA\Schema (type = "string"),
     *          description = "Token for authentication",
     *          required = true,
     *     ),
     *     @OA\Parameter (
     *          name = "id",
     *          in = "path",
     *          @OA\Schema (type = "integer"),
     *          description = "Id of the Item",
     *          required = true,
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="message", type="string", example = "Item with id 5 deleted"),
     *                      @OA\Property (property="hasSuccess", type="boolean", example = true)
     *                      )
     *              )
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 401),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "UNAUTHENTICATED"),
     *                      @OA\Property (property="description", type="string", example = "Log in to get an valid auth token.")
     *                      )
     *          )
     *     ),
     *      @OA\Response(
     *          response="403",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 403),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "INSUFFICIENT_PRIVILEGES"),
     *                      @OA\Property (property="description", type="string", example = "Auth-Token is missing on the header.")
     *                      )
     *          )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 404),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "RESOURCE_NOT_FOUND"),
     *                      @OA\Property (property="description", type="string", example = "The Item does not exist.")
     *                      )
     *          )
     *     )
     * )
     * @throws UserNotFoundException
     * @throws PermissionNoAuthorizationException
     * @throws PermissionAuthTokenException
     * @throws HttpBadRequestException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $itemId = (int) $this->resolveArg('id');
        $operation[] = 'delete';

        $itemRepo = $this->itemRepository;

        (new Permission($this->permissionRepo))->checkIfHasAccess($auth_token, $operation);

        $action = $itemRepo->deleteItem($itemId);

        $this->logger->info('Item with id ' . $itemId . ' deleted.');

        return $this->respondWithData($action);
    }
}
