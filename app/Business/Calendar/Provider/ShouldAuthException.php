<?php

namespace App\Business\Calendar\Provider;

class ShouldAuthException extends \Exception {
    /** @var string */
    protected $authUrl;

    public function __construct(string $authUrl, string $message = '', int $code = 0, \Exception $previous = null){
        $this->authUrl = $authUrl;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getAuthUrl(): string {
        return $this->authUrl;
    }
}