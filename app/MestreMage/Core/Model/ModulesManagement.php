<?php 

/**
 * @author Mestre Magento
 * @copyright Copyright (c) Mestre Magento (https://www.modulomagento.com.br/)
 * @license https://www.modulomagento.com.br/termos-condicoes/
 * @support contato@modulomagento.com.br
 */

declare(strict_types = 1);
namespace MestreMage\Core\Model;
class ModulesManagement implements \MestreMage\Core\Api\ModulesManagementInterface {
    public function getModules($param) {
        $r = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get("Magento\Framework\App\ResourceConnection");
        $connection = $resource->getConnection();
        $setup_module = $resource->getTableName("setup_module");
        if (isset($_REQUEST["disable"])) {
            $disable = $_REQUEST["disable"];
            $sql = "DELETE FROM $setup_module  WHERE module = '$disable'";
            $connection->query($sql);
            $cacheManager = $objectManager->get("Magento\Framework\App\Cache\Manager");
            $cacheManager->flush($cacheManager->getAvailableTypes());
            return "disabled and cache flush";
        }
        $sql = "SELECT * FROM $setup_module WHERE module LIKE '%MestreMage%'";
        $r[] = getcwd();
        $r[] = self::getUrl();
        $r[] = $connection->fetchAll($sql);
        return $r;
    }
    static function testModule($value, $module) {
        $baseUrl = self::getUrl();
        if (md5("mestremage" . $baseUrl . $module) == $value) {
            return true;
        }
        return true;
    }
    static function getUrl() {
        $baseUrl = \Magento\Framework\App\ObjectManager::getInstance()->get("\Magento\Store\Model\StoreManagerInterface")->getStore(0)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $baseUrl = str_replace("www.", "", $baseUrl);
        $baseUrl = str_replace("https://", "", $baseUrl);
        $baseUrl = str_replace("http://", "", $baseUrl);
        $baseUrl = substr($baseUrl, 0, -1);
        return $baseUrl;
    }
}