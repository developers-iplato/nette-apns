<?php

namespace Nemec\Apns;

use ZendService\Apple\Apns\Client\Feedback as Client;
use ZendService\Apple\Apns\Response\Feedback as FeedbackResponse;

class Feedback extends Client {
    
    protected $environment = self::SANDBOX_URI;
    protected $certificate;
    protected $passPhrase;
    
    /**
     * @param int $environment
     * @param string $certificate
     * @param string $passPhrase
     */
    public function __construct($environment, $certificate, $passPhrase = null) {
        $this->environment = $environment;
        $this->certificate = $certificate;
        $this->passPhrase = $passPhrase;
    }
    
    /**
     * @return FeedbackResponse
     */
    public function feedback() {
        if (!$this->isConnected()) {
            $this->open($this->environment, $this->certificate, $this->passPhrase);
        }

        $tokens = array();
        while ($token = $this->read(38)) {
            $tokens[] = new FeedbackResponse($token);
        }

        return $tokens;
    }
    
}