<?php

namespace Nemec\Apns;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use ZendService\Apple\Apns\Client\Message as Client;
use ZendService\Apple\Apns\Message as ApnsMessage;
use ZendService\Apple\Apns\Response\Message as MessageResponse;
use ZendService\Apple\Exception;

class Provider extends Client {
    
    protected $environment = self::SANDBOX_URI;
    protected $certificate;
    protected $passPhrase;

    protected $cache;
    
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
     * @param ApnsMessage $message
     * @return MessageResponse
     * @throws Exception\RuntimeException
     */
    public function send(ApnsMessage $message) {
        if (!$this->isConnected()) {
            $this->open($this->environment, $this->certificate, $this->passPhrase);
        }

        $ret = $this->write($message->getPayloadJson());
        if ($ret === false) {
            throw new Exception\RuntimeException('Server is unavailable; please retry later');
        }
        if ($ret === 0) {
            throw new Exception\RuntimeException('Server is unavailable; broken pipe or closed connection');
        }

        return new MessageResponse($this->read());
    }

    public function reconnect() {
        $this->close();
        $this->open($this->environment, $this->certificate, $this->passPhrase);
    }
}
