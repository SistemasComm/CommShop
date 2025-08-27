<?php
/**
 * Copyright © sdfdsfds All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MestreMage\SigepWeb\Controller\Pdf;

use MestreMage\SigepWeb\PhpSigep\Services\SoapClient\Real;
use MestreMage\SigepWeb\PhpSigep\Bootstrap;
use MestreMage\SigepWeb\PhpSigep\Config;
use MestreMage\SigepWeb\PhpSigep\Pdf\CartaoDePostagem;
use MestreMage\SigepWeb\PhpSigep\Pdf\CartaoDePostagem2016;
use MestreMage\SigepWeb\PhpSigep\Pdf\CartaoDePostagem2018;
use MestreMage\SigepWeb\PhpSigep\Model\Dimensao;
use MestreMage\SigepWeb\PhpSigep\Model\Destinatario;
use MestreMage\SigepWeb\PhpSigep\Model\DestinoNacional;
use MestreMage\SigepWeb\PhpSigep\Model\Remetente;
use MestreMage\SigepWeb\PhpSigep\Model\Etiqueta;
use MestreMage\SigepWeb\PhpSigep\Model\ServicoAdicional;
use MestreMage\SigepWeb\PhpSigep\Model\PreListaDePostagem;
use MestreMage\SigepWeb\PhpSigep\Model\AccessDataHomologacao;
use MestreMage\SigepWeb\PhpSigep\Model\ServicoDePostagem;
use MestreMage\SigepWeb\PhpSigep\Model\ObjetoPostal;
use MestreMage\SigepWeb\PhpSigep\Pdf\ListaDePostagem;
use MestreMage\SigepWeb\PhpSigep\Pdf\ImprovedFPDF;
use MestreMage\SigepWeb\PhpSigep\Pdf\Chancela\Carta2016;
use MestreMage\SigepWeb\PhpSigep\Pdf\Chancela\Carta;
use MestreMage\SigepWeb\PhpSigep\Pdf\Chancela\Sedex2016;
use MestreMage\SigepWeb\PhpSigep\Pdf\Chancela\Sedex;
use MestreMage\SigepWeb\PhpSigep\Pdf\Chancela\Pac2016;
use MestreMage\SigepWeb\PhpSigep\Pdf\Chancela\Pac;
use MestreMage\SigepWeb\PhpSigep\Model\CalcPrecoPrazo;
use MestreMage\SigepWeb\PhpSigep\Model\RastrearObjeto;
use MestreMage\SigepWeb\PhpSigep\Model\Diretoria;
use MestreMage\SigepWeb\PhpSigep\Model\AccessData;
use MestreMage\SigepWeb\PhpSigep\Model\SolicitaEtiquetas;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $config = new Config();
        Bootstrap::start($config);

        if (!isset($_REQUEST['param'])) {
            return '?param=gerar_etiqueta || ?param=imprimirEtiquetas_2018 || ?param=buscar_cep || ?param=print_plp || ?param=imprimirEtiquetas || ?param=buscar_cliente || ?param=gera_chancelas || ?param=calc_preco_prazo || ?param=rastreamento_de_objetos';
        }
  
        switch ($_REQUEST['param']) {
            case 'set_infos':
              try {
                  $valor_declarado = $_REQUEST['valor_declarado'];
                  $nota_fiscal = $_REQUEST['nota_fiscal'];
                  $order_id = $_REQUEST['order_id'];
                  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                  $order = $objectManager->create('\Magento\Sales\Model\Order')->load($order_id);
                  $order->getPayment()->setAdditionalInformation('sigep_valor_declarado', $valor_declarado);
                  $order->getPayment()->setAdditionalInformation('sigep_nota_fiscal', $nota_fiscal);
                  $order->getPayment()->save();
                  return 'Valores inseridos com sucesso!!';
              } catch (Exception $e){
                    $this->setLog($e->getMessage());
                  return 'Erro ao adicionar valores!!';
              }
                break;
            case 'imprimirEtiquetas_2018':
                if(!$this->getConfig('section_module_sigepweb/sigepweb/active')){
                    return ['Habilite e configure o modulo do sigepweb'];
                }

                if (!isset($_REQUEST['order_ids'])) {return 'faltou id de pedidos ex: ?order_ids=10,20,30';}
                $order_ids = explode(',', $_REQUEST['order_ids']);
                $etiquetas_sigep_web['errorMsg'] = null;
                $retorno = [];
                $encomendas = [];
                    foreach ($order_ids as $key => $order_id) {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($order_id);
                        if(!count((array)$order->getBillingAddress())){continue;}
                        $id_metodo_entrega = $this->getConfig('section_module_sigepweb/sigepweb/shipping_method_default');
                        if (preg_match('/correios_correios/', $order->getData('shipping_method'))) {
                            $id_metodo_entrega = str_replace('correios_correios','',$order->getData('shipping_method'));
                            $id_metodo_entrega = str_replace('_mm_','',$id_metodo_entrega);
                        }

                        $street = $order->getBillingAddress()->getStreet();
                        if(count($street) == 4){
                            $rua = (isset($street[0]) ? $street[0] : '..');
                            $complemento = (isset($street[2]) ? $street[2] : '..');
                            $numero = (isset($street[1]) ? $street[1] : '..');
                            $bairro = (isset($street[3]) ? $street[3] : '..');
                        }else{
                            $rua = (isset($street[0]) ? $street[0] : '..');
                            $complemento = '';
                            $numero = (isset($street[1]) ? $street[1] : '..');
                            $bairro = (isset($street[2]) ? $street[2] : '..');
                        }
                        $orderItems = $order->getAllVisibleItems();
                        $weight_total = 0;
                        $array_length = [];
                        $array_width = [];
                        $weight = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/weight_default'));
                        $width = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/width_default'));
                        $length = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/length_default'));
                        $height = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/height_default'));
                        $length_total = 0;
                        $height_total = 0;
                        $width_total = 0;
                        foreach ($orderItems as $item) {
                            $qty = (int)$item->getQtyOrdered();
                            $height = ($item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_altura')) ? (((float)$item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_altura'))) * $qty) : ($height * $qty));
                            $length = ($item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_comprimento')) ? (((float)$item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_comprimento'))) * $qty) : ($length * $qty));
                            $width = ($item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_largura')) ? (((float)$item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_largura'))) * $qty) : ($width * $qty));
                            if(in_array($width,$array_width)){
                                $width = 0;
                                $length = 0;
                            }else{
                                $array_width[] = $width;
                                $array_length[] = $length;
                            }
                            $weight = ($item->getProduct()->getWeight() ? ($item->getProduct()->getWeight() * $qty) : ($weight * $qty));
                            $weight_total += $weight;
                            $width_total += $width;
                            $length_total += $length;
                            $height_total += $height;
                        }
                        $dimensao = new Dimensao();
                        $dimensao->setAltura($height_total);
                        $dimensao->setLargura($width_total);
                        $dimensao->setComprimento($length_total);
                        $dimensao->setDiametro(0);
                        $dimensao->setTipo(Dimensao::TIPO_PACOTE_CAIXA);
                        $destinatario = new Destinatario();
                        $destinatario->setNome($this->_removerCaracter($order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName()));
                        $destinatario->setLogradouro($this->_removerCaracter($rua));
                        $destinatario->setNumero($this->_removerCaracter($numero));
                        $destinatario->setComplemento($this->_removerCaracter($complemento));
                        $destino = new DestinoNacional();
                        $destino->setBairro($this->_removerCaracter($bairro));
                        $destino->setCep(preg_replace("/[^0-9]/", "",$order->getBillingAddress()->getPostcode()));
                        $destino->setNumeroNotaFiscal($order->getPayment()->getAdditionalInformation('sigep_nota_fiscal'));
                        $destino->setCidade($this->_removerCaracter($order->getBillingAddress()->getCity()));
                        $destino->setUf($this->buscarUf($order->getBillingAddress()->getRegionId()));
                        $etiqueta = new Etiqueta();
                        $etiquetas_sem_dv = '';
                        $etiquetas_com_dv = '';
                        if($order->getPayment()->getAdditionalInformation('sigep_etiquetas_sem_dv')){
                            $etiquetas_sem_dv = $order->getPayment()->getAdditionalInformation('sigep_etiquetas_sem_dv');
                            $id_metodo_entrega = $order->getPayment()->getAdditionalInformation('sigep_id_metodo_entrega');
                        }else{

                            $etiquetas_return = $this->getCodeCorreiosByService($id_metodo_entrega);
                            $etiquetas_sem_dv = $etiquetas_return['code_correios'];
                            $id_metodo_entrega = $etiquetas_return['code_servico'];

                            if ($etiquetas_sem_dv) {
                                $etiquetas_com_dv = $this->getEtiquetaDv($etiquetas_sem_dv);
                                $order->getPayment()->setAdditionalInformation('sigep_etiquetas_com_dv', $etiquetas_com_dv);
                                $order->getPayment()->setAdditionalInformation('sigep_etiquetas_sem_dv', $etiquetas_sem_dv);
                                $order->getPayment()->setAdditionalInformation('sigep_id_metodo_entrega', $id_metodo_entrega);
                                $order->getPayment()->setAdditionalInformation('sigep_id_status', 'Aberto - ');                                
                                $order->getPayment()->save();
                            }
                        }
        
                        if($etiquetas_sem_dv){
                            $etiqueta->setEtiquetaSemDv($etiquetas_sem_dv);
                            $servicoAdicional = new ServicoAdicional();
                            $servicoAdicional->setCodigoServicoAdicional(ServicoAdicional::SERVICE_REGISTRO);
                            $servicoAdicional->setCodigoServicoAdicional(ServicoAdicional::SERVICE_AVISO_DE_RECEBIMENTO);
                            $servicoAdicional2 = new ServicoAdicional();
                            $servicoAdicional2->setCodigoServicoAdicional(ServicoAdicional::SERVICE_REGISTRO);

                            if($sigep_valor_declarado =$order->getPayment()->getAdditionalInformation('sigep_valor_declarado')){
                                $servicoAdicional->setCodigoServicoAdicional(ServicoAdicional::SERVICE_VALOR_DECLARADO);
                                $servicoAdicional->setValorDeclarado($sigep_valor_declarado);
                            }

                            $encomenda = new ObjetoPostal();
                            $encomenda->setServicosAdicionais(array($servicoAdicional, $servicoAdicional2));
                            $encomenda->setDestinatario($destinatario);
                            $encomenda->setDestino($destino);
                            $encomenda->setDimensao($dimensao);
                            $encomenda->setEtiqueta($etiqueta);
                            $encomenda->setPeso($weight_total);
                            $encomenda->setServicoDePostagem(new ServicoDePostagem($id_metodo_entrega));
                            $encomendas[] = $encomenda;
                            $this->addTrack($order,$etiquetas_com_dv);
                        }
                    }
                    if (count($encomendas)) {
                        $remetente = new Remetente();
                        $remetente->setNome($this->getConfig('section_module_sigepweb/sigepweb/nome_cliente'));
                        $remetente->setLogradouro($this->getConfig('section_module_sigepweb/sigepweb/endereco_remetente'));
                        $remetente->setNumero($this->getConfig('section_module_sigepweb/sigepweb/numero_remetente'));
                        $remetente->setComplemento($this->getConfig('section_module_sigepweb/sigepweb/compl_remetente'));
                        $remetente->setBairro($this->getConfig('section_module_sigepweb/sigepweb/bairro_remetente'));
                        $remetente->setCep($this->getConfig('section_module_sigepweb/sigepweb/cep_origem'));
                        $remetente->setUf($this->getConfig('section_module_sigepweb/sigepweb/uf_remetente'));
                        $remetente->setCidade($this->getConfig('section_module_sigepweb/sigepweb/cidade_remetente'));
                        $remetente->setTelefone($this->getConfig('section_module_sigepweb/sigepweb/fone_remetente'));
                        $plp = new PreListaDePostagem();
                        $plp->setAccessData(new AccessDataHomologacao());
                        $plp->setEncomendas($encomendas);
                        $plp->setRemetente($remetente);
                        $this->setLog('imprimirEtiquetas: '.json_encode((array)$remetente));
                        $logoFile = $this->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'sigepweb/'.$this->getConfig('section_module_sigepweb/sigepweb/application_icon');
                        $pdf = new CartaoDePostagem2018($plp, time(), $logoFile, array());
                        $pdf->render('I');
                    }
    
                return $retorno;
                break;
            case 'print_plp':
                if(!$this->getConfig('section_module_sigepweb/sigepweb/active')){
                    return ['Habilite e configure o modulo do sigepweb'];
                }

                if (!isset($_REQUEST['order_ids'])) {return 'faltou id de pedidos ex: ?order_ids=10,20,30';}
                $order_ids = explode(',', $_REQUEST['order_ids']);
                $etiquetas_sigep_web['errorMsg'] = null;
                $retorno = [];
                $encomendas = [];
                    foreach ($order_ids as $key => $order_id) {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                         $order = $objectManager->create('\Magento\Sales\Model\Order')->load($order_id);
                        if(!count((array)$order->getBillingAddress())){continue;}
                        $id_metodo_entrega = $this->getConfig('section_module_sigepweb/sigepweb/shipping_method_default');
                        if (preg_match('/correios_correios/', $order->getData('shipping_method'))) {
                            $id_metodo_entrega = str_replace('correios_correios','',$order->getData('shipping_method'));
                            $id_metodo_entrega = str_replace('_mm_','',$id_metodo_entrega);
                        }
                        $street = $order->getBillingAddress()->getStreet();
                        if(count($street) == 4){
                            $rua = (isset($street[0]) ? $street[0] : '..');
                            $complemento = (isset($street[2]) ? $street[2] : '..');
                            $numero = (isset($street[1]) ? $street[1] : '..');
                            $bairro = (isset($street[3]) ? $street[3] : '..');
                        }else{
                            $rua = (isset($street[0]) ? $street[0] : '..');
                            $complemento = '';
                            $numero = (isset($street[1]) ? $street[1] : '..');
                            $bairro = (isset($street[2]) ? $street[2] : '..');
                        }
                        $orderItems = $order->getAllVisibleItems();
                        $weight_total = 0;
                        $array_length = [];
                        $array_width = [];
                        $weight = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/weight_default'));
                        $width = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/width_default'));
                        $length = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/length_default'));
                        $height = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/height_default'));
                        $length_total = 0;
                        $height_total = 0;
                        $width_total = 0;
                        foreach ($orderItems as $item) {
                            $qty = (int)$item->getQtyOrdered();
                            $height = ($item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_altura')) ? (((float)$item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_altura'))) * $qty) : ($height * $qty));
                            $length = ($item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_comprimento')) ? (((float)$item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_comprimento'))) * $qty) : ($length * $qty));
                            $width = ($item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_largura')) ? (((float)$item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_largura'))) * $qty) : ($width * $qty));
                            if(in_array($width,$array_width)){
                                $width = 0;
                                $length = 0;
                            }else{
                                $array_width[] = $width;
                                $array_length[] = $length;
                            }
                            $weight = ($item->getProduct()->getWeight() ? ($item->getProduct()->getWeight() * $qty) : ($weight * $qty));
                            $weight_total += $weight;
                            $width_total += $width;
                            $length_total += $length;
                            $height_total += $height;
                        }
                        $dimensao = new Dimensao();
                        $dimensao->setAltura($height_total);
                        $dimensao->setLargura($width_total);
                        $dimensao->setComprimento($length_total);
                        $dimensao->setDiametro(0);
                        $dimensao->setTipo(Dimensao::TIPO_PACOTE_CAIXA);
                        $destinatario = new Destinatario();
                        $destinatario->setNome($this->_removerCaracter($order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName()));
                        $destinatario->setLogradouro($this->_removerCaracter($rua));
                        $destinatario->setNumero($this->_removerCaracter($numero));
                        $destinatario->setComplemento($this->_removerCaracter($complemento));
                        $destino = new DestinoNacional();
                        $destino->setBairro($this->_removerCaracter($bairro));
                        $destino->setCep(preg_replace("/[^0-9]/", "",$order->getBillingAddress()->getPostcode()));
                        $destino->setNumeroNotaFiscal($order->getPayment()->getAdditionalInformation('sigep_nota_fiscal'));
                        $destino->setCidade($this->_removerCaracter($order->getBillingAddress()->getCity()));
                        $destino->setUf($this->buscarUf($order->getBillingAddress()->getRegionId()));
                        $etiqueta = new Etiqueta();
                        $etiquetas_sem_dv = '';
                        $etiquetas_com_dv = '';
                        if($order->getPayment()->getAdditionalInformation('sigep_etiquetas_sem_dv')){
                            $etiquetas_sem_dv = $order->getPayment()->getAdditionalInformation('sigep_etiquetas_sem_dv');
                            $id_metodo_entrega = $order->getPayment()->getAdditionalInformation('sigep_id_metodo_entrega');
                        }else{

                            $etiquetas_return = $this->getCodeCorreiosByService($id_metodo_entrega);
                            $etiquetas_sem_dv = $etiquetas_return['code_correios'];
                            $id_metodo_entrega = $etiquetas_return['code_servico'];

                            if ($etiquetas_sem_dv) {
                                $etiquetas_com_dv = $this->getEtiquetaDv($etiquetas_sem_dv);
                                $order->getPayment()->setAdditionalInformation('sigep_etiquetas_com_dv', $etiquetas_com_dv);
                                $order->getPayment()->setAdditionalInformation('sigep_etiquetas_sem_dv', $etiquetas_sem_dv);
                                $order->getPayment()->setAdditionalInformation('sigep_id_metodo_entrega', $id_metodo_entrega);
                                $order->getPayment()->setAdditionalInformation('sigep_id_status', 'Aberto - ');
                                $order->getPayment()->save();
                            }
                        }

                        if($etiquetas_sem_dv) {
                            $etiqueta->setEtiquetaSemDv($etiquetas_sem_dv);
                            $servicoAdicional = new ServicoAdicional();
                            $servicoAdicional->setCodigoServicoAdicional(ServicoAdicional::SERVICE_REGISTRO);



                            if($sigep_valor_declarado =$order->getPayment()->getAdditionalInformation('sigep_valor_declarado')){
                            $servicoAdicional->setCodigoServicoAdicional(ServicoAdicional::SERVICE_VALOR_DECLARADO);
                            $servicoAdicional->setValorDeclarado($sigep_valor_declarado);
                            }


                            $encomenda = new ObjetoPostal();
                            $encomenda->setServicosAdicionais(array($servicoAdicional));
                            $encomenda->setDestinatario($destinatario);
                            $encomenda->setDestino($destino);
                            $encomenda->setDimensao($dimensao);
                            $encomenda->setEtiqueta($etiqueta);
                            $encomenda->setPeso($weight_total);
                            $encomenda->setServicoDePostagem(new ServicoDePostagem($id_metodo_entrega));                            
                            $encomendas[] = $encomenda;
                           $this->addTrack($order,$etiquetas_com_dv);
                        }
                    }
                if (count($encomendas)) {
                    $remetente = new Remetente();
                    $remetente->setNome($this->getConfig('section_module_sigepweb/sigepweb/nome_cliente'));
                    $remetente->setLogradouro($this->getConfig('section_module_sigepweb/sigepweb/endereco_remetente'));
                    $remetente->setNumero($this->getConfig('section_module_sigepweb/sigepweb/numero_remetente'));
                    $remetente->setComplemento($this->getConfig('section_module_sigepweb/sigepweb/compl_remetente'));
                    $remetente->setBairro($this->getConfig('section_module_sigepweb/sigepweb/bairro_remetente'));
                    $remetente->setCep($this->getConfig('section_module_sigepweb/sigepweb/cep_origem'));
                    $remetente->setUf($this->getConfig('section_module_sigepweb/sigepweb/uf_remetente'));
                    $remetente->setCidade($this->getConfig('section_module_sigepweb/sigepweb/cidade_remetente'));
                    $remetente->setTelefone($this->getConfig('section_module_sigepweb/sigepweb/fone_remetente'));
                    $plp = new PreListaDePostagem();
                    $plp->setAccessData(new AccessDataHomologacao());
                    $plp->setEncomendas($encomendas);
                    $plp->setRemetente($remetente);
                    $this->setLog('print_plp: '.json_encode((array)$remetente));
                    $pdf  = new ListaDePostagem($plp, time());
                    $pdf->render('I');
                }
              
                return $retorno;
                break;
            case 'fechar_plp':
      


                $r = [];
                $usuario = $this->getConfig('section_module_sigepweb/sigepweb/usuario');
                $senha = $this->getConfig('section_module_sigepweb/sigepweb/senha');
                $contrato = $this->getConfig('section_module_sigepweb/sigepweb/numero_contrato');
                $cartao =$this->getConfig('section_module_sigepweb/sigepweb/cartao_postagem');
                $cod_administrativo =$this->getConfig('section_module_sigepweb/sigepweb/cod_administrativo');
                $diretoria =$this->getConfig('section_module_sigepweb/sigepweb/diretoria');
                $cnpj_remetente =$this->getConfig('section_module_sigepweb/sigepweb/cnpj_remetente');
                $context = stream_context_create(array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                        )
                    ));
                    $url_context = 'https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl';
                $client = new \SoapClient($url_context, array( 
                    'stream_context' => $context
                    ));

                 $this->setLog('ENVIO :: Url: '.$url_context);
          
                if (!isset($_REQUEST['order_ids'])) {return 'faltou id de pedidos ex: ?order_ids=10,20,30';}
                $order_ids = explode(',', $_REQUEST['order_ids']);
                $etiquetas_sigep_web['errorMsg'] = null;
                $retorno = [];
                $encomendas = [];

                    foreach ($order_ids as $key => $order_id) {

                        if(!$order_id) continue;

                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                         $order = $objectManager->create('\Magento\Sales\Model\Order')->load($order_id);
                           
                         if($order->getPayment()->getAdditionalInformation('sigep_etiquetas_sem_dv')){
                            $etiquetas_sem_dv = $order->getPayment()->getAdditionalInformation('sigep_etiquetas_sem_dv');
                            $sigep_id_metodo_entrega = $order->getPayment()->getAdditionalInformation('sigep_id_metodo_entrega');
                            $sigep_valor_declarado = $order->getPayment()->getAdditionalInformation('sigep_valor_declarado');

                            $street = $order->getBillingAddress()->getStreet();
                            if(count($street) == 4){
                                $rua = (isset($street[0]) ? $street[0] : '..');
                                $complemento = (isset($street[2]) ? $street[2] : '..');
                                $numero = (isset($street[1]) ? $street[1] : '..');
                                $bairro = (isset($street[3]) ? $street[3] : '..');
                            }else{
                                $rua = (isset($street[0]) ? $street[0] : '..');
                                $complemento = '';
                                $numero = (isset($street[1]) ? $street[1] : '..');
                                $bairro = (isset($street[2]) ? $street[2] : '..');
                            }
                            $orderItems = $order->getAllVisibleItems();
                            $weight_total = 0;
                            $array_length = [];
                            $array_width = [];
                            $weight = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/weight_default'));
                            $width = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/width_default'));
                            $length = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/length_default'));
                            $height = str_replace(',','.',$this->getConfig('section_module_sigepweb/sigepweb/height_default'));
                            $length_total = 0;
                            $height_total = 0;
                            $width_total = 0;
                            foreach ($orderItems as $item) {
                                $qty = (int)$item->getQtyOrdered();
                                $height = ($item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_altura')) ? (((float)$item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_altura'))) * $qty) : ($height * $qty));
                                $length = ($item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_comprimento')) ? (((float)$item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_comprimento'))) * $qty) : ($length * $qty));
                                $width = ($item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_largura')) ? (((float)$item->getData($this->getConfig('section_module_sigepweb/sigepweb/sigepweb_largura'))) * $qty) : ($width * $qty));
                                if(in_array($width,$array_width)){
                                    $width = 0;
                                    $length = 0;
                                }else{
                                    $array_width[] = $width;
                                    $array_length[] = $length;
                                }
                                $weight = ($item->getProduct()->getWeight() ? ($item->getProduct()->getWeight() * $qty) : ($weight * $qty));
                                $weight_total += $weight;
                                $width_total += $width;
                                $length_total += $length;
                                $height_total += $height;
                            }



                            $xml = "<?xml version='1.0' encoding='ISO-8859-1' ?>
                            <correioslog>
                            <tipo_arquivo>Postagem</tipo_arquivo>
                            <versao_arquivo>2.3</versao_arquivo>
                            <plp>
                            <id_plp />
                            <valor_global />
                            <mcu_unidade_postagem />
                            <nome_unidade_postagem />
                            <cartao_postagem><![CDATA[".$cartao."]]></cartao_postagem>
                            </plp>
                            <remetente>
                            <numero_contrato><![CDATA[".$contrato."]]></numero_contrato>
                            <numero_diretoria><![CDATA[".$diretoria."]]></numero_diretoria>
                            <codigo_administrativo><![CDATA[".$cod_administrativo."]]></codigo_administrativo>
                            <nome_remetente><![CDATA[".$this->getConfig('section_module_sigepweb/sigepweb/nome_cliente')."]]></nome_remetente>
                            <logradouro_remetente><![CDATA[".$this->getConfig('section_module_sigepweb/sigepweb/endereco_remetente')."]]></logradouro_remetente>
                            <numero_remetente><![CDATA[".$this->getConfig('section_module_sigepweb/sigepweb/numero_remetente')."]]></numero_remetente>
                            <complemento_remetente><![CDATA[".$this->getConfig('section_module_sigepweb/sigepweb/compl_remetente')."]]></complemento_remetente>
                            <bairro_remetente><![CDATA[".$this->getConfig('section_module_sigepweb/sigepweb/bairro_remetente')."]]></bairro_remetente>
                            <cep_remetente><![CDATA[".$this->getConfig('section_module_sigepweb/sigepweb/cep_origem')."]]></cep_remetente>
                            <cidade_remetente><![CDATA[".$this->getConfig('section_module_sigepweb/sigepweb/cidade_remetente')."]]></cidade_remetente>
                            <uf_remetente><![CDATA[".$this->getConfig('section_module_sigepweb/sigepweb/uf_remetente')."]]></uf_remetente>
                            <telefone_remetente><![CDATA[".$this->getConfig('section_module_sigepweb/sigepweb/fone_remetente')."]]></telefone_remetente>
                            <fax_remetente><![CDATA[]]></fax_remetente>
                            <celular_remetente />
                            <cpf_cnpj_remetente><![CDATA[".$cnpj_remetente."]]></cpf_cnpj_remetente>
                            <ciencia_conteudo_proibido><![CDATA[S]]></ciencia_conteudo_proibido>
                            </remetente>
                            <forma_pagamento/>
                            <objeto_postal>
                            <numero_etiqueta><![CDATA[".str_replace(' ','',$etiquetas_sem_dv)."]]></numero_etiqueta>
                            <codigo_objeto_cliente/>
                            <codigo_servico_postagem><![CDATA[".$sigep_id_metodo_entrega."]]></codigo_servico_postagem>
                            <cubagem><![CDATA[0]]></cubagem>
                            <peso><![CDATA[".$weight_total."]]></peso>
                            <rt1/>
                            <rt2/>
                            <restricao_anac/>
                            <destinatario>
                            <nome_destinatario><![CDATA[".$this->_removerCaracter($order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName())."]]></nome_destinatario>
                            <telefone_destinatario><![CDATA[".preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getTelephone())."]]></telefone_destinatario>
                            <celular_destinatario><![CDATA[".preg_replace("/[^0-9]/", "", $order->getBillingAddress()->getFax())."]]></celular_destinatario>
                            <email_destinatario><![CDATA[".$order->getBillingAddress()->getEmail()."]]></email_destinatario>
                            <logradouro_destinatario><![CDATA[".$this->_removerCaracter($rua)."]]></logradouro_destinatario>
                            <complemento_destinatario><![CDATA[".$this->_removerCaracter($complemento)."]]></complemento_destinatario>
                            <numero_end_destinatario><![CDATA[".$this->_removerCaracter($numero)."]]></numero_end_destinatario>
                            <cpf_cnpj_destinatario><![CDATA[".($order->getCustomerTaxvat() ? $order->getCustomerTaxvat() : ($order->getBillingAddress()->getVatId()? $order->getBillingAddress()->getVatId() :$order->getShippingAddress()->getVatId()))."]]></cpf_cnpj_destinatario>
                            </destinatario>
                            <nacional>
                            <bairro_destinatario><![CDATA[".$this->_removerCaracter($bairro)."]]></bairro_destinatario>
                            <cidade_destinatario><![CDATA[".$this->_removerCaracter($order->getBillingAddress()->getCity())."]]></cidade_destinatario>
                            <uf_destinatario><![CDATA[".$this->buscarUf($order->getBillingAddress()->getRegionId())."]]></uf_destinatario>
                            <cep_destinatario><![CDATA[".preg_replace("/[^0-9]/", "",$order->getBillingAddress()->getPostcode())."]]></cep_destinatario>
                            <codigo_usuario_postal/>
                            <centro_custo_cliente/>
                            <numero_nota_fiscal><![CDATA[".$order->getPayment()->getAdditionalInformation('sigep_nota_fiscal')."]]></numero_nota_fiscal>
                            <serie_nota_fiscal/>
                            <valor_nota_fiscal/>
                            <natureza_nota_fiscal/>
                            <descricao_objeto><![CDATA[Teste]]></descricao_objeto>
                            <valor_a_cobrar><![CDATA[0,0]]></valor_a_cobrar>
                            </nacional>
                            <servico_adicional>
                            <valor_declarado><![CDATA[".$sigep_valor_declarado."]]></valor_declarado>
                            </servico_adicional>
                            <dimensao_objeto>
                            <tipo_objeto><![CDATA[".Dimensao::TIPO_PACOTE_CAIXA."]]></tipo_objeto>
                            <dimensao_altura><![CDATA[".$height_total."]]></dimensao_altura>
                            <dimensao_largura><![CDATA[".$width_total."]]></dimensao_largura>
                            <dimensao_comprimento><![CDATA[".$length_total."]]></dimensao_comprimento>
                            </dimensao_objeto>
                            <data_postagem_sara/>
                            <status_processamento><![CDATA[0]]></status_processamento>
                            <numero_comprovante_postagem/>
                            <valor_cobrado/>
                            </objeto_postal>
                            </correioslog>";



                            $soapArgs = array(
                                'xml' => $xml,
                                'idPlpCliente'        => $order_id,
                                'listaEtiquetas'     => str_replace(' ','',$etiquetas_sem_dv),
                                'cartaoPostagem'     => $cartao,
                                'usuario'          => $usuario,
                                'senha'            => $senha
                                );

                                $this->setLog('ENVIO :: fechar_plp: '.trim(preg_replace('/\s\s+/', ' ', $xml)));
                                $returnPlp =$client->fechaPlpVariosServicos( $soapArgs );  
                                $this->setLog('RETORNO :: fechar_plp: '.json_encode((array)$returnPlp));
                                $order->getPayment()->setAdditionalInformation('sigep_id_status', 'Fechado - ');
                                $order->getPayment()->save();
                        
                     
                         }

                    }
                 echo '<script>window.history.go(-1);</script>';
                 exit();
                break;
        }



    }


    public function getEtiquetaDv($etiquetas_sem_dv) {

        $usuario = $this->getConfig('section_module_sigepweb/sigepweb/usuario');
        $senha = $this->getConfig('section_module_sigepweb/sigepweb/senha');
        $contrato = $this->getConfig('section_module_sigepweb/sigepweb/numero_contrato');
        $cartao =$this->getConfig('section_module_sigepweb/sigepweb/cartao_postagem');
        
        
        $context = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
            ));
        $client = new \SoapClient('https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl', array( 
            'stream_context' => $context
            ));
        
                $soapArgs = array(
                    'etiquetas'     => $etiquetas_sem_dv,
                    'usuario'          => $usuario,
                    'senha'            => $senha
                    );

            $digitoverificador = $client->geraDigitoVerificadorEtiquetas( $soapArgs );  

            return str_replace(' ',$digitoverificador->return,$etiquetas_sem_dv);
    }

    function addTrack($order,$number){
        if (! $order->canShip()) { return; };
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $url = 'https://www2.correios.com.br/sistemas/rastreamento/default.cfm?objetos='.$number;
        $convertOrder = $objectManager->create('Magento\Sales\Model\Convert\Order');
        $shipment = $convertOrder->toShipment($order);
        foreach ($order->getAllItems() as $orderItem) {
            if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }
            $qtyShipped = $orderItem->getQtyToShip();
            $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
            $shipment->addItem($shipmentItem);
        }
        $shipment->register();
        $data = array(
            'carrier_code' => 'correios',
            'title' => 'Correios',
            'number' => $number,
            'url' => $url
        );
        $shipment->getOrder()->setIsInProcess(true);
        try {
            $track = $objectManager->create('Magento\Sales\Model\Order\Shipment\TrackFactory')->create()->addData($data);
            $shipment->addTrack($track)->save();
            $shipment->save();
            $shipment->getOrder()->save();
            $objectManager->create('Magento\Shipping\Model\ShipmentNotifier')->notify($shipment);
            $shipment->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }

    public function getCodeCorreiosByService($code_servico) {
        $r = [];
        $usuario = $this->getConfig('section_module_sigepweb/sigepweb/usuario');
        $senha = $this->getConfig('section_module_sigepweb/sigepweb/senha');
        $contrato = $this->getConfig('section_module_sigepweb/sigepweb/numero_contrato');
        $cartao =$this->getConfig('section_module_sigepweb/sigepweb/cartao_postagem');
        
        
        $context = stream_context_create(array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
                )
            ));
        $client = new \SoapClient('https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl', array( 
            'stream_context' => $context
            ));
        
            $soapArgs = array(
                'idContrato'       => $contrato,
                'idCartaoPostagem' => $cartao,
                'usuario'          => $usuario,
                'senha'            => $senha
                );
        
                $servicosResponse = $client->buscaCliente( $soapArgs );

                $servicos = []; 
                $servico_id = '';               
                $servico_codigo = '';               
                foreach( $servicosResponse->return->contratos->cartoesPostagem->servicos as $servico )
                {
                    $servicos[preg_replace("/[^0-9]/", "",$servico->codigo)] = $servico->id;
                    $servico_codigo_retorno = $servico->codigo;
                    $servico_id_retorno = $servico->id;
         
                }
        
                if(isset($servicos[$code_servico])){
                    $servico = $servicos[$code_servico];
                }else{
                    $id_metodo_entrega = $this->getConfig('section_module_sigepweb/sigepweb/shipping_method_default');
                    if(isset($servicos[$id_metodo_entrega])){
                        $servico = $servicos[$id_metodo_entrega];
                    }else{
                        $servico = array_values($servicos)[0];
                    }

                    $code_servico = $id_metodo_entrega;
                }
           

                $cnpj = $servicosResponse->return->cnpj;   
                $soapArgs = array(
                    'tipoDestinatario' => 'C',
                    'identificador'    => $cnpj,
                    'idServico'        => $servico,
                    'qtdEtiquetas'     => 1,
                    'usuario'          => $usuario,
                    'senha'            => $senha
                    );
        
            $etiquetasResponse = $client->solicitaEtiquetas( $soapArgs );      
            $r['code_correios'] = substr($etiquetasResponse->return, 0 ,strpos($etiquetasResponse->return, ','));
            $r['code_servico'] = $code_servico;
            return $r;
    }

    public function _removerCaracter($string) {
        return preg_replace(array("/(ç|Ç)/","/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","c a A e E i I o O u U n N"),$string);
    }

    public function buscarUf($uf){
        $idUf = '';
        switch($uf) {
            case 485:
                $idUf = "AC";
                break;
            case 486:
                $idUf = "AL";
                break;
            case 487:
                $idUf = "AP";
                break;
            case 488:
                $idUf = "AM";
                break;
            case 489:
                $idUf = "BA";
                break;
            case 490:
                $idUf = "CE";
                break;
            case 491:
                $idUf = "ES";
                break;
            case 492:
                $idUf = "GO";
                break;
            case 493:
                $idUf = "MA";
                break;
            case 494:
                $idUf = "MT";
                break;
            case 495:
                $idUf = "MS";
                break;
            case 496:
                $idUf = "MG";
                break;
            case 497:
                $idUf = "PA";
                break;
            case 498:
                $idUf = "PB";
                break;
            case 499:
                $idUf = "PR";
                break;
            case 500:
                $idUf = "PE";
                break;
            case 501:
                $idUf = "PI";
                break;
            case 502:
                $idUf = "RJ";
                break;
            case 503:
                $idUf = "RN";
                break;
            case 504:
                $idUf = "RS";
                break;
            case 505:
                $idUf = "RO";
                break;
            case 506:
                $idUf = "RR";
                break;
            case 507:
                $idUf = "SC";
                break;
            case 508:
                $idUf = "SP";
                break;
            case 509:
                $idUf = "SE";
                break;
            case 510:
                $idUf = "TO";
                break;
            case 511:
                $idUf = "DF";
                break;
        }
        return $idUf;
    }

    public function getConfig($string){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        $scopeConfig = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $scopeConfig->getValue($string, $storeScope);
    }

    /**
     * @return string
     */
    public function getBaseUrl($type)
    {
        $storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl($type);
    }
    function setLog($msg){
        if($this->getConfig('section_module_sigepweb/sigepweb/log')){
            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/sigepweb.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);
            $logger->info(json_encode($msg));
        }
    }
    function getEtiquetasSigep($obj){
        $array = $obj->toArray();
        $return = [];
        if(isset($array['result'])){
            $return['errorMsg'] = null;
            foreach($array['result'] as $item){
                $return['result'][] = $item['etiquetaSemDv'];
            }
        }else{
            $return['errorMsg'] = $array['errorMsg'];
        }
        return $return;
    }
}