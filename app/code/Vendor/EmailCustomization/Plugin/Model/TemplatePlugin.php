<?php
namespace Vendor\EmailCustomization\Plugin\Model;

class TemplatePlugin
{
    public function beforeGetProcessedTemplate($subject, $variables = [])
    {
        if (array_key_exists('comment', $variables)) {
            // Passe $variables por referência para que possa ser modificado
            $variables['comment'] = $this->customizeComment($variables['comment'], $variables);
        }

        return [$variables];
    }


    private function customizeComment($comment, &$variables)
    {
        // Captura e remove a data
        preg_match('/Data: ([^,]+), /', $comment, $dataMatches);
    if (!empty($dataMatches)) {
        try {
            // Tenta criar um objeto DateTime com a data capturada
            $dateObject = \DateTime::createFromFormat('Y-m-d H:i:s', $dataMatches[1]);
            if ($dateObject) {
                // Formata a data no formato desejado e armazena na variável
                $variables['data'] = $dateObject->format('d/m/Y');
            } else {
                // Se a criação do objeto DateTime falhar, mantenha a data original
                $variables['data'] = $dataMatches[1];
            }
        } catch (\Exception $e) {
            // Se ocorrer algum erro, você pode decidir como lidar com isso
            // Por exemplo, logar o erro e usar a data original
            error_log($e->getMessage());
            $variables['data'] = $dataMatches[1];
        }
        // Remove a data do comentário
        $comment = str_replace($dataMatches[0], '', $comment);
    }

        // Captura e remove o link da NFe se não for 'null'
        preg_match('/Link Nfe: (?!null)[^,]+, /', $comment, $linkNfeMatches);
        if (!empty($linkNfeMatches)) {
            $variables['link_nfe'] = $linkNfeMatches[0];
            $comment = str_replace($linkNfeMatches[0], '', $comment);
        }

        $comment = preg_replace('/Link Nfe: [^,]+, /', '', $comment);
        

        // Captura e remove o número da nota
        preg_match('/Número Nota: (\d+), /', $comment, $notaMatches);
        if (!empty($notaMatches)) {
            $variables['numero_nota'] = $notaMatches[1];
            $comment = str_replace($notaMatches[0], '', $comment);
        }

        // Captura e remove a série
        preg_match('/Serie: (\d+)/', $comment, $serieMatches);
        if (!empty($serieMatches)) {
            $variables['serie'] = $serieMatches[1];
            $comment = str_replace($serieMatches[0], '', $comment);
        }
        $comment = preg_replace('/, \.$/', '', $comment);
        return $comment;
    }

}
