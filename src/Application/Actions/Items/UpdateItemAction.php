<?php

namespace App\Application\Actions\Items;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Item\Exception\ItemNameFormatException;
use App\Domain\Item\Exception\ItemStatusException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class UpdateItemAction extends ItemAction
{
    /**
     * @OA\Patch(
     *     path="/items/{id}",
     *     tags= {"Items"},
     *     summary="Requires Authentication",
     *     description="Update a item, if success return it",
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
     *      ),
     *     @OA\RequestBody (
     *          @OA\JsonContent(
     *               type = "object",
     *               @OA\Property (property="name", type="string"),
     *               @OA\Property (property="status", type="string"),
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="id", type="integer", example = 1),
     *                      @OA\Property (property="name", type="string", example = "itemName"),
     *                      @OA\Property (property="status", type="string", example = "status"),
     *                      @OA\Property (property="sectionId", type="integer", example = 1),
     *                      @OA\Property (property="isActive", type="boolean", example = true)
     *                      )
     *              )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 400),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "BAD_REQUEST"),
     *                      @OA\Property (property="description", type="string", example = "Payload is not valid, is missing the status field.")
     *                      )
     *          )
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
     *      @OA\Response(
     *          response="404",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 404),
     *               @OA\Property (property="error", type="object",
     *                      @OA\Property (property="type", type="string", example = "RESOURCE_NOT_FOUND"),
     *                      @OA\Property (property="description", type="string", example = "The Item does not exist.")
     *                      )
     *              )
     *          )
     * )
     * @throws ItemStatusException
     * @throws UserNotFoundException
     * @throws PermissionAuthTokenException
     * @throws PermissionNoAuthorizationException
     * @throws ItemNameFormatException
     * @throws HttpBadRequestException
     * @throws DomainPayloadStructureValidatorException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $itemId = (int) $this->resolveArg('id');

        $data = $this->getFormData();
        $name = $data['name'] ?? null;
        $status = $data['status'] ?? null;
        $operation[] = 'update';

        $permissionRepo = $this->permissionRepo;
        $itemValidator = $this->itemValidator;
        $itemRepo = $this->itemRepository;

        (new Permission($permissionRepo))->checkIfHasAccess($auth_token, $operation);

        $statusId = null;

        if (!$name && !$status) {
            $args = compact('name', 'status');
            $itemValidator->checkIfPayloadStructureIsValid($args);
        }

        if ($name) {
            $itemValidator->checkIfItemNameIsValid($name);
        }

        if ($status) {
            $statusId = $permissionRepo->checkIfItemStatusIsValidAndReturnId($status);
        }

        $item = $itemRepo->updateItem($itemId, $name, $statusId);

        $this->logger->info(' Item with id ' . $itemId . ' was updated.');

        return $this->respondWithData($item);
    }
}
