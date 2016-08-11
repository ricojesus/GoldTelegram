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
* 1.3   04/08/2016  Tiago Rosa		1. Mudança na forma como pegar os Atcs da Vatsim e mudado o nome do comando na função ajuda para VATBRZ
* 1.4 	06/08/2016 Ricardo Jesus	Ajustes para o comando atcvatbrz e tabulacao do metodo vatsim
* 1.5   08/08/2016 	Tiago Rosa  inserindo comando /pilotovatbrz
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
	} elseif($mensagem=="vatsim"){
		$out = getVatsim();
	} elseif($mensagem=="pilotosvatbrz"){
		$out = getPilotovatbrz();
	}
	return $out;
}

function getStart($userName){
	$resultado = "";

	$resultado = "Olá <b>" . $userName . "</b>, cordial boa!\n\n";
	$resultado .= "Bem vindo ao Grupo da Gold Virtual Airlines no Telegram. \n";
	$resultado .= "Aqui você poderá conversar, se informar e se divertir com assuntos sobre aviação real e virtual. \n\nPara se informar das regras e das facilidades automatizadas que estão disponíveis siga 2 passos abaixo: \n";
	$resultado .= "1) Clique aqui @GodVirtualBot \n";
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
				$resultado = '🌤 Olá, veja como está o METAR de <b> ' . strtoupper($icao) . ' </b> neste momento! ';
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
	$resultado .= "✔️ /pilotosvatbrz - Comando para visualizar os pilotos voando na VATBRZ  \n";
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
	$resultado = "✅ A Gold Virtual informa o(s) ATC(s) Online na VATBRZ: \n\n" ;
	
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
						$registro .=  str_replace('-','',substr(trim($value->text),0,strlen(trim($value->text))-4)) . ' - ';
					}
					foreach ($dep as $value) {
						$registro .=  $value->text . ' - ';
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
						$retorno .= $registro . "\n\n";
						$inserir = 0;
					}
				} 
			}
		}
	}
	if ($retorno == ''){
		$retorno = "🚨 Infelizmente não temos pilotos onlines no momento. \n";
	}
	$resultado = "✅ A Gold Virtual informa o(s) Piloto(s) Online na VATBRZ: \n Callsign - RealName - Dep - Dest \n\n" ;
	return $resultado . $retorno. DEFAULT_FOOTER;
}
function clean($text){
	$text = trim( preg_replace( '/\s+/', ' ', $text ) );  
	$text = preg_replace("/(\r\n|\n|\r|\t)/i", '', $text);
	return $text;
}
?>
