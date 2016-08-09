<?php
/**
*
* @author     Ricardo Jesus
* @version    1.0  
*  
* Programa responsável por receber a requisição a partir do Telegram client indentificar o comando
*   e comunicar com a api do Telegram e retornar o resultado para o Telegram client
*
*Versão Data       Autor            Descricao
*------ ---------- ---------------- --------------------------------------
* 1.0   23/07/2016 Ricardo Jesus	Versão inicial
* 1.1   04/08/2016 Tiago Rosa       1.implantação da mensagem de boas vindas, (adicionado um membro no grupo)
*									2.Desabilitado o Rodapé (webpage) da mensagem de start)
*									3.Alterado o comando de vatsim para vatbrz
* 1.2	06/08/2016 Ricardo Jesus	Alteração do comando vatbrz para atcvatbrz
* 1.4	08/08/2016 Ricardo Jesus    Inclusão de parametro $user para gravação de estatisticas
*------ ---------- ---------------- --------------------------------------
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
		sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('start', $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
	}elseif(isset($message['text'])) {
		$text = $message['text'];//texto recebido na mensagem
		$user = $message['from']['first_name']; //Usuario requisitante

		if (strtolower(substr($text, 0, 6)) == "/start") {
			$text = $message['from']['first_name'];
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('start', null, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));

		} elseif (strtolower(substr($text, 0, 6)) == "/metar") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('metar', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 7)) == "/regras") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('regras', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 6)) == "/ajuda") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('ajuda', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 10)) == "/atcvatbrz") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('vatsim', $text, $user),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} elseif (strtolower(substr($text, 0, 10)) == "/estatisticas") {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => getResult('estatisticas', null, null),'disable_web_page_preview'=>true,'parse_mode'=>'HTML'));
		} else {
			sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Desculpe, '. $user. ' não consegui compreender sua mensagem! \n digite /ajuda para conhecer todos os serviços disponíveis'));
		}
	} else {
		sendMessage("sendMessage", array('chat_id' => $chat_id, "text" => 'Desculpe, mas só compreendo mensagens em texto'));
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