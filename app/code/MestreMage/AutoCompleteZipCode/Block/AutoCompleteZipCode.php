<?php


namespace MestreMage\AutoCompleteZipCode\Block;
use MestreMage\Core\Model\ModulesManagement;
class AutoCompleteZipCode extends \Magento\Framework\View\Element\Template
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
    
    /**
     * @return string
     */
    public function isActiv(){

        if(!ModulesManagement::testModule($this->getCoreConfig(\base64_decode('YXV0b2NvbXBsZXRlemlwY29kZS9nZXJhbC9hY3RpdmVfaGFzaA==')),'MestreMage_AutoCompleteZipCode')){
            return false;
        }
        if($this->getLocale()) {
            return $this->getCoreConfig('autocompletezipcode/geral/enabled');
        }else{
            return false;
        }
    }
    /**
     * @return string
     */

    public function getCoreConfig($line){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $scopeConfig->getValue($line, $storeScope);

        if(!$value) {
            if($scopeConfig->getValue("autocompletezipcode/geral/log", $storeScope)){
                $this->setLog('Faltou configura��es do modulo no painel  ex: street 1');
            }
        }

        return $value;
    }

    public function getLocale(){
        $info = $this->getCoreConfig('general/locale/code');

        if($info == 'pt_BR'){
            return true;
        }else{
            if($this->getCoreConfig("autocompletezipcode/geral/log")){
                $this->setLog('o idioma da loja n�o esta em portugu�s  | pt_BR');
            }
            return false;
        }

    }

    public function setLog($msg){
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/AutoCompleteZipCode.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        $logger->info($msg);
    }

}
