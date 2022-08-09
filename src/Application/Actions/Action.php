<?php
declare(strict_types=1);

namespace App\Application\Actions;

use App\Domain\DomainException\DomainOperationException;
use App\Domain\DomainException\DomainPayloadDataValidatorException;
use App\Domain\DomainException\DomainPayloadStructureValidatorException;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\DomainException\DomainRecordWithoutAuthorizationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="My First API", version="0.1"),
 * @OA\Server(url="https://sandbox.exads.rocks/")
 */
abstract class Action
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;
    /**
     * @var array
     */
    protected $args;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        try {
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        } catch (DomainRecordWithoutAuthorizationException $e) {
            throw new HttpForbiddenException($this->request, $e->getMessage());
        } catch (DomainPayloadStructureValidatorException $e) {
            throw new HttpBadRequestException($this->request, $e->getMessage());
        } catch (DomainPayloadDataValidatorException $e) {
            throw new HttpBadRequestException($this->request, $e->getMessage());
        } catch (DomainOperationException $e) {
            throw new HttpInternalServerErrorException($this->request, $e->getMessage());
        }
    }

    /**
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    abstract protected function action(): Response;

    /**
     * @return array|object
     */
    protected function getFormData()
    {
        return $this->request->getParsedBody();
    }

    protected function getAuthTokenHeader()
    {
        return $this->request->getServerParams()['HTTP_AUTH_TOKEN'] ?? "";
    }

    /**
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * @param array|object|null $data
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);

        return $this->respond($payload);
    }

    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);

        return $this->response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($payload->getStatusCode());
    }
}
