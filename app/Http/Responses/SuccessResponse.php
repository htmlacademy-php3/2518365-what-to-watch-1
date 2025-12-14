<?php

namespace App\Http\Responses;

class SuccessResponse extends BaseResponse
{
    /**
     * Формирование содержимого ответа с успехом.
     *
     * @return array|null
     */
    protected function makeResponseData(): ?array
    {
        return [
            'data' => $this->prepareData(),
        ];
    }
}
