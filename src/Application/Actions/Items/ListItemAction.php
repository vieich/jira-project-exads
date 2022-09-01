<?php

namespace App\Application\Actions\Items;

use App\Domain\DomainException\DomainDataFormatException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNoAuthorizationException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class ListItemAction extends ItemAction
{

    /**
     * @OA\Get(
     *     path="/items",
     *     tags= {"Items"},
     *     summary="Requires Authentication",
     *     description="Search for all Items, if found return it",
     *     @OA\Parameter (
     *          name = "Auth-Token",
     *          in = "header",
     *          @OA\Schema (type = "string"),
     *          description = "Token for authentication",
     *          required = true,
     *     ),
     *     @OA\RequestBody (
     *          @OA\JsonContent(
     *               type = "object",
     *               @OA\Property (property="showDeleted", type="boolean", example = true)          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="array",
     *                      @OA\Items(
     *                      @OA\Property (property="id", type="integer", example = 1),
     *                      @OA\Property (property="name", type="string", example = "itemName"),
     *                      @OA\Property (property="status", type="string", example = "to do"),
     *                      @OA\Property (property="sectionId", type="integer", example = 1),
     *                      @OA\Property (property="isActive", type="boolean", example = true)
     *                          )
     *                  )
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
     *     )
     * )
     * @throws UserNotFoundException
     * @throws PermissionNoAuthorizationException
     * @throws PermissionAuthTokenException
     * @throws DomainDataFormatException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $data = $this->getFormData();
        $showDeleted = $data['showDeleted'] ?? false;
        $operation[] = 'read';

        $itemRepository = $this->itemRepository;

        if ($showDeleted) {
            $this->itemValidator->checkIfShowDeletedIsValid($showDeleted);
            $operation[] = 'showDeleted';
        }

        (new Permission($this->permissionRepo))->checkIfHasAccess($auth_token, $operation);

        $items = $itemRepository->findAll($showDeleted);

        $this->logger->info('List of items viewed.');

        return $this->respondWithData($items);
    }
}
