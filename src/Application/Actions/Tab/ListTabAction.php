<?php

namespace App\Application\Actions\Tab;

use App\Domain\DomainException\DomainDataFormatException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class ListTabAction extends TabAction
{
    /**
     * @OA\Get(
     *     path="/tabs",
     *     tags= {"Tabs"},
     *     summary="Requires Authentication",
     *     description="Search for all Tabs, if found return it",
     *     @OA\Parameter (
     *          name = "Auth-Token",
     *          in = "header",
     *          @OA\Schema (type = "string"),
     *          description = "Token for authentication",
     *          required = true,
     *     ),
     *     @OA\Parameter (
     *          name = "showDeleted",
     *          in = "query",
     *          @OA\Schema (type = "string"),
     *          description = "true or false, based on if you want to see the deleted Tabs",
     *          required = true,
     *     ),
     *     @OA\Parameter (
     *          name = "pageNumber",
     *          in = "query",
     *          @OA\Schema (type = "string"),
     *          description = "number of the page, default is 1",
     *          required = false,
     *     ),
     *     @OA\Parameter (
     *          name = "recordsPerPage",
     *          in = "query",
     *          @OA\Schema (type = "string"),
     *          description = "number of records presented on the page, default is 10",
     *          required = false,
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer"),
     *               @OA\Property (property="data", type="array",
     *                      @OA\Items(
     *                      @OA\Property (property="id", type="integer", example = 1),
     *                      @OA\Property (property="name", type="string", example = "Name"),
     *                      @OA\Property (property="ticketId", type="integer", example = 1),
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
     * @throws PermissionNoAuthorizationException
     * @throws PermissionAuthTokenException
     * @throws UserNotFoundException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $queryParams = $this->getQueryParams();
        $pageNumber = $queryParams['pageNumber'] ?? false;
        $recordsPerPage = $queryParams['recordsPerPage'] ?? false;
        $showDeleted = $queryParams['showDeleted'] ?? false;
        $operation[] = 'read';

        $tabValidator = $this->tabValidator;
        $tabRepo = $this->tabRepository;
        $tabPaginator = $this->tabPaginator;

        $showDeleted = $tabValidator->transformShowDeletedIntoBoolean($showDeleted);

        if ($showDeleted) {
            $operation[] = 'showDeleted';
        }

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, $operation);

        $tabs = $tabRepo->findAll($showDeleted);

        $paginatedTabs = $tabPaginator->getDataPaginated($pageNumber, $recordsPerPage, $tabs);

        $this->logger->info('Tab list was viewed.');
        return $this->respondWithData($paginatedTabs['data'], $paginatedTabs['hasNextPage']);
    }
}