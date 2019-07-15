<?php

namespace Nemec\Apns\DI;

use Nette;

class ApnsExtension extends Nette\DI\CompilerExtension {
    
    public $defaults = array(
        'environment' => 0,
        'certificate' => null,
        'passPhrase' => null,
    );
    
    private $configuredProviders = array();
    
    private $configuredFeedbacks = array();
    
    public function loadConfiguration() {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();
        
        if (isset($config['server']) || isset($config['certificate'])) {
            $config = array('default' => $config);
        }
        
        if (empty($config)) {
            throw new \Exception('Please configure the APNs extensions.');
        }
        
        foreach ($config as $name => $conf) {
            if (!is_array($conf) || empty($conf['certificate'])) {
                throw new \Exception('Please configure the APNs extensions.');
            }
            
            $conf = Nette\DI\Config\Helpers::merge($conf, $this->defaults);
            if (($conf['environment'] == 1) || ($conf['environment'] === 'production')) {
                $conf['environment'] = 1;
            } else {
                $conf['environment'] = 0;
            }
            
            $this->processProvider($name, $conf);
            
            $this->processFeedback($name, $conf);
        }
        
        $builder->addDefinition($this->prefix('registry'))->setClass('Nemec\Apns\Registry', array(
            $this->configuredProviders,
            $this->configuredFeedbacks,
            $builder->parameters[$this->name]['defaultProvider'],
            $builder->parameters[$this->name]['defaultFeedback'],
        ));
    }
    
    public function processProvider($name, array $config) {
        $builder = $this->getContainerBuilder();
        
        if (!isset($builder->parameters[$this->name]['defaultProvider'])) {
            $builder->parameters[$this->name]['defaultProvider'] = $name;
        }
        
        $serviceId = $this->prefix($name . '.provider');
        
        $builder->addDefinition($serviceId)
            ->setClass(
                'Nemec\Apns\Provider',
                array($config['environment'], $config['certificate'], $config['passPhrase'])
            );
        
        $this->configuredProviders[$name] = $serviceId;
        
        return $this->prefix('@' . $name . '.provider');
    }
    
    public function processFeedback($name, array $config) {
        $builder = $this->getContainerBuilder();
        
        if (!isset($builder->parameters[$this->name]['defaultFeedback'])) {
            $builder->parameters[$this->name]['defaultFeedback'] = $name;
        }
        
        $serviceId = $this->prefix($name . '.feedback');
        
        $builder->addDefinition($serviceId)
            ->setClass(
                'Nemec\Apns\Feedback', 
                array($config['environment'], $config['certificate'], $config['passPhrase'])
            );
        
        $this->configuredFeedbacks[$name] = $serviceId;
        
        return $this->prefix('@' . $name . '.feedback');
    }

}
