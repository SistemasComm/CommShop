#!/bin/bash

# Verificar se o diretório atual contém o bin/magento (para confirmar que é uma instalação do Magento)
if [ ! -f "bin/magento" ]; then
  echo "Erro: Arquivo bin/magento não encontrado. Certifique-se de que está na pasta raiz do Magento."
  exit 1
fi


# Criar o diretório e o arquivo _import.less para Comm/default
echo "Criando o arquivo _import.less para o tema Comm/default..."
mkdir -p app/design/frontend/Comm/default/Magento_PageBuilder/web/css/source/content-type
if [ $? -eq 0 ]; then
  echo "Diretório app/design/frontend/Comm/default/Magento_PageBuilder/web/css/source/content-type criado ou já existe."
else
  echo "Erro ao criar o diretório para _import.less."
  exit 1
fi

# Criar o arquivo _import.less
cat > app/design/frontend/Comm/default/Magento_PageBuilder/web/css/source/content-type/_import.less <<EOL
// Importações padrão para Magento_PageBuilder content types

EOL
if [ $? -eq 0 ]; then
  echo "Arquivo _import.less criado com sucesso em app/design/frontend/Comm/default/Magento_PageBuilder/web/css/source/content-type/."
else
  echo "Erro ao criar o arquivo _import.less."
  exit 1
fi

# Ativar modo de manutenção
echo "Ativando modo de manutenção..."
php bin/magento maintenance:enable
if [ $? -eq 0 ]; then
  echo "Modo de manutenção ativado com sucesso."
else
  echo "Erro ao ativar o modo de manutenção."
  exit 1
fi


# forcando generated
mv generated generated++
rm -rf generated++

# Verificar se as pastas var e generated existem
if [ ! -d "var" ]; then
  echo "Aviso: Pasta 'var' não encontrada. Criando..."
  mkdir -p var
fi

if [ ! -d "generated" ]; then
  echo "Aviso: Pasta 'generated' não encontrada. Criando..."
  mkdir -p generated
fi

# Verificar permissões de escrita nas pastas var e generated
echo "Verificando permissões de escrita..."
if [ ! -w "var" ] || [ ! -w "generated" ]; then
  echo "Aviso: Sem permissão de escrita nas pastas var ou generated. Tentando corrigir..."
  chmod -R u+w var generated 2>/dev/null
  if [ $? -ne 0 ]; then
    echo "Erro: Não foi possível corrigir permissões. Tente executar o script com sudo."
    exit 1
  fi
fi

# Tentar limpar as pastas var e generated
echo "Limpando pastas var e generated..."
rm -rf var/* generated/* pub/static/frontend/* 2>error.log
if [ $? -eq 0 ]; then
  echo "Pastas var e generated limpas com sucesso."
else
  echo "Erro ao limpar as pastas var e/ou generated. Detalhes no arquivo error.log."
  cat error.log
  echo "Tentando forçar a exclusão com sudo..."
 sudo rm -rf var/* generated/* pub/static/frontend/* 2>>error.log
  if [ $? -eq 0 ]; then
    echo "Pastas var e generated limpas com sucesso usando sudo."
  else
    echo "Erro: Falha ao limpar mesmo com sudo. Verifique o arquivo error.log para detalhes."
    cat error.log
    #exit 1
  fi
fi



# Executar comandos do Magento
echo "Executando comandos de setup do Magento..."
php bin/magento setup:upgrade
if [ $? -eq 0 ]; then
  echo "Setup upgrade executado com sucesso."
else
  echo "Erro ao executar setup:upgrade."
  exit 1
fi

php bin/magento setup:di:compile
if [ $? -eq 0 ]; then
  echo "Compilação DI executada com sucesso."
else
  echo "Erro ao executar setup:di:compile."
  exit 1
fi

php bin/magento setup:static-content:deploy -f --exclude-theme Magento/blank
if [ $? -eq 0 ]; then
  echo "Deploy de conteúdo estático executado com sucesso."
else
  echo "Erro ao executar setup:static-content:deploy."
  exit 1
fi

# Limpar cache
php bin/magento cache:clean
if [ $? -eq 0 ]; then
  echo "Cache limpo com sucesso."
else
  echo "Erro ao limpar o cache."
  exit 1
fi

# Desativar modo de manutenção
echo "Desativando modo de manutenção..."
php bin/magento maintenance:disable
if [ $? -eq 0 ]; then
  echo "Modo de manutenção desativado com sucesso."
else
  echo "Erro ao desativar o modo de manutenção."
  exit 1
fi

# Atribuir permissões 777 às pastas var e generated
echo "Atribuindo permissões 777 às pastas var e generated..."
chmod -R 777 var/* generated/* pub/* 2>/dev/null
if [ $? -eq 0 ]; then
  echo "Permissões 777 aplicadas com sucesso às pastas var e generated."
else
  echo "Erro ao atribuir permissões. Tente executar com sudo."
 # exit 1
fi

echo "Script concluído com sucesso!"
