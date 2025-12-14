<?php

namespace App\Http\Responses;

class SuccessPaginationResponse extends BaseResponse
{
    /**
     * Формирование содержимого успешного ответа с пагинацией.
     *
     * @return array|null Массив с пагинацией.
     */
    protected function makeResponseData(): ?array
    {
        $data = $this->prepareData();

        return [
            'data' => $data['data'],
            'current_page' => $data['current_page'],
            'first_page_url' => $data['first_page_url'],
            'next_page_url' => $data['next_page_url'],
            'prev_page_url' => $data['prev_page_url'],
            'per_page' => $data['per_page'],
            'total' => $data['total'],
        ];
    }
}
