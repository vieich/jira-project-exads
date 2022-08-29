<?php

namespace App\Application\Actions\Section;

use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\Permission\Exception\PermissionAuthTokenException;
use App\Domain\Permission\Exception\PermissionNoAuthorizationException;
use App\Domain\Permission\Permission;
use App\Domain\Section\Exception\SectionNameFormatException;
use App\Domain\Section\Exception\SectionTabIdFormatException;
use App\Domain\User\Exception\UserNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class CreateSectionAction extends SectionAction
{

    /**
     * @OA\Post(
     *     path="/sections",
     *     tags={"Sections"},
     *     summary="Require Authentication",
     *     description="Create a section, if success return it",
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
     *               @OA\Property (property="sectionName", type="string", example = "Section"),
     *               @OA\Property (property="tabId", type="integer", example = 1)
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(type = "object",
     *               @OA\Property (property="statusCode", type="integer", example = 200),
     *               @OA\Property (property="data", type="object",
     *                      @OA\Property (property="id", type="integer", example = 1),
     *                      @OA\Property (property="sectionName", type="string", example = "SectionName"),
     *                      @OA\Property (property="tabId", type="integer", example = 1),
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
     *                      @OA\Property (property="description", type="string", example = "Payload is not valid, is missing the sectionName field.")
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
     * @throws UserNotFoundException
     * @throws SectionTabIdFormatException
     * @throws PermissionAuthTokenException
     * @throws SectionNameFormatException
     * @throws PermissionNoAuthorizationException
     * @throws DomainPayloadStructureValidatorException
     */
    protected function action(): Response
    {
        $auth_token = $this->getAuthTokenHeader();

        $data = $this->getFormData();
        $sectionName = $data['sectionName'] ?? null;
        $tabId = $data['tabId'] ?? null;
        $operation[] = 'read';

        $args = compact('sectionName', 'tabId');

        $sectionValidator = $this->sectionValidator;
        $sectionRepo = $this->sectionRepository;

        (new Permission($this->permissionRepository))->checkIfHasAccess($auth_token, $operation);

        $sectionValidator->checkIfPayloadStructureIsValid($args);
        $sectionValidator->checkIfSectionNameIsValid($sectionName);
        $sectionValidator->checkIfTabIdIsValid($tabId);

        $section = $sectionRepo->createSection($sectionName, $tabId);

        $this->logger->info('Section with id ' . $section->getId() . ' created.');

        return $this->respondWithData($section);
    }
}