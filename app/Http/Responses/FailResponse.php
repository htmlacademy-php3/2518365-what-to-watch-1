<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FailResponse extends BaseResponse
{
    private string $errorMessage;

    public function __construct(string $errorMessage = null, int $statusCode = null, Throwable $exception = null)
    {
        if ($exception !== null) {
            $this->errorMessage = $errorMessage ?? 'Ошибка в теле запроса';
            $statusCode = $statusCode ?? Response::HTTP_BAD_REQUEST;
        }
        $this->errorMessage = $errorMessage ?? $exception->getMessage();
        $statusCode = $statusCode ?? ($exception->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);

        parent::__construct(null, $statusCode);
    }

    /**
     * Формирование содержимого ответа с ошибкой.
     *
     * @return array|null
     */
    protected function makeResponseData(): ?array
    {
        return [
            'message' => $this->errorMessage,
        ];
    }
}
