<?php

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

class AjaxClientes{

	/*=============================================
	EDITAR CLIENTE
	=============================================*/	

	public $idCliente;

	public function ajaxEditarCliente(){

		$item = "id";
		$valor = $this->idCliente;

		$respuesta = ControladorClientes::ctrMostrarClientes($item, $valor);

		echo json_encode($respuesta);

	}

	/*=============================================
	BUSCAR CLIENTE POR DNI
	=============================================*/	

	public $dniCliente;

	public function ajaxBuscarClienteDni(){

		$item = "documento";
		$valor = $this->dniCliente;

		$respuesta = ControladorClientes::ctrMostrarClientes($item, $valor);

		// Siempre devolver un JSON válido, incluso si no hay resultados
		if($respuesta){
			echo json_encode($respuesta);
		} else {
			echo json_encode(array('error' => 'Cliente no encontrado'));
		}

	}

}

/*=============================================
EDITAR CLIENTE
=============================================*/	

if(isset($_POST["idCliente"])){

	$cliente = new AjaxClientes();
	$cliente -> idCliente = $_POST["idCliente"];
	$cliente -> ajaxEditarCliente();

}

/*=============================================
BUSCAR CLIENTE POR DNI
=============================================*/	

if(isset($_POST["buscarClienteDni"])){

	$cliente = new AjaxClientes();
	$cliente -> dniCliente = $_POST["buscarClienteDni"];
	$cliente -> ajaxBuscarClienteDni();

}