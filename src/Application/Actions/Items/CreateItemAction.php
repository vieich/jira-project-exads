<?php

namespace App\Application\Actions\Items;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Item\Exception\ItemNameFormatException;
use App\Domain\Item\Exception\ItemSectionIdFormatException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Permission;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateItemAction extends ItemAction
{
    /**
     * @OA\Post(
     *     path="/items",
     *     tags={"Items"},
     *     summary="Require Authentication",
     *     description="Create a Item, if success return it",
     *      @OA\Parameter (
     *          name = "Auth-Token",
     *          in = "header",
     *          @OA\Schema (type = "string"),
     *          description = "Token for authentication",
     *          required = true,
     *     ),
     *     @OA\RequestBody (
     *          @OA\JsonContent(
     *               type = "object",
     *               @OA\Property (property="name", type="string", example = "name"),
     *               @OA\Property (property="sectionId", type="integer", example = 1)
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="id", type="integer", example = 1),
     *                      @OA\Property (property="name", type="string", example = "name"),
     *                      @OA\Property (property="status", type="string", example = "to do"),
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
     *                      @OA\Property (property="description", type="string", example = "Payload is not valid, is missing the name field.")
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
     *     )
     * )
     * @throws ItemSectionIdFormatException
     * @throws UserNotFoundException
     * @throws PermissionAuthTokenException
     * @throws ItemNameFormatException
     * @throws DomainPayloadStructureValidatorException|PermissionNoAuthorizationException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $name = $data['name'] ?? null;
        $sectionId = $data['sectionId'] ?? null;
        $operation[] = 'create';

        $args = compact('name', 'sectionId');

        $itemValidator = $this->itemValidator;
        $itemRepo = $this->itemRepository;

        (new Permission($this->permissionRepo))->checkIfHasAccess($auth_token, $operation);

        $itemValidator->checkIfPayloadStructureIsValid($args);
        $itemValidator->checkIfItemNameIsValid($name);
        $itemValidator->checkIfSectionIdIsValid($sectionId);

        $item = $itemRepo->createItem($name, $sectionId);

        $this->logger->info('Item with id ' . $item->getId() . ' created.');

        return $this->respondWithData($item);
    }
}