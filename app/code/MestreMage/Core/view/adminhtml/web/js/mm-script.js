/*
 * @author Mestre Magento
 * @copyright Copyright (c) Mestre Magento (https://www.modulomagento.com.br/)
 * @license https://www.modulomagento.com.br/termos-condicoes/
 * @support contato@modulomagento.com.br
 */

window.onload = function(){

    if(test_zip_dest = document.querySelector('tr#row_modulo_correios_section_settings_con_test_zip_dest')){
        test_zip_dest.insertAdjacentHTML('afterend', '<a href="javascript:searchPostCode()">Verificar</a> <tr id="return_search_mm"></tr>');
    }

}
function searchPostCode(){
        var contract_cod_adm = document.getElementById('modulo_correios_section_settings_contract_cod_adm').value;
        var contract_pass_adm = document.getElementById('modulo_correios_section_settings_contract_pass_adm').value;
        var con_test_zip_orig = document.getElementById('modulo_correios_section_settings_con_test_zip_orig').value;
        var con_test_zip_dest = document.getElementById('modulo_correios_section_settings_con_test_zip_dest').value;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

               var spl_content =  this.responseText.split("||");
             var r = '';

             if(spl_content[1].length > 10){
                r += '<p>Ops! O webservice do Correios retornou a mensagem abaixo:</p>';
                r += '<p>'+spl_content[0]+'</p>';
                r += '<p>Entre em contato com o Correios ligue para 3003 0800 (capitais e regiões metropolitanas) ou 0800 200 0800 (demais localidades).</p>';
                r += '<p>No link abaixo você pode constatar o retorno do Correios:</p>';
                r += '<p><a href="'+spl_content[1]+'" target="_blank" >'+spl_content[1]+'</a></p>';
             }else{
                r += '<p>'+spl_content[0]+'</p>';
             }


             r = r.replace('"',"");
             r = r.replace('"',"");

                document.getElementById("return_search_mm").innerHTML = r;
            }
        };
        xmlhttp.open("GET", window.location.origin+"/rest/all/V1/mestremage-correios/searchpostcode?param=1&contract_cod_adm="+contract_cod_adm+"&contract_pass_adm="+contract_pass_adm+"&con_test_zip_orig="+con_test_zip_orig+"&con_test_zip_dest="+con_test_zip_dest, true);
        xmlhttp.send();
    


}