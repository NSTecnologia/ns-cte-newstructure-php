# NSSuiteClientPHP

Utilizando a NS API, este exemplo - criado em PHP - possui funcionalidades para consumir documentos fiscais eletrônicos em geral, como por exemplo: 
+ CTe; 

Simplificando todos os projetos utilizados em um único exemplo, deixando mais pratica e facil a integração com a NS API.

## Primeiros passos:

### Integrando ao sistema:

Para utilizar as funções de comunicação com a API, você precisa realizar os seguintes passos:

1. Extraia o conteúdo da pasta compactada que você baixou;
2. Copie para sua aplicação a pasta src, na qual contem todos as classes que serão utilizadas;
3. Abra o seu projeto e importe a pasta copiada.

Pronto! Agora, você já pode consumir a NS Suite API através do seu sistema. Todas as funcionalidades de comunicação foram implementadas na classe NSSuite.php.

------

## Emissão Sincrona:

### Realizando uma Emissão Sincrona:

Para realizar uma emissão completa de um CTe (utilizada para exemplo), você poderá utilizar a função emitirCTeSincrono da classe NSSuite. Veja abaixo sobre os parâmetros necessários, e um exemplo de chamada do método.

##### Parâmetros:

**ATENÇÃO:** o **token** também é um parâmetro necessário e você deve, primeiramente, defini-lo na classe **NSSuite.php**, como pode ver abaixo:

Parametros     | Descrição
:-------------:|:-----------
conteudo       | Conteúdo de emissão do documento.
tpConteudo     | Tipo de conteúdo que está sendo enviado. Valores possíveis: json, xml, txt
CNPJ           | CNPJ do emitente do documento.
tpDown         | Tipo de arquivos a serem baixados.Valores possíveis: <ul> <li>**X** - XML</li> <li>**J** - JSON</li> <li>**P** - PDF</li> <li>**XP** - XML e PDF</li> <li>**JP** - JSON e PDF</li> </ul> 
tpAmb          | Ambiente onde foi autorizado o documento.Valores possíveis:<ul> <li>1 - produção</li> <li>2 - homologação</li> </ul>
caminho        | Caminho onde devem ser salvos os documentos baixados.
exibeNaTela    | Se for baixado, exibir o PDF na tela após a autorização.Valores possíveis: <ul> <li>**True** - será exibido</li> <li>**False** - não será exibido</li> </ul> 

##### Exemplo de chamada:

Após ter todos os parâmetros listados acima, você deverá fazer a chamada da função. Veja o código de exemplo abaixo:

      $retorno = $NSSuite->emitirCTeSincrono($conteudo, $tpConteudo, $cnpjEmit, $tpDown, $tpAmb, $caminho, $exibeNaTela);
      echo $retorno;

A função emitirCTeSincrono fará o envio, a consulta e download do documento, utilizando as funções emitirDocumento, consultarStatusProcessamento e downloadDocumentoESalvar, presentes na classe NSSuite.php. Por isso, o retorno será um JSON com os principais campos retornados pelos métodos citados anteriormente. No exemplo abaixo, veja como tratar o retorno da função emitirCTeSincrono:

##### Exemplo de tratamento de retorno:

O JSON retornado pelo método terá os seguintes campos: statusEnvio, statusConsulta, statusDownload, cStat, chCTe, nProt, motivo, nsRec, erros. Veja o exemplo abaixo:

    {
        "statusEnvio": "200",
        "statusConsulta": "200",
        "statusDownload": "200",
        "cStat": "100",
        "chCTe": "43181007364617000135570000000119741004621864",
        "nProt": "143180007036833",
        "motivo": "Autorizado o uso do CT-e",
        "nsRec": "313022",
        "erros": ""
    }
      
Confira um código para tratamento do retorno, no qual pegará as informações dispostas no JSON de Retorno disponibilizado:


    $resposta = $NSSuite->emitirCTeSincrono($conteudo, $tpConteudo, $cnpjEmit, $tpDown, $tpAmb, $caminho, $exibeNaTela);

    $statusEnvio = $resposta['statusEnvio'];
    $statusConsulta = $resposta['statusConsulta'];
    $statusDownload = $resposta['statusDownload'];
    $cStat = $resposta['cStat'];
    $chCTe = $resposta['chCTe'];
    $nProt = $resposta['nProt'];
    $motivo = $resposta['motivo'];
    $nsNRec = $resposta['nsRec'];
    $erros = $resposta['erros'];

    if ($statusEnvio == 200 || $statusEnvio == -6){
        if ($statusConsulta == 200){
            if ($cStat == 100){
                echo $motivo;
                if ($statusDownload != 200){
                    echo 'Erro Download';
                }
            }else{
                echo $motivo;
            }
        }else{
            echo $motivo . '<br>' . $erros;
        }
    }else{
        echo $motivo . '<br>' . $erros;
    }
-----

## Cancelamento de Documento:

### Realizando um Cancelamento:

Utilizando CTe como exemplo para o cancelamento deve-se ter em mente que você deverá usar a função cancelarDocumentoESalvar da classe NSSuite. Veja abaixo sobre os parâmetros necessários, e um exemplo de chamada do método.

##### Parâmetros:

**ATENÇÃO:** o **token** também é um parâmetro necessário e você deve, primeiramente, defini-lo na classe **NSSuite.php**, como pode ver abaixo:

Parametros     | Descrição
:-------------:|:-----------
**modelo**            | Conteúdo de emissão do documento.<ul> <li>"57" (CTe);</li> </ul>
**CancelarReq**       | JSON contendo as informações de uma requisição de cancelamento de documento
**DownloadEventoReq** | JSON contendo as informações de uma requisição de Download de Evento
**caminho**           | Caminho onde devem ser salvos os documentos baixados.
**chave**             | Ambiente onde foi autorizado o documento.Valores possíveis:<ul> <li>1 - produção</li> <li>2 - homologação</li> </ul> 
**exibeNaTela**       | Se for baixado, exibir o PDF na tela após a autorização.Valores possíveis: <ul> <li>**True** - será exibido</li> <li>**False** - não será exibido</li> </ul> 

##### Exemplo de chamada:

Após ter todos os parâmetros listados acima, você deverá fazer a chamada da função. Veja o código de exemplo abaixo:

    $NSSuite = new NSSuite;

    $cancelarReqNFe = new CancelarReqCTe();
    $cancelarReqNFe->chNFe = '43190307364617000135570000000130621004621863';
    $cancelarReqNFe->dhEvento='2019-03-15T15:37:14-03:00';
    $cancelarReqNFe->nProt = '143190000501923';
    $cancelarReqNFe->tpAmb = '2';
    $cancelarReqNFe->xJust = 'TESTE DE CANCELAMENTO INTEGRAÇÃO NS';

    $down = new DownloadEventoReqCTe();
    $down->tpDown = 'XP';
    $down->tpEvento= 'CANC';
    $down->nSeqEvento = '1';
    $down->tpAmb = '2';
    $down->chNFe = '43190307364617000135570000000130621004621863';
    $retorno = $NSSuite->cancelarDocumentoESalvar('55', $cancelarReqCte, $down, './Notas', '43190307364617000135570000000130621004621863', true);
    
A função **cancelarDocumentoESalvar** fará o cancelamento de qualquer documento que possa ser cancelado e fazendo o download do evento feito, neste caso hipotético, um CTe, utilizando as funções cancelarDocumento e downloadEventoESalvar, presentes na classe NSSuite.php. Dessa forma, o retorno será um JSON com os principais campos retornados pelos métodos citados anteriormente. No exemplo abaixo, veja o retorno da nossa API em um cancelamento:

##### Exemplo de retorno de cancelamento:

    {
      "status": 135,
      "motivo": "CT-e cancelada com sucesso",
      "retEvento": {
        "cStat": 135,
        "xMotivo": "Evento registrado e vinculado a CT-e",
        "chNFe": "43190307364617000135570000000130621004621863",
        "dhRegEvento": "2019-03-15T15:37:14-03:00",
        "nProt": "143190000501923"
      }
    }

-----

## Carta de Correção(CC):

### Realizando uma Correção de Documento:

Utilizando CTe como exemplo para a criação de uma carta de correção, deve-se ter em mente que você deverá usar a função corrigirDocumentoESalvar da classe NSSuite. Veja abaixo sobre os parâmetros necessários, e um exemplo de chamada do método.

##### Parâmetros:

**ATENÇÃO:** o **token** também é um parâmetro necessário e você deve, primeiramente, defini-lo na classe **NSSuite.php**, como pode ver abaixo:

Parametros     | Descrição
:-------------:|:-----------
**modelo**            | Conteúdo de emissão do documento.<ul> <li>"57" (CTe);</li> </ul>
**CorrigirReq**       | JSON contendo as informações de uma requisição de carta de correção
**DownloadEventoReq** | JSON contendo as informações de uma requisição de Download de Evento
**caminho**           | Caminho onde devem ser salvos os documentos baixados.
**chave**             | Ambiente onde foi autorizado o documento.Valores possíveis:<ul> <li>1 - produção</li> <li>2 - homologação</li> </ul> 
**nSeqEvento**        | Número sequencial do evento
**exibeNaTela**       | Se for baixado, exibir o PDF na tela após a autorização.Valores possíveis: <ul> <li>**True** - será exibido</li> <li>**False** - não será exibido</li> </ul> 

##### Exemplo de chamada:

Após ter todos os parâmetros listados acima, você deverá fazer a chamada da função. Veja o código de exemplo abaixo:

    $cceTeste  = new CorrigirReqCTe();
    $cceTeste->chNFe = '43190207364617000135570000000129281004621862';
    $cceTeste->dhEvento = '2019-03-06T12:00:00-03:00';
    $cceTeste->tpAmb = '2';
    $cceTeste->nSeqEvento = '1';
    $cceTeste->xCorrecao = 'CC-e realizada para teste de integração';

    $downTeste = new DownloadEventoReqCTe();
    $downTeste->chNFe = '143190207364617000135570000000129281004621862';
    $downTeste->tpAmb = '2';
    $downTeste->nSeqEvento = '1';
    $downTeste->tpDown = 'XP';
    $downTeste->tpEvento = 'CCE';

    $retorno = NSSuite->corrigirDocumentoESalvar('57', $cceTeste, $downTeste, './Notas', '43190207364617000135570000000129281004621862', '1', true);
    
A função corrigirDocumentoESalvar irá vincular um CCe (carta de correção) ao projeto selecionado, neste caso hipotético, à um CTe, utilizando as funções corrigirDocumento e downloadEventoESalvar, presentes na classe NSSuite.php. Dessa forma, o retorno será um JSON com os principais campos retornados pelos métodos citados anteriormente. No exemplo abaixo, veja o retorno da nossa API em uma CCe:

##### Exempo de retorno de correção de documento:

    {
      "status": 200,
      "motivo": "CC-e vinculada com sucesso",
      "retEvento": {
        "cStat": 135,
        "xMotivo": "Evento registrado e vinculado a CT-e",
        "chNFe": "43190207364617000135570000000129281004621862",
        "dhRegEvento": "2019-03-06T12:00:50-03:00",
        "nProt": "143190000330112"
      }
    }


-----

## Inutilizar Numeração:

### Realizando uma Inutilização de Numeração de um Documento:

Utilizando CTe como exemplo para a inutilização de numeração, deverá ser utilizada a função inutilizarNumeracaoESalvar da classe NSSuite. Veja abaixo sobre os parâmetros necessários, e um exemplo de chamada do método.

##### Parâmetros:

**ATENÇÃO:** o **token** também é um parâmetro necessário e você deve, primeiramente, defini-lo na classe **NSSuite.php**, como pode ver abaixo:

Parametros     | Descrição
:-------------:|:-----------
**modelo**            | Conteúdo de emissão do documento.<ul> <li>"57" (CTe);</li> </ul>
**InutilizarReq**     | JSON contendo as informações de uma requisição de inutilização de numeração
**caminho**           | Caminho onde devem ser salvos os documentos baixados.

##### Exemplo de chamada:

Após ter todos os parâmetros listados acima, você deverá fazer a chamada da função. Veja o código de exemplo abaixo:

      $NSSuite = new NSSuite;
      $inutilizarReq = new InutilizarReqCTe();
      $inutilizarReq->cUF = '43';
      $inutilizarReq->CNPJ = '07364617000135';
      $inutilizarReq->tpAmb = '2';
      $inutilizarReq->ano = '20';
      $inutilizarReq->serie = '20';
      $inutilizarReq->nNFIni = '9996'
      $inutilizarReq->nNFFin = '9996'
      $inutilizarReq->xJust = 'Inutilizacao realizada para teste de integracao';

      $caminho = '.\Notas';
      $retorno = $NSSuite->inutilizarNumeracaoESalvar('57', $inutilizarReq, $caminho);
    
A função inutilizarNumeracaoESalvar irá inutilizar a numeração do documento, neste caso hipotético, à um CTe, presente na classe NSSuite.php. Dessa forma, o retorno será um JSON com os principais campos retornados pelos métodos citados anteriormente. No exemplo abaixo, veja o retorno da nossa API de um inutilização de numeração:

##### Exempo de retorno de correção de documento:

            {
                  "status": 200,
                  "motivo": "Consulta realizada com sucesso",
                  "retornoInutNFe": {
                        "cStat": "102",
                        "xMotivo": "Inutilizacao de numero homologado",
                        "chave": "43111111111111111157111111111111111111111",
                        "tpAmb": 2,
                        "dhRecbto": "2016-12-07T17:10:52-02:00",
                        "nProt": "143160001541466",
                        "xmlInut": "<?xml version=\"1.0\" encoding=\"utf-8\"?><ProcInutCTe versao=\"3.10\" xmlns=\"http://www.portalfiscal.inf.br/nfe\">...</ProcInutCTe>"
                  }
            }
    
 

![Ns](https://nstecnologia.com.br/blog/wp-content/uploads/2018/11/ns%C2%B4tecnologia.png) | Obrigado pela atenção!
