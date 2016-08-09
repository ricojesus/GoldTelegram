<?php
class DAO
{
	var $host = "localhost";
	var $user = "goldv250_vbot";
	var $pass = "GVBot01";
	var $dbase = "goldv250_virtualbot";
	var $querytypes;
	var $conexao;
	var $mysqli;
	
	function __constructor(){
		ExceptionThrower::Start();
		$this->connectBD();
	}

	function connectBD()
	{
		$this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->dbase);
		if (mysqli_connect_errno()) {
		    die('Não foi possível conectar-se ao banco de dados: ' . mysqli_connect_error());
		    exit();
		}
	}
	
	function desconectBD()
	{
		try{
			mysql_close($this->conexao);
		}
		catch(Exception $e){
			throw new Exception('Erro Fechando conexão com a base de dados, contactar suporte', 0, $e);
		}		
	}
	
	function executeQuery($query)
	{
		$this->connectBD();

		return $this->mysqli->query($query);
	}	
}