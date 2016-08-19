<?php
/**
*
* @author     Ricardo Jesus
* @version    1.0  
*  
* Programa responsável por receber a requisição a partir do Telegram client indentificar o comando
*   e comunicar com a api do Telegram e retornar o resultado para o Telegram client
*
* Versão Data       Autor            Descricao
* ------ ---------- ---------------- --------------------------------------
* 1.0    23/07/2016 Ricardo Jesus	 Versão inicial
* 1.1    04/08/2016 Tiago Rosa       1.implantação da mensagem de boas vindas, (adicionado um membro no grupo)
*									 2.Desabilitado o Rodapé (webpage) da mensagem de start)
*									 3.Alterado o comando de vatsim para vatbrz
* 1.2	06/08/2016	Ricardo Jesus	 Alteração do comando vatbrz para atcvatbrz
* 1.3	08/08/2016	Tiago Rosa		 Implementação do comando pilotosvatbrz
* 1.4	15/08/2016	Tiago Rosa		 Implementação do comando cartas
* 1.5   16/08/2016  Tiago Rosa		 Implementação dos comandos atcivaobr e pilotosivaobr
* 1.6	16/08/2016	Ricardo Jesus	 Inclusão de Param. User para todos os comandos, para as estatisticas
* 1.7	17/08/2016	Tiago Rosa	 Inclusão do comando /pv
* 1.8	18/08/2016	Tiago Rosa		 envio de gif no start
*------ ----------  ---------------- --------------------------------------
* 
*/
require('parser.php');

// Token é o codigo unico cedido pelo Telegram, somente alterar caso seja requisitado um novo Token ao Telegram 
// ** Token teste Ricardo ** //
//define('BOT_TOKEN', '269387957:AAH5K_-dRKlw0pQb07Ontu8NxBG83pr2xhU');
// ** Token teste Tiago ** //
//define('BOT_TOKEN', '181646159:AAEagAKnu-cpgHwBBbyegPBAAT7NO63_dfc');


// ** Token da GOLD para implementar no ambiente de produção essa linha deve ser descomentada e a linha superior comentada. ** //

define('BOT_TOKEN', '269999230:AAFcwOL7UCs0BsYHqoXVhy9V5KEsinTlNaw');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');


//Metodo de entrada das requisições, interpreta os comandos e direciona para o Parse
function processMessage($message) {
	// processa a mensagem recebida
	$message_id = $message['message_id'];
	$chat_id = $message['chat']['id'];
	$user = $message['new_chat_member']['first_name'];
	if($user != ''){
		sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('start', $message['from']['first_name']),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		sendMessage("sendPhoto", array('chat_id' => $chat_id, "photo" => "AgADAQADqqcxG37cFxAvQftM2BTX8QeE5y8ABLu7myirrGgkASwBAAEC",'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
	}elseif(isset($message['text'])) {
		$text = $message['text'];//texto recebido na mensagem
		$user = $message['from']['first_name'];

		if (strtolower(substr($text, 0, 6)) == "/start") {						
			$text = $message['from']['first_name'];
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('start', $message['from']['first_name']),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
			sendMessage("sendPhoto", array('chat_id' => $chat_id, "photo" => "AgADAQADqqcxG37cFxAvQftM2BTX8QeE5y8ABLu7myirrGgkASwBAAEC",'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 6)) == "/metar") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('metar', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 7)) == "/regras") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('regras', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 6)) == "/ajuda") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('ajuda', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 10)) == "/atcvatbrz") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('atcvatbrz', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 14)) == "/pilotosvatbrz") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('pilotosvatbrz', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 7)) == "/cartas") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('cartas', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 10)) == "/atcivaobr") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('atcivaobr', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 14)) == "/pilotosivaobr") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('pilotosivaobr', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 4)) == "/bot") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('bot', null, null),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 3)) == "/pv") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('pv', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} else {
			if (substr($text,0,1)=="/"){
				sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Desculpe, '. $message['from']['first_name']. ' não consegui compreender sua mensagem!'));
			}
		}
	}
}

// Metodo para envio do retorno da requisição, não modificar
function sendMessage($method, $parameters) {
	$options = array('http' => array(
					 'method'  => 'POST',
					 'content' => json_encode($parameters),
					 'header'=>  "Content-Type: application/json\r\n" .
					 "Accept: application/json\r\n"));

	$context  = stream_context_create( $options );
	file_get_contents(API_URL.$method, false, $context );
}

// ** codigo para envio automatico quando no site ** //


$update_response = file_get_contents("php://input");
$update = json_decode($update_response, true);
if (isset($update["message"])) {
  processMessage($update["message"]);
}

// ** fim do codigo **//

// ** Codigo para envio durante testes ** //
/*
$update_response = file_get_contents(API_URL."getUpdates");
$response = json_decode($update_response, true);
$length = count($response["result"]);
//obtém a última atualização recebida pelo bot
$update = $response["result"][$length-1];

if (isset($update["message"])) {
 	processMessage($update["message"]);
}
*/
?>