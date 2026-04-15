<?php

require_once(__DIR__ . '/Compartilhados/Endpoints.php');
require_once(__DIR__ . '/Compartilhados/Parametros.php');
require_once(__DIR__ . '/Compartilhados/Genericos.php');

foreach (glob(__DIR__ . '/Requisicoes/_Genericos/*.php') as $filename) {
    include_once($filename);
}

// Carregando apenas as dependências de CTe
require_once(__DIR__ . '/Requisicoes/CTe/ConsStatusProcessamentoReqCTe.php');
require_once(__DIR__ . '/Requisicoes/CTe/DownloadReqCTe.php');
require_once(__DIR__ . '/Requisicoes/CTe/InfGTVReqCTe.php');
require_once(__DIR__ . '/Retornos/CTe/EmitirSincronoRetCTe.php');

class NSSuiteCTe {

    private $token;
    private $parametros;
    private $endpoints;
    private $genericos;

    public function __construct($token = "SEU_TOKEN_AQUI") {
        $this->parametros = new Parametros(5);
        $this->endpoints = new Endpoints;
        $this->genericos = new Genericos;
        $this->token = $token;
    }

    private function enviaConteudoParaAPI($conteudoAEnviar, $url, $tpConteudo) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $conteudoAEnviar);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = array('X-AUTH-TOKEN: ' . $this->token);
        if ($tpConteudo == 'json') array_push($headers, 'Content-Type: application/json');
        else if ($tpConteudo == 'xml') array_push($headers, 'Content-Type: application/xml');
        else array_push($headers, 'Content-Type: text/plain');

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    // Emissão Normal ou OS
    public function emitirCTeSincrono($conteudo, $mod, $tpConteudo, $CNPJ, $tpDown, $tpAmb, $caminho, $exibeNaTela) {
        $urlEnvio = ($mod == '67') ? $this->endpoints->CTeOSEnvio : $this->endpoints->CTeEnvio;
        return $this->processarEmissao($urlEnvio, $mod, $conteudo, $tpConteudo, $CNPJ, $tpDown, $tpAmb, $caminho, $exibeNaTela);
    }

    // Emissão Simplificada
    public function emitirCTeSimplificadoSincrono($conteudo, $tpConteudo, $CNPJ, $tpDown, $tpAmb, $caminho, $exibeNaTela) {
        $urlEnvio = "https://cte.v2.ns.eti.br/cte/issuesimp";
        return $this->processarEmissao($urlEnvio, '57', $conteudo, $tpConteudo, $CNPJ, $tpDown, $tpAmb, $caminho, $exibeNaTela);
    }

    private function processarEmissao($urlEnvio, $modelo, $conteudo, $tpConteudo, $CNPJ, $tpDown, $tpAmb, $caminho, $exibeNaTela) {
        $this->genericos->gravarLinhaLog($modelo, '[EMISSAO_SINCRONA_INICIO]');
        
        $this->genericos->gravarLinhaLog($modelo, '[ENVIA_DADOS]');
        $this->genericos->gravarLinhaLog($modelo, $conteudo);

        $resposta = $this->enviaConteudoParaAPI($conteudo, $urlEnvio, $tpConteudo);
        
        $this->genericos->gravarLinhaLog($modelo, '[ENVIA_RESPOSTA]');
        $this->genericos->gravarLinhaLog($modelo, json_encode($resposta));

        $statusEnvio = $resposta['status'];
        // NSRec conforme solicitado (nsRec ou nsNRec como fallback)
        $nsRec = $resposta['nsRec'] ?? $resposta['nsNRec'] ?? null; 

        if ($statusEnvio == 200 || $statusEnvio == -6) {
            $tentativas = 0;
            $maxTentativas = 5;

            do {
                sleep($this->parametros->TEMPO_ESPERA);
                
                $consStatus = new ConsStatusProcessamentoReqCTe;
                $consStatus->CNPJ = $CNPJ;
                $consStatus->nsRec = $nsRec; 
                $consStatus->tpAmb = $tpAmb;

                $respostaCons = $this->consultarStatusProcessamento($modelo, $consStatus);
                $cStat = $respostaCons['cStat'] ?? null;
                $xMotivo = $respostaCons['xMotivo'] ?? '';

                // Lógica de Reconsulta se estiver em processamento
                if ($cStat == "0" || strpos($xMotivo, "Documento em processamento") !== false) {
                    $tentativas++;
                    continue; 
                }
                break;

            } while ($tentativas < $maxTentativas);

            if ($cStat == 100 || $cStat == 150) {
                $chCTe = $respostaCons['chCTe'];
                $downloadReq = new DownloadReqCTe();
                $downloadReq->chCTe = $chCTe;
                $downloadReq->CNPJ = $CNPJ;
                $downloadReq->tpAmb = $tpAmb;
                $downloadReq->tpDown = $tpDown;

                $this->downloadDocumentoESalvar($modelo, $downloadReq, $caminho, $chCTe . '-CTe', $exibeNaTela);
                
                $retorno = json_encode($respostaCons, JSON_UNESCAPED_UNICODE);
                $this->genericos->gravarLinhaLog($modelo, '[JSON_RETORNO]');
                $this->genericos->gravarLinhaLog($modelo, $retorno);
                $this->genericos->gravarLinhaLog($modelo, '[EMISSAO_SINCRONA_FIM]');
                
                return $retorno;
            }
            
            $retorno = json_encode($respostaCons ?? $resposta, JSON_UNESCAPED_UNICODE);
            $this->genericos->gravarLinhaLog($modelo, '[JSON_RETORNO]');
            $this->genericos->gravarLinhaLog($modelo, $retorno);
            $this->genericos->gravarLinhaLog($modelo, '[EMISSAO_SINCRONA_FIM]');
            
            return $retorno;
        }
        
        $retorno = json_encode($resposta, JSON_UNESCAPED_UNICODE);
        $this->genericos->gravarLinhaLog($modelo, '[JSON_RETORNO]');
        $this->genericos->gravarLinhaLog($modelo, $retorno);
        $this->genericos->gravarLinhaLog($modelo, '[EMISSAO_SINCRONA_FIM]');
        
        return $retorno;
    }

    public function consultarStatusProcessamento($modelo, $consStatusProcessamentoReq) {
        $url = $this->endpoints->CTeConsStatusProcessamento;
        $json = json_encode((array) $consStatusProcessamentoReq, JSON_UNESCAPED_UNICODE);

        $this->genericos->gravarLinhaLog($modelo, '[CONSULTA_DADOS]');
        $this->genericos->gravarLinhaLog($modelo, $json);

        $resposta = $this->enviaConteudoParaAPI($json, $url, 'json');

        $this->genericos->gravarLinhaLog($modelo, '[CONSULTA_RESPOSTA]');
        $this->genericos->gravarLinhaLog($modelo, json_encode($resposta));

        return $resposta;
    }

public function downloadDocumentoESalvar($modelo, $downloadReq, $caminho, $nome, $exibeNaTela) {
        $url = $this->endpoints->CTeDownload;
        $json = json_encode((array) $downloadReq, JSON_UNESCAPED_UNICODE);
        
        $this->genericos->gravarLinhaLog($modelo, '[DOWNLOAD_DADOS]');
        $this->genericos->gravarLinhaLog($modelo, $json);

        $resposta = $this->enviaConteudoParaAPI($json, $url, 'json');
        $status = $resposta['status'];

        if ($status == 200) {
            // CORREÇÃO: Cria a pasta de forma recursiva e normaliza a barra final
            if (!file_exists($caminho)) {
                mkdir($caminho, 0777, true);
            }
            $dir = rtrim($caminho, '/\\') . DIRECTORY_SEPARATOR;

            if (isset($resposta['xml'])) $this->genericos->salvaXML($resposta['xml'], $dir, $nome);
            if (isset($resposta['pdf'])) {
                $this->genericos->salvaPDF($resposta['pdf'], $dir, $nome);
                if ($exibeNaTela) $this->genericos->exibirNaTela($dir, $nome);
            }
        }
        return $resposta;
    }

    public function downloadEventoESalvar($modelo, $downloadEventoReq, $caminho, $chave, $nSeqEvento, $exibeNaTela) {
        $url = $this->endpoints->CTeDownloadEvento;
        $json = json_encode((array) $downloadEventoReq, JSON_UNESCAPED_UNICODE);
        
        $this->genericos->gravarLinhaLog($modelo, '[DOWNLOAD_EVENTO_DADOS]');
        $this->genericos->gravarLinhaLog($modelo, $json);

        $resposta = $this->enviaConteudoParaAPI($json, $url, 'json');
        $status = $resposta['status'];

        if ($status == 200) {
            // CORREÇÃO: Normalização do diretório
            if (!file_exists($caminho)) {
                mkdir($caminho, 0777, true);
            }
            $dir = rtrim($caminho, '/\\') . DIRECTORY_SEPARATOR;
            $nome = $chave . '-procEvenCTe' . $nSeqEvento;

            if (isset($resposta['xml'])) $this->genericos->salvaXML($resposta['xml'], $dir, $nome);
            if (isset($resposta['pdf'])) {
                $this->genericos->salvaPDF($resposta['pdf'], $dir, $nome);
                if ($exibeNaTela) $this->genericos->exibirNaTela($dir, $nome);
            }
        }
        return $resposta;
    }

    // Cancelamento com download automático
    public function cancelarCTeESalvar($modelo, $cancelarReq, $downloadEventoReq, $caminho, $chave, $exibeNaTela) {
        $json = json_encode((array) $cancelarReq, JSON_UNESCAPED_UNICODE);
        
        $this->genericos->gravarLinhaLog($modelo, '[CANCELAMENTO_DADOS]');
        $this->genericos->gravarLinhaLog($modelo, $json);

        $resposta = $this->enviaConteudoParaAPI($json, $this->endpoints->CTeCancelamento, 'json');

        $this->genericos->gravarLinhaLog($modelo, '[CANCELAMENTO_RESPOSTA]');
        $this->genericos->gravarLinhaLog($modelo, json_encode($resposta));

        if ($resposta['status'] == 200 || ($resposta['cStat'] ?? '') == '135') {
            $this->downloadEventoESalvar($modelo, $downloadEventoReq, $caminho, $chave, '1', $exibeNaTela);
        }
        return $resposta;
    }

    // CCe com download automático
    public function corrigirCTeESalvar($modelo, $corrigirReq, $downloadEventoReq, $caminho, $chave, $nSeqEvento, $exibeNaTela) {
        $json = json_encode((array) $corrigirReq, JSON_UNESCAPED_UNICODE);
        
        $this->genericos->gravarLinhaLog($modelo, '[CCE_DADOS]');
        $this->genericos->gravarLinhaLog($modelo, $json);

        $resposta = $this->enviaConteudoParaAPI($json, $this->endpoints->CTeCCe, 'json');

        $this->genericos->gravarLinhaLog($modelo, '[CCE_RESPOSTA]');
        $this->genericos->gravarLinhaLog($modelo, json_encode($resposta));

        if ($resposta['status'] == 200) {
            $this->downloadEventoESalvar($modelo, $downloadEventoReq, $caminho, $chave, $nSeqEvento, $exibeNaTela);
        }
        return $resposta;
    }

    public function downloadEventoE ($modelo, $downloadEventoReq, $caminho, $chave, $nSeqEvento, $exibeNaTela) {
        $url = $this->endpoints->CTeDownloadEvento;
        $json = json_encode((array) $downloadEventoReq, JSON_UNESCAPED_UNICODE);
        
        $this->genericos->gravarLinhaLog($modelo, '[DOWNLOAD_EVENTO_DADOS]');
        $this->genericos->gravarLinhaLog($modelo, $json);

        $resposta = $this->enviaConteudoParaAPI($json, $url, 'json');
        $status = $resposta['status'];

        if(($status != 200) && ($status != 100)){
            $this->genericos->gravarLinhaLog($modelo, '[DOWNLOAD_EVENTO_RESPOSTA]');
            $this->genericos->gravarLinhaLog($modelo, json_encode($resposta));
        }else{
            $this->genericos->gravarLinhaLog($modelo, '[DOWNLOAD_EVENTO_STATUS]');
            $this->genericos->gravarLinhaLog($modelo, $status);
        }

        if ($status == 200) {
            if (!file_exists($caminho)) mkdir($caminho, 0777, true);
            $dir = (substr($caminho, -1) != DIRECTORY_SEPARATOR) ? $caminho . DIRECTORY_SEPARATOR : $caminho;
            $nome = $chave . '-procEvenCTe' . $nSeqEvento;

            if (isset($resposta['xml'])) $this->genericos->salvaXML($resposta['xml'], $dir, $nome);
            if (isset($resposta['pdf'])) {
                $this->genericos->salvaPDF($resposta['pdf'], $dir, $nome);
                if ($exibeNaTela) $this->genericos->exibirNaTela($dir, $nome);
            }
        }
        return $resposta;
    }
}