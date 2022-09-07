<?php

namespace App\Application\Actions\Section;

use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class ListSectionAction extends SectionAction
{
    /**
     * @OA\Get(
     *     path="/sections",
     *     tags= {"Sections"},
     *     summary="Requires Authentication",
     *     description="Search for all Sections, if found return it",
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
     *          description = "true or false, based on if you want to see the deleted Section",
     *          required = true,
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="array",
     *                      @OA\Items(
     *                      @OA\Property (property="id", type="integer", example = 1),
     *                      @OA\Property (property="name", type="string", example = "SectionName"),
     *                      @OA\Property (property="tabId", type="integer", example = 1),
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
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();
        $queryParams = $this->getQueryParams();
        $pageNumber = $queryParams['pageNumber'] ?? false;
        $recordsPerPage = $queryParams['recordsPerPage'] ?? false;
        $showDeleted = $queryParams['showDeleted'] ?? false;
        $operation[] = 'read';

        $sectionRepo = $this->sectionRepository;
        $sectionValidator = $this->sectionValidator;
        $sectionPaginator = $this->sectionPaginator;

        if ($showDeleted) {
            $showDeleted = $sectionValidator->transformShowDeletedIntoBoolean($showDeleted);
            $operation[] = 'showDeleted';
        }

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, $operation);

        $sections = $sectionRepo->findAll($showDeleted);

        $paginatedSections = $sectionPaginator->getDataPaginated($pageNumber, $recordsPerPage, $sections);

        $this->logger->info('Sections list viewed.');
        return $this->respondWithData($paginatedSections['data'], $paginatedSections['hasNextPage']);
    }
}
