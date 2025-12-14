<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseResponse implements Responsable
{
    public function __construct(protected mixed $data = [], public int $statusCode = Response::HTTP_OK)
    {
    }

    /**
     * Создание HTTP-ответа, представляющего объект.
     *
     * @param Request $request Запрос
     * @return JsonResponse
     */
    public function toResponse($request)
    {
        return response()->json($this->makeResponseData(), $this->statusCode);
    }

    /**
     * Преобразование возвращаемых данных к массиву.
     *
     * @return array|null
     */
    protected function prepareData(): ?array
    {
        if ($this->data instanceof Arrayable) {
            return $this->data->toArray();
        }

        return $this->data;
    }

    /**
     * Формирование содержимого ответа с текстом ошибки/успеха.
     *
     * @return array|null
     */
    abstract protected function makeResponseData(): ?array;
}
