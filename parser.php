<?php
/**
*
* @author     Ricardo Jesus
* @version    1.0  
*  
* Programa responsável por transformar a requisição num resultado em padrão texto.
*
* Versão Data       Autor            Descricao
* ------ ---------- ---------------- --------------------------------------
* 1.0   23/07/2016 	Ricardo Jesus	 	Versão inicial
* 1.1	29/07/2016	Tiago Rosa	 	Melhorias gerais e inclusao do Vatsim
* 1.2	30/07/2016	Rodrigo Figueiredo	Edição dos textos
* ------ ---------- ---------------- --------------------------------------
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
	$resultado .= "✔️ /vatsim - Comando para visualizar  Controladores na VATBRZ  \n";
	$resultado .= "\n<b>✈️ Siga-nos: Redes Sociais!</b> \n";
	$resultado .= "Facebook: www.facebook.com/GOLDVIRTUAL \n";
	$resultado .= "Youtube: www.youtube.com/user/GoldVirtualAirlines \n";
	$resultado .= "Telegram: www.telegram.me/goldvirtual \n";
	$resultado .= "Twitter: www.twitter.com/GoldVirtualAirlines \n";
	
	return $resultado .= DEFAULT_FOOTER;
}

function getvatsim(){
	//Busca Tabela
	$url = file_get_contents('https://stats.vatsim.net/who.html');
	$pos = strpos($url,'<h2>Controllers</h2>');
	$tabela_ini = substr($url, $pos, strlen($url));
	$pos_fim = strpos($tabela_ini,'</table>');
	$tabela_fim = substr($tabela_ini, 1, $pos_fim);
	//---
	 
	 //Busca Linha
	$pos = strpos($tabela_fim, '<tr class="odd">');
	$tabela = substr($tabela_fim, $pos);
	$tabela = str_replace('<tr class="odd">','<tr>',$tabela);
	//--
	$retorno = '';
	 
	$processa = true;
	while ($processa){
		$pos_ini = strpos($tabela,'<tr>');
		$pos_fim = strpos($tabela,'</tr>');
		$linha = substr($tabela, $pos_ini,($pos_fim+5));
		$tabela = str_replace($linha,'',$tabela);
		$linha = str_replace('<tr>','',$linha);
		$linha = str_replace('</tr>','',$linha);
		$registro = '';
	
		for ($i = 1; $i <= 5; $i++) {
			$pos_linha_ini = strpos($linha,'<td');
			$pos_linha_fim = strpos($linha,'</td');
			$campo=  substr($linha, $pos_linha_ini,$pos_linha_fim); 
			$linha = str_replace($campo,'',$linha);
			$campo = str_replace('<td>', '', $campo);
			$campo = str_replace('</td>', '', $campo);
			$campo = str_replace('</a>', '', $campo);
			$pos_a = strpos($campo, '>');
		
			if (!($pos_a === false)) {
				$campo = substr($campo, $pos_a+1, strlen($campo));
			}
		
			if($i == 1 || $i==2 ){
				$registro .= clean($campo) . ' - ';   
			}elseif($i==5){
			   $registro .= clean($campo);
			}
		}
		if (substr($registro,0,2) == 'SB'){
			$retorno .= $registro . "\n\n";
		}
		
		if ($pos_ini === false) {
			$processa = false;
		} 
	}
	if ($retorno == ''){
		$retorno = "🚨 Infelizmente não temos controladores onlines no momento. Realize seu voo normalmente e não esqueça de reportar via texto na frequência da UNICOM 123.450 \n";
	}
	return $resultado = "✅ A Gold Virtual informa o(s) ATC(s) Online na VATBRZ: \n \n". 
	
	$retorno. DEFAULT_FOOTER;
}

function clean($text){
	$text = trim( preg_replace( '/\s+/', ' ', $text ) );  
	$text = preg_replace("/(\r\n|\n|\r|\t)/i", '', $text);
	return $text;
}

