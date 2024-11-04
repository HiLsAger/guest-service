<?php

namespace Hilsager\GuestService;

class RestMessage
{
    private string $message;

    private array $data;

    private int $code;

    public function __construct(array|int $statusOrData = [], string $message = '', int $code = 200)
    {
        if (is_array($statusOrData)) {
            $this->data = $statusOrData;
            $this->message = $message;
            $this->code = $code;
        } else {
            $this->message = $message;
            $this->data = [
                'message' => $statusOrData
                    ? 'Запись успешно обновлена'
                    : 'Ничего не изменилось :('
            ];
            $this->code = $code;
        }
    }

    public function prepareResponse()
    {
        return [
            'result' => $this->data ?? [],
            'code' => $this->code ?? 200,
            'message' => $this->message ?? ''
        ];
    }
}