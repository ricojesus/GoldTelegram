<?php
/**
*
* @author     Ricardo Jesus
* @version    1.0  
*  
* Programa responsável por transformar a requisição num resultado em padrão texto.
*
*Versão Data       Autor            Descricao
*------ ---------- ---------------- --------------------------------------
* 1.0   23/07/2016 	Ricardo Jesus	 	Versão inicial
* 1.1	29/07/2016	Tiago Rosa	 	Melhorias gerais e inclusao do Vatsim
* 1.2	30/07/2016	Rodrigo Figueiredo	Edição dos textos
* 1.3   04/08/2016  	Tiago Rosa		Mudança na forma como pegar os Atcs da Vatsim e mudado o nome do comando na função ajuda para VATBRZ
* 1.4 	06/08/2016 	Ricardo Jesus		Ajustes para o comando atcvatbrz e tabulacao do metodo vatsim
* 1.5   08/08/2016 	Tiago Rosa  		Inserindo comando /pilotovatbrz
* 1.6	13/08/2016	Rodrigo Figueiredo	Edição de textos e layout
* 1.7   15/08/2016	Tiago Rosa		Inserindo comando /cartas
* 1.8   16/08/2016  	Tiago Rosa		Inserindo comandos /atcivaobr e /pilotosivaobr
*------ ---------- ---------------- --------------------------------------
* 
*/

define('DEFAULT_FOOTER', "\n <b>" . " --- IMPORTANTE --- " . "</b> \n  ⚠️ Atenção: Recomendo que me use preferencialmente de forma privada, para isso basta clicar aqui ➡️ @GoldVirtualBOT. \n \n Sempre que precisar de auxilio me chame digitando /ajuda \n by goldvirtual.com.br ");

//Metodo principal responsavel por direcionar a requisicao para o metodo correspondente
function getResult($mensagem, $text){
	$out = '';

	if($mensagem=="start"){
		$out = getStart($text);
	} elseif($mensagem=="metar"){
		$out = getMetar(substr($text, 7));
	} elseif($mensagem=="regras"){
		$out = getRegras();
	} elseif($mensagem=="ajuda"){
		$out = getAjuda();
	} elseif($mensagem=="atcvatbrz"){
		$out = getVatsim();
	} elseif($mensagem=="pilotosvatbrz"){
		$out = getPilotovatbrz();
	} elseif($mensagem=="cartas"){
		$out = getCartas(substr($text,8,4),substr($text,12));
	} elseif($mensagem=="atcivaobr"){
		$out = getatcivaobr();
	} elseif($mensagem=="pilotosivaobr"){
		$out = getpilotosivaobr();
	}
	return $out;
}

function getStart($userName){
	$resultado = "";

	$resultado = "Olá <b>" . $userName . "</b>, cordial boa!\n\n";
	$resultado .= "Bem vindo ao Grupo da Gold Virtual Airlines no Telegram. \n";
	$resultado .= "Aqui você poderá conversar, se divertir e ficar ligado nos assuntos sobre aviação real e virtual. \n\nPara se informar das regras e das facilidades automatizadas que estão disponíveis siga 2 passos abaixo: \n";
	$resultado .= "1) Clique aqui ➡️ @GodVirtualBot \n";
	$resultado .= "2) Digite /regras \n";
	
	return $resultado .= DEFAULT_FOOTER;
	}

//Metodo para captura de Metar e TAF
function getMetar($icao){
	try{
		$resultado = '';
		
		if($icao === false)
		{
			$resultado = "🚨 Olá, informe um ICAO válido para que eu consiga te mostrar o METAR e o TAF \n (Ex.: /metar SBRF) \n";
			$resultado .= DEFAULT_FOOTER;
			return $resultado;
		}
		else{		
			$metar = file_get_contents('http://www.redemet.aer.mil.br/api/consulta_automatica/index.php?local='. $icao .'&msg=metar');

			if (strpos($metar, 'localizada') == 0){
				$resultado = '<b>🌤 A Gold Virtual informa como está o METAR de '. strtoupper($icao) .'  neste momento:</b> ';
				$resultado .= "\n\n➡️" . substr($metar, 14) . "\n";
				$resultado .= 'Trouxe também o TAF com as previsões das próximas horas, fique ligado!';
				$taf = file_get_contents('http://www.redemet.aer.mil.br/api/consulta_automatica/index.php?local='. $icao .'&msg=taf');
				$resultado .=  "\n\n➡️" . substr($taf, 14);				
				
				$resultado .= DEFAULT_FOOTER;
			}else{
				$resultado = "🚨 Olá, informe um ICAO válido para que eu consiga te mostrar o METAR e o TAF \n (Ex.: /metar SBRF) \n";
				$resultado .= " \n" .DEFAULT_FOOTER;
			}
		}
		return $resultado;

	}catch (Exception $e){
		return 'Erro consultando Metar, favor consultar a staff de TI da Gold' . $e->getMessage();
	}
}

function getRegras(){
	$resultado = "";

	$resultado = "<b>📌 *** Regras do Grupo ***</b>\n\n";
	$resultado .= "Fique ligado nas principais regras de convivência deste grupo:  \n";
	$resultado .= "1⃣ A informação, instrução, respeito, aviação e diversão, deverão ser os principios de qualquer interação no grupo.  \n";
	$resultado .= "2⃣ Proibido qualquer ato descriminatório como religião, cor, raça, etnia, politica e opção sexual.  \n";
	$resultado .= "2⃣ Proibido qualquer ato descriminatório como religião, cor, raça, etnia, politica e opção sexual.  \n";
	$resultado .= "4⃣ Proibido o compartilhamento de conteúdos piratas. \n";
	
	return $resultado .= DEFAULT_FOOTER;
}

function getAjuda(){
	$resultado = "";

	$resultado = "<b>💡 Olá, eu sou o BOT da Gold Virtual Airlines!</b>  \n\n";
	$resultado .= "Veja abaixo todos os comandos que estão disponiveis: \n";
	$resultado .= "✔️ /ajuda - Comando para ver as funcionalidades do BOT  \n";
	$resultado .= "✔️ /regras - Comando para ver as Regras do Grupo  \n";
	$resultado .= "✔️ /metar - Comando para visualizar o METAR e TAF  \n";
	$resultado .= "✔️ /atcvatbrz - Comando para visualizar Controladores na VATBRZ  \n";
	$resultado .= "✔️ /pilotosvatbrz - Comando para visualizar os pilotos da VATBRZ  \n";
	$resultado .= "✔️ /cartas - Comando para trazer as cartas de um aeródromo  \n";
	$resultado .= "✔️ /atcivaobr - Comando para visualizar Controladores na IVAOBR  \n";
	$resultado .= "✔️ /pilotosivaobr - Comando para visualizar os pilotos da IVAOBR  \n";
	$resultado .= "\n<b>✈️ Siga-nos: Redes Sociais!</b> \n";
	$resultado .= "Facebook: www.facebook.com/GOLDVIRTUAL \n";
	$resultado .= "Youtube: www.youtube.com/user/GoldVirtualAirlines \n";
	$resultado .= "Telegram: www.telegram.me/goldvirtual \n";
	$resultado .= "Twitter: www.twitter.com/GoldVirtualAirlines \n";
	
	return $resultado .= DEFAULT_FOOTER;
}

function getvatsim(){
	//acerto da tabulacao 
	$url = file_get_contents('https://extraction.import.io/query/extractor/2461882d-1900-4d76-9692-43cdd29a1c2c?_apikey=f50390b575ce430ca4ef0aa36d0bea8560f0ff3ca97d78f53041c81d8f63cf60b13f1e1683575554ea1766bd1b74a5f9060378680cb4315db4209c9a56d9f8885782c90d8738131a70b35e5b3c4b5563&url=http%3A%2F%2Fvatview.com%2Fvatview_display_list.php%3Ftyped%3Datc');

	$json_str = json_decode($url);
	//print_r($json_str);

	$extractorData = $json_str->extractorData;
	//echo 'url: ' . $extractorData->url . '<br/>';
	//echo 'resourceId: ' . $extractorData->resourceId . '<br/>';

	$data = $extractorData->data;
	$retorno = '';

	foreach ($data as $d) {
		foreach ($d as $group) {
			foreach ($group as $g) {
				$registro = '';

				if (isset($g->callsign) && isset( $g->Name) && isset($g->Frequency)){
					$callsign = $g->callsign;
					$Name = $g->Name;
					$Frequency = $g->Frequency;

					foreach ($callsign as $value) {
						$registro .=  $value->text . ' - ';
					}

					foreach ($Name as $value) {
						$registro .=  $value->text . ' - ';
					}
					foreach ($Frequency as $value) {
						$registro .=  $value->text;
					}
					if (substr($registro,0,2) == 'SB'){
						$retorno .= $registro . "\n\n";
					}
				} 
			} 
		}
	}

	if ($retorno == ''){
		$retorno = "🚨 Infelizmente não temos controladores onlines no momento. Realize seu voo normalmente e não esqueça de reportar via texto na frequência da UNICOM 123.450 \n";
	}
	$resultado = "<b>✈️ A Gold Virtual informa o(s) ATC(s) online na VATBRZ:</b> \nFormato: Posição - Nome do ATC - Frequência \n\n" ;
	
	return $resultado . $retorno. DEFAULT_FOOTER;
}

function getPilotovatbrz(){
	$url = file_get_contents('https://extraction.import.io/query/extractor/ab868837-91aa-45f1-a6fd-729249303548?_apikey=f50390b575ce430ca4ef0aa36d0bea8560f0ff3ca97d78f53041c81d8f63cf60b13f1e1683575554ea1766bd1b74a5f9060378680cb4315db4209c9a56d9f8885782c90d8738131a70b35e5b3c4b5563&url=http%3A%2F%2Fvatview.com%2Fvatview_display_list.php%3Ftyped%3Dpilots');
	$json_str = json_decode($url);
	$extractorData = $json_str->extractorData;
	$data = $extractorData->data;
	$retorno = '';
	$inserir = 0;
	foreach ($data as $d) {
		foreach ($d as $group) {
			foreach ($group as $g) {
				$registro = '';
				if (isset($g->Callsign) && isset( $g->name) && isset($g->Dep) && isset($g->Dest)){
					$callsign = $g->Callsign;
					$Name = $g->name;
					$dep = $g->Dep;
					$dest = $g->Dest;
					foreach ($callsign as $value) {
						$registro .=  $value->text . ' - ';
					}
					foreach ($Name as $value) {
						$registro .=  str_replace('-','',substr(trim($value->text),0,strlen(trim($value->text))-4)) . ' (';
					}
					foreach ($dep as $value) {
						$registro .=  $value->text . ' > ';
						if(substr($value->text,0,2)=='SB' || substr($value->text,0,2)=='SD' || substr($value->text,0,2)=='SI' || substr($value->text,0,2)=='SJ' || substr($value->text,0,2)=='SN' || substr($value->text,0,2)=='SW'){
							$inserir = 1;
						}
					}
					foreach ($dest as $value) {
						$registro .=  $value->text;
						if(substr($value->text,0,2)=='SB' || substr($value->text,0,2)=='SD' || substr($value->text,0,2)=='SI' || substr($value->text,0,2)=='SJ' || substr($value->text,0,2)=='SN' || substr($value->text,0,2)=='SW'){
							$inserir = 1;
						}
					}
					if ($inserir == 1){
						$retorno .= $registro . ")\n\n";
						$inserir = 0;
					}
				} 
			}
		}
	}
	if ($retorno == ''){
		$retorno = "🚨 Infelizmente não temos pilotos voando na VATBRZ no momento. \n";
	}
	$resultado = "<b>✈️ A Gold Virtual informa o(s) Piloto(s) Online na VATBRZ:</b> \nFormato: Callsign - Nome do Piloto (Dep > Dest) \n\n" ;
	return $resultado . $retorno. DEFAULT_FOOTER;
}
function clean($text){
	$text = trim( preg_replace( '/\s+/', ' ', $text ) );  
	$text = preg_replace("/(\r\n|\n|\r|\t)/i", '', $text);
	return $text;
}
function getCartas($icao,$tipo){
	try{
		$resultado = '';
		if($icao === false || $tipo===false)
		{
			$resultado = "🚨 Olá, informe um ICAO válido para que eu consiga te mostrar as cartas correspondentes \n (Ex.: /cartas SBJV SID) \n";
			$resultado .= DEFAULT_FOOTER;
			return $resultado;
		}
		else{		
			$icao = trim($icao);
			$tipo = trim($tipo);
			$link = file_get_contents('http://www.aisweb.aer.mil.br/api/?apiKey=1017549855&apiPass=4e1194fd-3711-11e6-a8ca-00505680c1b4&area=cartas&IcaoCode='.$icao.'&tipo='.$tipo);
			$xml = simplexml_load_string($link);
			$qtde = $xml->cartas['total'];
			if($qtde>0){
				$resultado = "✉ A Gold Virtual informa a(s) Carta(s) " .$tipo. " para o aeródromo ".$icao.": \n\n";
				foreach($xml->cartas->item as $item){
					$resultado .= "<a href ='".$item->link."'>".$item->nome."</a>\n";
				}
				$resultado .= DEFAULT_FOOTER;
			}
			else{
				$resultado = "🚨 Olá, Não conseguimos encontra nenhuma carta {$tipo} para o aeródromo {$icao}";
				$resultado .= DEFAULT_FOOTER;
			}
		}
		return $resultado;
	}catch (Exception $e){
		return 'Erro consultando cartas, favor consultar a staff de TI da Gold' . $e->getMessage();
	}
}
function getatcivaobr(){
	try{
		$retorno = '';
		$url = file_get_contents("https://api.ivao.aero/getdata/whazzup");
		
		$cabecalho = explode(":","callsign:vid:name:clienttype:frequency:latitude:longitude:altitude:groundspeed:flightplanaircraft:flightplancruisingspeed:flightplandepartureaerodrome:flightplancruisinglevel:flightplandestinationaerodrome:server:protocol:combinedrating:transpondercode:facilitytype:visualrange:flightplanrevision:flightplanflightrules:flightplandeparturetime:flightplanactualdeparturetime:flightplaneethours:flightplaneetminutes:flightplanendurancehours:flightplanenduranceminutes:flightplanalternateaerodrome:flightplanitem18otherinfo:flightplanroute:unused1:unused2:unused3:unused4:atis:atistime:connectiontime:softwarename:softwareversion:administrativeversion:atc/pilotversion:flightplan2ndalternateaerodrome:flightplantypeofflight:flightplanpersonsonboard:heading:onground:simulator:plane");
		
		$pos = strpos($url,'!CLIENTS');
		$tabela_ini = substr($url, $pos+7, strlen($url));
		$pos_fim = strpos($tabela_ini,'!AIRPORTS');
		$tabela_fim = substr($tabela_ini, 1, $pos_fim-1);
		
		$matriz = explode("\n", $tabela_fim);
		
		for($i = 0;$i< count($matriz); $i++){
			if (trim($matriz[$i]) != ""){
				$reg = explode(":",$matriz[$i]);
				$reg = array_combine($cabecalho,$reg);
				
				if($reg['clienttype'] == 'ATC'){
					if(substr($reg['callsign'],0,2)=='SB'){
						$retorno .= "<b>".$reg['callsign']."</b> - ".$reg['vid']." - ".$reg['frequency']."\n";
					}
				}
			}
		}
		if ($retorno == ''){
			$retorno = "🚨 Infelizmente não temos controladores onlines no momento. Realize seu voo normalmente e não esqueça de reportar via texto na frequência da UNICOM 122.800 \n";
		}
	
		$resultado = "<b>✈️ A Gold Virtual informa o(s) ATC(s) online na IVAOBR:</b> \nFormato: Posição - VID - Frequência \n\n" ;
	
		return $resultado . $retorno. DEFAULT_FOOTER;
		
	}catch (Exception $e){
		return 'Erro consultando Cartas, favor consultar a staff de TI da Gold' . $e->getMessage();
	}
}
function getpilotosivaobr(){
	try{
		$retorno = '';
		$inserir = 0;
		$url = file_get_contents("https://api.ivao.aero/getdata/whazzup");
		
		$cabecalho = explode(":","callsign:vid:name:clienttype:frequency:latitude:longitude:altitude:groundspeed:flightplanaircraft:flightplancruisingspeed:flightplandepartureaerodrome:flightplancruisinglevel:flightplandestinationaerodrome:server:protocol:combinedrating:transpondercode:facilitytype:visualrange:flightplanrevision:flightplanflightrules:flightplandeparturetime:flightplanactualdeparturetime:flightplaneethours:flightplaneetminutes:flightplanendurancehours:flightplanenduranceminutes:flightplanalternateaerodrome:flightplanitem18otherinfo:flightplanroute:unused1:unused2:unused3:unused4:atis:atistime:connectiontime:softwarename:softwareversion:administrativeversion:atc/pilotversion:flightplan2ndalternateaerodrome:flightplantypeofflight:flightplanpersonsonboard:heading:onground:simulator:plane");
		
		$pos = strpos($url,'!CLIENTS');
		$tabela_ini = substr($url, $pos+7, strlen($url));
		$pos_fim = strpos($tabela_ini,'!AIRPORTS');
		$tabela_fim = substr($tabela_ini, 1, $pos_fim-1);
		
		$matriz = explode("\n", $tabela_fim);
		
		for($i = 0;$i< count($matriz); $i++){
			if (trim($matriz[$i]) != ""){
				$reg = explode(":",$matriz[$i]);
				$reg = array_combine($cabecalho,$reg);
				
				if($reg['clienttype'] == 'PILOT'){
					if(substr($reg['flightplandepartureaerodrome'],0,2)=='SB'  || substr($reg['flightplandepartureaerodrome'],0,2)=='SD' ||
						substr($reg['flightplandepartureaerodrome'],0,2)=='SI' || substr($reg['flightplandepartureaerodrome'],0,2)=='SJ'||
						substr($reg['flightplandepartureaerodrome'],0,2)=='SN' || substr($reg['flightplandepartureaerodrome'],0,2)=='SW'){
							$inserir = 1;
					}
					if(substr($reg['flightplandestinationaerodrome'],0,2)=='SB'  || substr($reg['flightplandestinationaerodrome'],0,2)=='SD' ||
						substr($reg['flightplandestinationaerodrome'],0,2)=='SI' || substr($reg['flightplandestinationaerodrome'],0,2)=='SJ'||
						substr($reg['flightplandestinationaerodrome'],0,2)=='SN' || substr($reg['flightplandestinationaerodrome'],0,2)=='SW'){
							$inserir = 1;
					}
					
					if($inserir == 1){
						$retorno .= "<b>".$reg['callsign']."</b> - ".$reg['vid']." - (".$reg['flightplandepartureaerodrome']." > ".$reg['flightplandestinationaerodrome'].")\n\n";
						$inserir = 0;
					}
				}
			}
		}
		if ($retorno == ''){
			$retorno = "🚨 Infelizmente não temos pilotos voando na IVAOBR no momento. \n";
		}
		
		$resultado = "<b>✈️ A Gold Virtual informa o(s) Piloto(s) Online na IVAOBR:</b> \nFormato: Callsign - VID - (Dep > Dest) \n\n" ;
		
		return $resultado . $retorno. DEFAULT_FOOTER;
		
	}catch (Exception $e){
		return 'Erro consultando Cartas, favor consultar a staff de TI da Gold' . $e->getMessage();
	}
}
?>
