<?php

namespace App\Services\JsonErrorResponse;

/**
 * Handles api responses with json+problem header (To standarize errors response)
 */
class JsonErrorResponse 
{
    const TYPE_INVALID_REQUEST_BODY_FORMAT = 'invalid_body_format';///nasz exception
    const TYPE_MODEL_VALIDATION_ERROR = 'model_validation_error';
    const TYPE_FORM_VALIDATION_ERROR = 'form_validation_error';
    const TYPE_NOT_FOUND_ERROR = 'not_found_error';
    const TYPE_ACTION_FAILED = 'action_failed';

    static private $titles = [
        self::TYPE_INVALID_REQUEST_BODY_FORMAT => 'Invalid JSON format sent',
        self::TYPE_MODEL_VALIDATION_ERROR => 'There was a model validation error.',
        self::TYPE_FORM_VALIDATION_ERROR => 'There was a form validation error.',
        self::TYPE_NOT_FOUND_ERROR => 'Object not found.',
        self::TYPE_ACTION_FAILED => 'Action failed.',
    ];

    private $statusCode;

    private $type;

    private $title;

    private $extraData = [];

    public function __construct(int $statusCode, string $type, ?string $customTitle)
    {
        $this->statusCode = $statusCode;
        $this->type = $type;

        if ($customTitle) {
            $this->title = $customTitle;
        } else {
            if (!isset(self::$titles[$type])) {
                throw new \InvalidArgumentException('No title defined for type '.$type);
            }

            $this->title = self::$titles[$type];
        }
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setSingleExtraData(string $name, string $value)
    {
        $this->extraData[$name] = $value;
    }

    public function setArrayExtraData(array $extraData)
    {
        $this->extraData = $extraData;
    }

    public function toArray()
    {
        return array_merge(
            $this->extraData,
            [
                'status' => $this->statusCode,
                'type' => $this->type,
                'title' => $this->title,
            ]
        );
    }
    

}
