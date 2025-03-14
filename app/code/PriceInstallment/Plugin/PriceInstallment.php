<?php
/**
 *
 * @author      Mestre Magento
 * @copyright   2018 MestreMage (https://mestremage.com.br)
 * @license     https://mestremage.com.br Copyright
 *
 * @link        https://mestremage.com.br/
 */
namespace MestreMage\PriceInstallment\Plugin;
use MestreMage\Core\Model\ModulesManagement;

class PriceInstallment
{

    var $_repeatcount = 0;
    function aroundToHtml($subject, callable $proceed) {
        try {
            $json = '';
            $txt_valor_vista = '';
            $script = '';
            if ($this->getconfPanel('priceInstallment/geral/ativarmodulo')) {
                $finalPreco = $subject->getSaleableItem()->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $parcelasfix = $this->getconfPanel('priceInstallment/loja/valormaximomeses');
                $juros = str_replace(',','.',$this->getconfPanel('priceInstallment/loja/valorjuros'));
                $valorminimo = $this->getconfPanel('priceInstallment/loja/valorminimoparcela');
                $loop = (int)$this->getconfPanel('priceInstallment/loja/loop_tooltip');
                $mostrartabelaparcela = $this->getconfPanel('priceInstallment/loja/mostrartabelaparcela');
                $mostrartotaldasparcelas = $this->getconfPanel('priceInstallment/loja/mostrartotaldasparcelas');
                $jurosparcela = $this->getconfPanel('priceInstallment/loja/jurosapartirdaparcela');
                if(!ModulesManagement::testModule($this->getconfPanel(\base64_decode('cHJpY2VJbnN0YWxsbWVudC9nZXJhbC9hY3RpdmVfaGFzaA==')),'MestreMage_PriceInstallment'))  return $proceed();
                $desconto = $this->getconfPanel('priceInstallment/loja/desconto');
                $mostrarsoparcelasemjuros = $this->getconfPanel('priceInstallment/loja/mostrarsoparcelasemjuros');
                $valordesconto = $this->getconfPanel('priceInstallment/loja/valordesconto');
                $frase = $this->getconfPanel('priceInstallment/loja/frase');
                $valor_a_vista_ativarmodulo = $this->getconfPanel('priceInstallment/loja/valor_a_vista_ativarmodulo');
                $textosemjuros = __($this->getconfPanel('priceInstallment/loja/textosemjuros'));
                $textocomjuros = __($this->getconfPanel('priceInstallment/loja/textocomjuros'));
                $padraodotextonatabela = $this->getconfPanel('priceInstallment/loja/padraodotextonatabela');
                $titulotabela = $this->getconfPanel('priceInstallment/loja/titulotabela');
                $precoavistanatabela = $this->getconfPanel('priceInstallment/loja/precoavistanatabela');
                $textoprecoavista = __($this->getconfPanel('priceInstallment/loja/textoprecoavista'));
                $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
                $currencyCode = $storeManager->getStore()->getCurrentCurrencyCode(); 
                $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($currencyCode); 
                $currencySymbol = $currency->getCurrencySymbol();

                if($jurosparcela > $parcelasfix){
                    $jurosparcela = $parcelasfix;
                }

                if($subject->getSaleableItem()->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $finalPreco = $objectManager->get('Magento\Catalog\Model\Product')->load($subject->getSaleableItem()->getId())->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
                }

                $txtPadrao = $this->getconfPanel('priceInstallment/loja/padraodotexto');
                $valor = $finalPreco;
                $parcelas = 1;
                $final = '';
                for ($x = 1;$x <= $parcelasfix;$x++){
                    $final = ($valor / $x);
                    if((int)$final >= (int)$valorminimo) {
                        $parcelas =  $x;
                    }
                }

                    if ($valor_a_vista_ativarmodulo) {
                        if ($desconto == 1) {
                            $final = ($finalPreco - (float)$valordesconto);
                        } elseif ($desconto == 2) {
                            $percentual = (float)$valordesconto / 100.0;
                            $final = $finalPreco - ($percentual * $finalPreco);
                        }
                        $txt_valor_vista = '<div id="preco-a-vista-mm"><p>' . str_replace("{valor}", $this->formatarValor($final), $frase) . '</p></div>';
                    }
                    if ($subject->getZone() == 'item_view') { // pagina interna do produto

                        if (!$this->_repeatcount) {
                            $this->_repeatcount++;
                            $script .= '<div class="jsr-priceinstallment"  style="list-style:none" >';
                            if ($jurosparcela >= $parcelas) {
                                $script .= '<p class="mm-price-parcels-view">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, 0, $parcelas), str_replace("{parcel}", $parcelas, $txtPadrao)) . '</p>';
                            } else {
                                if($mostrarsoparcelasemjuros){
                                    $parcelassenjuros = $jurosparcela - 1;
                                    $script .= '<p class="mm-price-parcels-view">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, 0, $parcelassenjuros), str_replace("{parcel}", $parcelassenjuros, $txtPadrao)) . '</p>';
                                }else{
                                    $script .= '<p class="mm-price-parcels-view">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, $juros, $parcelas), str_replace("{parcel}", $parcelas, $txtPadrao)) . '</p>';
                                }                               
                            }
                            $script .= '</div>';
                            $json .= '"valorDesconto": "' . $valordesconto .
                                '","tipoDesconto": "' . $desconto .
                                '","jurosParcela": "' . $jurosparcela .
                                '","juros": "' . $juros .
                                '","fraseAvista": "' . $frase .
                                '","parcelasFix": "' . $parcelasfix .
                                '","parcelasModificada": "' . $parcelas .
                                '","mostrarTabelaParcela": "' . $mostrartabelaparcela .
                                '","mostrartotaldasparcelas": "' . $mostrartotaldasparcelas .
                                '","padraodotextonatabela": "' . $padraodotextonatabela .
                                '","tituloTabela": "' . $titulotabela .
                                '","precoAvistaTabela": "' . $precoavistanatabela .
                                '","textoPrecoAvista": "' . $textoprecoavista .
                                '","txtPadrao": "' . $txtPadrao .
                                '","valorMinimo": "' . $valorminimo .
                                '","textoSemJuros": "' . $textosemjuros .
                                '","textoComJuros": "' . $textocomjuros .
                                '","currencysymbol": "' . $currencySymbol .
                                '","mostrarsoparcelasemjuros": "' . $mostrarsoparcelasemjuros .
                                '","typeInterest": "' . 'compound' .
                                '","valorAvistaAtivarmodulo": "' . $valor_a_vista_ativarmodulo .
                                '","lblParcel": "' . __('parcel') .
                                '","lblPrice": "' . __('price') .
                                '"';
                            $json = "<script> var MestreMage_PriceInstallment = JSON.parse('{" . $json . "}'); </script>";
                            $script .= $txt_valor_vista;
                            if($mostrartabelaparcela){
                                $script .= $this->montarTabela($finalPreco, $juros, $parcelas,$final);
                            }
                        }
                    } else{   
                        
                        if($loop){
                            $parcel = '';
                            for ($i = 1; $i <= $loop; $i++) {
                                $for_parcelas = ($parcelas - $i);
                                if($for_parcelas > 1){                    
                                    if ($jurosparcela > $for_parcelas) {
                                        $parcel .= '<p>' .str_replace("{price}", $this->aplicarPorcentagem($finalPreco, 0, $for_parcelas), str_replace("{parcel}", $for_parcelas, $txtPadrao)). '</p>';
                                    }else{
                                        $parcel .= '<p>' .str_replace("{price}", $this->aplicarPorcentagem($finalPreco, $juros, $for_parcelas), str_replace("{parcel}", $for_parcelas, $txtPadrao)). '</p>';
                                    }
                                }
                            }
        
                            
                            if ($jurosparcela >= $parcelas) {
                                $script .= '<div class="btn btn-primary tooltip"><p class="mm-price-parcels-grid">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, 0, $parcelas), str_replace("{parcel}", $parcelas, $txtPadrao)) . '</p><div class="top">'.$parcel.'<i></i></div></div>';
                            } else {
                                if($mostrarsoparcelasemjuros){
                                    $parcelassenjuros = $jurosparcela - 1;
                                    $script .= '<div class="btn btn-primary tooltip"><p class="mm-price-parcels-grid">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, 0, $parcelassenjuros), str_replace("{parcel}", $parcelassenjuros, $txtPadrao)) . '</p><div class="top">'.$parcel.'<i></i></div></div>';
                                }else{
                                    $script .= '<div class="btn btn-primary tooltip"><p class="mm-price-parcels-grid">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, $juros, $parcelas), str_replace("{parcel}", $parcelas, $txtPadrao)) . '</p><div class="top">'.$parcel.'<i></i></div></div>';
                                }                               
                            }

                
                        }else{
                            if ($jurosparcela >= $parcelas) {
                                $script .= '<p class="mm-price-parcels-grid">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, 0, $parcelas), str_replace("{parcel}", $parcelas, $txtPadrao)) . '</p>';                     
                           }else{
                                if($mostrarsoparcelasemjuros){
                                    $parcelassenjuros = $jurosparcela - 1;
                                    $script .= '<p class="mm-price-parcels-grid">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, 0, $parcelassenjuros), str_replace("{parcel}", $parcelassenjuros, $txtPadrao)) . '</p>';                                                   
                                }else{
                                    $script .= '<p class="mm-price-parcels-grid">' . str_replace("{price}", $this->aplicarPorcentagem($finalPreco, $juros, $parcelas), str_replace("{parcel}", $parcelas, $txtPadrao)) . '</p>';                                                   
                                }
                            }
                        }
                        $script .= $txt_valor_vista;
                    }

                if(!$this->getconfPanel('priceInstallment/loja/mostrarparcelaunica')){
                    if($parcelas <= 1){
                        return $proceed();
                    }
                }
            }

            return $proceed().$script.$json;
        } catch (Exception $e) {
            $this->log($e->getMessage());
            return $proceed();
        }
    }

    public function montarTabela($valor,$juros,$parcela,$final = null){
        $jurosparcela = $this->getconfPanel('priceInstallment/loja/jurosapartirdaparcela');
        $padraodotextonatabela = $this->getconfPanel('priceInstallment/loja/padraodotextonatabela');
        $titulotabela = $this->getconfPanel('priceInstallment/loja/titulotabela');
        $precoavistanatabela = $this->getconfPanel('priceInstallment/loja/precoavistanatabela');
        $textoprecoavista = $this->getconfPanel('priceInstallment/loja/textoprecoavista');
        $mostrartotaldasparcelas = $this->getconfPanel('priceInstallment/loja/mostrartotaldasparcelas');
        $showparcelpopup = $this->getconfPanel('priceInstallment/loja/showparcelpopup');
        $txtlinkpopup = $this->getconfPanel('priceInstallment/loja/txtlinkpopup');
        $valor_a_vista_ativarmodulo = $this->getconfPanel('priceInstallment/loja/valor_a_vista_ativarmodulo');
        $desconto = $this->getconfPanel('priceInstallment/loja/desconto');
        $parcelasfix = $this->getconfPanel('priceInstallment/loja/valormaximomeses');

        if($jurosparcela > $parcelasfix){
            $jurosparcela = $parcelasfix;
        }

        $finalPreco = $valor;
        $juros = str_replace(',','.',$this->getconfPanel('priceInstallment/loja/valorjuros'));
        $valordesconto = $this->getconfPanel('priceInstallment/loja/valordesconto');
        $html_total = '';
        if($mostrartotaldasparcelas){
        $html_total = '<th>'.__('Total').'</th>';
        }
        $html = '<div id="mestre-magento-table">';
        $html .= '<h3>'.__($titulotabela).'</h3>';
        $html .= '<table>';
        $html .= '<tr><th>'.__('parcel').'</th><th>'.__('price').'</th>'.$html_total.'</tr>';

        if ($valor_a_vista_ativarmodulo) {
            if ($desconto == 1) {
                $final = ($finalPreco - (float)$valordesconto);
            } elseif ($desconto == 2) {
                $percentual = (float)$valordesconto / 100.0;
                $final = $finalPreco - ($percentual * $finalPreco);
            }

            if($precoavistanatabela){
                $html .= '<tr><td>'.__($textoprecoavista).'</td><td>'.$this->formatarValor($final).'</td></tr>';
            }

        }

        $parcela_link = '';
        for($i= 1; $i <= $parcela; $i++) {
            $totalparcela = '';
            if($jurosparcela > $i){
                if($mostrartotaldasparcelas){
                    $totalparcela = '<td> '.$this->formatarValor($valor).'</td>';
                }
                $html .= '<tr><td>'.str_replace("{parcel}", $i, $padraodotextonatabela).'</td><td> '.$this->aplicarPorcentagem($valor,0,$i).'</td>'.$totalparcela.'</tr>';
            }else{
                if($mostrartotaldasparcelas){
                    $totalparcela = '<td> '.$this->formatarValor(($this->jurosComposto($valor, $juros, $i) * $i)).'</td>';
                }
                $html .= '<tr><td>'.str_replace("{parcel}", $i, $padraodotextonatabela).'</td><td> '.$this->aplicarPorcentagem($valor,$juros,$i).'</td>'.$totalparcela.'</tr>';
                $parcela_link = '<tr><td>'.str_replace("{parcel}", $i, $padraodotextonatabela).'</td><td> '.$this->aplicarPorcentagem($valor,$juros,$i).'</td>'.$totalparcela.'</tr>';
            }
        }
        $html .= '</table> </div>';

        if((int)$showparcelpopup){
            $html = '<div id="modal-priceinstallment-mestremage" class="modal-interna"><div class="modal-interna-content"><div class="modal-interna-header"><span class="close-modal">&times</span></div><div class="modal-interna-body" id="modal-body">'.$html.'</div></div></div><p class="modal-buscar-priceinstallment"> <span id="link-buscar-priceinstallment" style="cursor: pointer;">'.$txtlinkpopup.'</span></p>';
        }
    
        return $html;
    }

    public function jurosSimples($valor, $taxa, $parcelas) {
        if($parcelas > 1) {

            $valParcela = ((float)$valor + (float)$taxa) / (int)$parcelas;

        }else{
            $valParcela = $valor;
        }
        return $valParcela;
    }

    public function jurosComposto($valor,$juros,$parcela) {
        if($parcela > 1) {
            if ($juros) {
                $taxa = (float)$juros / 100;
                $valor_final = ($valor * $taxa) / (1 - (pow(1 / (1 + $taxa), $parcela)));

                $valor_final = explode('.',$valor_final);
                if(isset($valor_final[1])){
                    $final = $valor_final[0].'.'.substr($valor_final[1], 0, 2);
                    $valor_final = (floatval($final));
                }else{
                    $valor_final = $valor_final / $parcela;
                }


            } else {
                $valor_final = $valor / $parcela;
            }
        }else{
            $valor_final = $valor;
        }

        return $valor_final;
    }

    public function aplicarPorcentagem($valor,$juros,$parcela){
        $valor_final = $this->jurosComposto($valor, $juros, $parcela);
        $textoFinal = '';
        $textosemjuros = __($this->getconfPanel('priceInstallment/loja/textosemjuros'));
        $textocomjuros = __($this->getconfPanel('priceInstallment/loja/textocomjuros'));

        if ($juros){
            $textoFinal .= ' ' . $textocomjuros;
        }else{
            $textoFinal .= ' ' . $textosemjuros;
        }
        return $this->formatarValor($valor_final).$textoFinal;
    }

    public function formatarValor($valor){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create('\Magento\Framework\Pricing\PriceCurrencyInterface')->format($valor,true,2);
    }

    public function verificarPag(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
        return $request->getFullActionName();
    }

    public function getconfPanel($valor){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($valor, $storeScope);
    }

    public function log($valor){
        if($this->getconfPanel('priceInstallment/geral/log')) {
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/priceInstallment.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info("Price Installment: ".$valor);
        }
    }
}





