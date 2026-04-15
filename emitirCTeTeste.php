<?php
require_once(__DIR__ . '/NSSuite.php');
require_once(__DIR__ . '/Layout/CTeJSON.php');

$NSSuite = new NSSuiteCTe("ADQWREQW561D32AWS1D6");
$CTeJSON = new CTeJSON;

// Instanciação das classes de nível superior
$cte = new CTe;
$infCte = new InfCte;
$ide = new Ide;
$emit = new Emit;
$enderEmit = new EnderEmit;
$rem = new Remetente;
$enderReme = new EnderReme;
$dest = new Dest;
$enderDest = new EnderDest;

// --- NOVAS INSTÂNCIAS (EXPEDIDOR E RECEBEDOR) ---
$exped = new Exped;
$enderExped = new EnderExped;
$receb = new Receb;
$enderReceb = new EnderReceb;
// ------------------------------------------------

$vPrest = new VPrest;
$imp = new Imp;
$icms = new ICMS;
$icms90 = new ICMS90;
$infCTeNorm = new InfCTeNorm;
$infCarga = new InfCarga;
$infQ = new InfQ;
$infDoc = new InfDoc;
$infNFe = new InfNFe;
$infModal = new InfModal;
$rodo = new Rodo;

// Hierarquia Base
$CTeJSON->CTe = $cte;
$cte->infCte = $infCte;
$infCte->versao = "4.00";

// 1. Identificação (ide)
$infCte->ide = $ide;
$ide->cUF = "43";
$ide->cCT = "00002807";
$ide->CFOP = "6353";
$ide->natOp = "SERVICO DE TRANSPORTE";
$ide->mod = "57";
$ide->serie = "99";
$ide->nCT = "103"; // MANTIDO CONFORME SOLICITADO
$ide->dhEmi = date("Y-m-d\TH:i:sP"); // Mantido dinâmico ao invés de literal "{{dhAtual}}"
$ide->tpImp = "1";
$ide->tpEmis = "1";
$ide->cDV = "4";
$ide->tpAmb = "2";
$ide->tpCTe = "0";
$ide->procEmi = "0";
$ide->verProc = "1.02|NS_API";
$ide->cMunEnv = "4308607";
$ide->xMunEnv = "GARIBALDI";
$ide->UFEnv = "RS";
$ide->modal = "01";
$ide->tpServ = "0";
$ide->cMunIni = "4302105";
$ide->xMunIni = "BENTO GONCALVES";
$ide->UFIni = "RS";
$ide->cMunFim = "2800605";
$ide->xMunFim = "BARRA DOS COQUEIROS";
$ide->UFFim = "SE";
$ide->retira = "1";
$ide->indIEToma = "1";

// Toma3
$toma3 = new stdClass();
$toma3->toma = "3";
$ide->toma3 = $toma3;

// 2. Emitente (emit)
$infCte->emit = $emit;
$emit->CNPJ = "07364617000135";
$emit->IE = "0170108708";
$emit->xNome = "CTE EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL";
$emit->xFant = "teste";
$emit->CRT = "1";
$emit->email = "michel.dummer@nstecnologia.com.br";

$emit->enderEmit = $enderEmit;
$enderEmit->xLgr = "ANTONIO DURO";
$enderEmit->nro = "870";
$enderEmit->xBairro = "CENTRO";
$enderEmit->cMun = "4303509";
$enderEmit->xMun = "CAMAQUA";
$enderEmit->CEP = "96180000";
$enderEmit->UF = "RS";
$enderEmit->fone = "5136712053";

// 3. Remetente (rem)
$infCte->rem = $rem;
$rem->CNPJ = "46028383000107";
$rem->IE = "00000006311202";
$rem->xNome = "CTE EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL";
$rem->fone = "005434552000";
$rem->email = "michel.dummer@nstecnologia.com.br";

$rem->enderReme = $enderReme;
$enderReme->xLgr = "Avenida Prefeito Chiquilito Erse";
$enderReme->nro = "5475";
$enderReme->xBairro = "Nova Esperança";
$enderReme->cMun = "1100205";
$enderReme->xMun = "Porto Velho";
$enderReme->CEP = "76822146";
$enderReme->UF = "RO";
$enderReme->cPais = "1058";
$enderReme->xPais = "BRASIL";

// 4. Destinatário (dest)
$infCte->dest = $dest;
$dest->CNPJ = "46028383000107";
$dest->IE = "00000006311202";
$dest->xNome = "CTE EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL";
$dest->email = "michel.dummer@nstecnologia.com.br";

$dest->enderDest = $enderDest;
$enderDest->xLgr = "Avenida Prefeito Chiquilito Erse";
$enderDest->nro = "5475";
$enderDest->xBairro = "Nova Esperança";
$enderDest->cMun = "1100205";
$enderDest->xMun = "Porto Velho";
$enderDest->CEP = "76822146";
$enderDest->UF = "RO";
$enderDest->cPais = "1058";
$enderDest->xPais = "BRASIL";

// 5. Expedidor (exped) - NOVO BLOCO
$infCte->exped = $exped;
$exped->CNPJ = "46028383000107";
$exped->IE = "00000006311202";
$exped->xNome = "CTE EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL";

$exped->enderExped = $enderExped;
$enderExped->xLgr = "Avenida Prefeito Chiquilito Erse";
$enderExped->nro = "5475";
$enderExped->xBairro = "Nova Esperança";
$enderExped->cMun = "1100205";
$enderExped->xMun = "Porto Velho";
$enderExped->CEP = "76822146";
$enderExped->UF = "RO";
$enderExped->cPais = "1058";
$enderExped->xPais = "BRASIL";

// 6. Recebedor (receb) - NOVO BLOCO
$infCte->receb = $receb;
$receb->CNPJ = "46028383000107";
$receb->IE = "00000006311202";
$receb->xNome = "CTE EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL";

$receb->enderReceb = $enderReceb;
$enderReceb->xLgr = "Avenida Prefeito Chiquilito Erse";
$enderReceb->nro = "5475";
$enderReceb->xBairro = "Nova Esperança";
$enderReceb->cMun = "1100205";
$enderReceb->xMun = "Porto Velho";
$enderReceb->CEP = "76822146";
$enderReceb->UF = "RO";
$enderReceb->cPais = "1058";
$enderReceb->xPais = "BRASIL";

// 7. Valores da Prestação (vPrest)
$infCte->vPrest = $vPrest;
$vPrest->vTPrest = "164.74";
$vPrest->vRec = "164.74";

$comp1 = new stdClass(); $comp1->xNome = "PESO";    $comp1->vComp = "304.00";
$comp2 = new stdClass(); $comp2->xNome = "SEC/CAT"; $comp2->vComp = "34.34";
$comp3 = new stdClass(); $comp3->xNome = "ADVALOR"; $comp3->vComp = "5.00";
$comp4 = new stdClass(); $comp4->xNome = "PEDAGIO"; $comp4->vComp = "4.31";
$vPrest->Comp = [$comp1, $comp2, $comp3, $comp4];

// 8. Impostos (imp)
$infCte->imp = $imp;
$imp->ICMS = $icms;
$icms->ICMS90 = $icms90;
$icms90->CST = "90";
$icms90->pRedBC = "1.00";
$icms90->vBC = "164.74";
$icms90->pICMS = "1.00";
$icms90->vICMS = "1.64";
$icms90->vICMSDeson = "1.00";
$icms90->cBenef = "123456";

// 9. Informações da Carga e Documentos (infCTeNorm)
$infCte->infCTeNorm = $infCTeNorm;
$infCTeNorm->infCarga = $infCarga;
$infCarga->vCarga = "1627.47";
$infCarga->proPred = "VINHOS";
$infCarga->xOutCat = "VINHOS";

$infCarga->infQ = $infQ;
$infQ->cUnid = "03";
$infQ->tpMed = "PESO BRUTO";
$infQ->qCarga = "30.0000";

// Documentos (NFe)
$infCTeNorm->infDoc = $infDoc;
$infDoc->infNFe = $infNFe;
$infNFe->chave = "43260207364617000135550670000000941847559300";

// 10. Modal (infModal)
$CTeJSON->infModal = $infModal;
$infModal->versaoModal = "4.00";
$infModal->rodo = $rodo;
$rodo->RNTRC = "09549369";

// Envio
$conteudoJSON = json_encode($CTeJSON, JSON_UNESCAPED_UNICODE);
$retorno = $NSSuite->emitirCTeSincrono($conteudoJSON, "57", "json", "07364617000135", "XP", "2", "C:/ns-cte-newstructure-php/Notas/", false);

echo $retorno;
?>