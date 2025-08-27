<?php
declare(strict_types=1);

namespace MestreMage\Correios\Model;

class ImportexportManagement implements \MestreMage\Correios\Api\ImportexportManagementInterface
{
    /**
     * {@inheritdoc}
     */
    public function postImportexport($req)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $backendUrl = $objectManager->get('\Magento\Backend\Model\UrlInterface');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('mm_correios_offline');
        
        if($req == 1){
            $file = $_FILES['file']['tmp_name'];
                $handle = fopen($file,"r");
                $data = [];

                do {
                    if (isset($data[0])) {
                        if($data[0] != 'servico'){
                            $connection->query("INSERT INTO $tableName (servico, prazo, peso_inicial, peso_final, valor, cep_inicial, cep_final) VALUES
                            (
                                '".$this->retirarAspas(addslashes($data[0]))."',
                                '".$this->retirarAspas(addslashes($data[1]))."',
                                '".$this->retirarAspas(addslashes($data[2]))."',
                                '".$this->retirarAspas(addslashes($data[3]))."',
                                '".$this->retirarAspas(addslashes($data[4]))."',
                                '".$this->retirarAspas(addslashes($data[5]))."',
                                '".$this->retirarAspas(addslashes($data[6]))."'
                            )
                        ");
                        }
                    }
                } while ($data = fgetcsv($handle,1000,",","'"));
   
                header(sprintf('Location: %s', $backendUrl->getUrl("correios/correios/index", [])));

        }else{

            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=CEP_CORREIOS.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
    
            $data = $connection->fetchAll("SELECT servico, prazo, peso_inicial, peso_final, valor, cep_inicial, cep_final FROM $tableName"); 
            $output = fopen("php://output", "w");

            if(count($data)){
                foreach ($data as $key =>  $row) {
                    if($key == 0){
                        fputcsv($output, [0 => 'servico', 1 => 'prazo', 2 => 'peso_inicial', 3 => 'peso_final', 4 => 'valor', 5 => 'cep_inicial', 6 => 'cep_final']);
                    }
                    fputcsv($output, $row);
    
                }
            }else{
                fputcsv($output, [0 => 'servico', 1 => 'prazo', 2 => 'peso_inicial', 3 => 'peso_final', 4 => 'valor', 5 => 'cep_inicial', 6 => 'cep_final']);
                fputcsv($output, [0 => 'SEDEX', 1 => '3', 2 => '1', 3 => '2', 4 => '18,50', 5 => '01000000', 6 => '03000000']);
                
            }

            fclose($output);
            exit;
        }
 
    }
    function retirarAspas($obj) {
        $string = str_replace('"',"",$obj);
         return str_replace('\\',"",$string);
    }

}