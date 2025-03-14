<?php

/**
 * @author Mestre Magento
 * @copyright Copyright (c) Mestre Magento (https://www.modulomagento.com.br/)
 * @license https://www.modulomagento.com.br/termos-condicoes/
 * @support contato@modulomagento.com.br
 */

namespace FCheckout\Core\Block\Adminhtml\System\Config;

class Extensions extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

    return '<p>######## LICENÇA DE USO ########</p>

    <p>Esse módulo foi desenvolvido pela empresa Mestre Magento e pode ser utilizado</p>
    <p>em apenas 1 domínio conforme os termos de uso que concordou ao efetuar a compra</p>
    <p>em nossa loja ou link de pagamento:</p>
    <br>
    <p><a href="https://www.modulomagento.com.br/termos-condicoes">https://www.modulomagento.com.br/termos-condicoes</a></p>
    <br>
    <p>Você empresário ou desenvolvedor, se dedica diáriamente da mesma forma que nós</p>
    <p>para produzir um produto com qualidade e suporte, portanto sempre adquira</p>
    <p>novas licenças de uso caso precise utilizá-lo em novos projetos.</p>

    <p>Não aceite a corrupção como meio viável de sustento e benefício próprio.';

        // return '<iframe id="mestremage_store" width="100%" src="https://www.modulomagento.com.br/coreModulos.php?id=' . uniqid() .'" ></iframe>';
    }
}
