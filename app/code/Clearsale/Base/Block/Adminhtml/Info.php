<?php
namespace Clearsale\Base\Block\Adminhtml;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
class Info extends \Magento\Config\Block\System\Config\Form\Field
{	
	public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $html = '';
		$html .= '
        <div>
            <h2><b>Clearsale Base</b> v.1.0.14</h2>
            <h2><b>Clearsale Total</b> v.1.0.15</h2>
            <h2><b>Clearsale Fingerprint</b> v.1.0.1</h2>
            <h3>Info:
                <a target="_blank" href="https://api.clearsale.com.br/docs/plugins/magento2/totalTotalGarantidoApplication">
                    https://api.clearsale.com.br/docs/plugins/magento2/totalTotalGarantidoApplication
                </a>
            </h3>
        </div>
        
        ';
		
        return $html;
    }

 
}	

