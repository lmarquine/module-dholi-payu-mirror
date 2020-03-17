# 1.0.5
## 17/03/2020

1. [](#changed)
    * Alteração nos arquivos de tradução
    
# 1.0.4
## 17/02/2020
1. [](#added)
    * Configuração para cancelar boletos gerados na quinta-feira
    
# 1.0.3
## 12/02/2020
1. [](#fixed)
    * Pedidos com status 'DECLINED' não estavam sendo cancelados.

# 1.0.2
## 13/01/2020
 
1. [](#added)
    * Abrir o link de impressão e link do pdf do boleto bancário em nova janela.

2. [](#fixed)
    * URL de notificação não recebia o parâmetro "reference_sale" no boleto bancário
    * Enum PayUOrderStatus não tinha o método isCancelled()
    
# 1.0.1
## 27/12/2019

1. [](#removed)
    * Opção de informar outra titularidade no Cartão de Crédito
    
2. [](#added)
    * Limpeza dos dados do Cartão de Crédito quando muda o Frete e o Cupom de Desconto
    * Compatibilidade com o One Step Checkout da Amasty
    
# 1.0.0
## 28/11/2019

1. [](#new)
    * Lançamento da versão PayU para Magento 2...