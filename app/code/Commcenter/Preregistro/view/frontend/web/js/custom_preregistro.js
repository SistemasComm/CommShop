require([
    "jquery",
    "jquery.mask"
], function($) {
    $(document).ready(function() {


        // Aplica a máscara de CPF
        $('#cpf').mask('000.000.000-00');

        // Aplica a máscara de telefone
        $('#phone').mask('(00) 00000-0000');
    });
});
