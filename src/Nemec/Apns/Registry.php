<?php

namespace Nemec\Apns;

use Nette;

class Registry {
    
    /**
     * @var array 
     */
    private $providers;
    
    /**
     * @var array 
     */
    private $feedbacks;
    
    /**
     * @var string 
     */
    private $defaultProvider;
    
    /**
     * @var string
     */
    private $defaultFeedback;
    
    /**
     * @var Nette\DI\Container 
     */
    private $serviceLocator;
    
    public function __construct(
        array $providers,
        array $feedbacks,
        $defaultProvider,
        $defaultFeedback,
        Nette\DI\Container $serviceLocator
    ) {
        $this->providers = $providers;
        $this->feedbacks = $feedbacks;
        $this->defaultProvider = $defaultProvider;
        $this->defaultFeedback = $defaultFeedback;
        $this->serviceLocator = $serviceLocator;
    }
    
    /**
     * @param string $name
     * @return Provider
     * @throws \Exception
     */
    public function getProvider($name = null) {
        if ($name === null) {
            $name = $this->defaultProvider;
        }
        
        if (!isset($this->providers[$name])) {
            throw new \Exception("Provider named $name does not exist.");
        }
        
        return $this->getService($this->providers[$name]);
    }
    
    /**
     * @return array
     */
    public function getProviderNames() {
        return $this->providers;
    }
    
    /**
     * @return Provider[]
     */
    public function getProviders() {
        $providers = array();
        foreach ($this->providers as $name => $id) {
            $providers[$name] = $this->getService($id);
        }
        
        return $providers;
    }
    
    /**
     * @return string
     */
    public function getDefaultProviderName() {
        return $this->defaultProvider;
    }
    
    /**
     * @param string $name
     * @throws \Exception
     */
    public function resetProvider($name = null) {
        if ($name === null) {
            $name = $this->defaultProvider;
        }
        
        if (!isset($this->providers[$name])) {
            throw new \Exception("Provider named $name does not exist.");
        }
        
        $this->resetService($this->providers[$name]);
    }
    
    /**
     * @param string $name
     * @return Feedback
     * @throws \Exception
     */
    public function getFeedback($name = null) {
        if ($name === null) {
            $name = $this->defaultFeedback;
        }
        
        if (!isset($this->feedbacks[$name])) {
            throw new \Exception("Feedback named $name does not exist.");
        }
        
        return $this->getService($this->feedbacks[$name]);
    }
    
    /**
     * @return array
     */
    public function getFeedbackNames() {
        return $this->feedbacks;
    }
    
    /**
     * @return Feedback[]
     */
    public function getFeedbacks() {
        $feedbacks = array();
        foreach ($this->feedbacks as $name => $id) {
            $feedbacks[$name] = $this->getService($id);
        }
        
        return $feedbacks;
    }
    
    /**
     * @return string
     */
    public function getDefaultFeedbackName() {
        return $this->defaultFeedback;
    }
    
    /**
     * @param string $name
     * @throws \Exception
     */
    public function resetFeedback($name = null) {
        if ($name === null) {
            $name = $this->defaultFeedback;
        }
        
        if (!isset($this->feedbacks[$name])) {
            throw new \Exception("Feedback named $name does not exist.");
        }
        
        $this->resetService($this->feedbacks[$name]);
    }
    
    protected function getService($name) {
        return $this->serviceLocator->getService($name);
    }
    
    protected function resetService($name) {
        $this->serviceLocator->removeService($name);
    }
    
}