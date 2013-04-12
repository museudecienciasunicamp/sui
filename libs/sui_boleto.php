<?php

/**
 *
 * Copyright 2011-2013, Museu Exploratório de Ciências da Unicamp (http://www.museudeciencias.com.br)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2011-2013, Museu Exploratório de Ciências da Unicamp (http://www.museudeciencias.com.br)
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link          https://github.com/museudecienciasunicamp/sui.git SUI public repository
 */

class SuiBoleto
{
	static $digitoUm;
	static $digitoDois;
	static $recalcular;
	
	static function linhaDigitavel($linha)
	{
		// 1º. Campo: composto pelo código de Banco, código da moeda, as cinco primeiras posições  do campo livre e  dígito verificador (módulo 10) deste campo;
		// 2º. Campo: composto pelas  posições 6ª. à 15ª. do  campo livre e dígito verificador (módulo 10) deste campo;
		// 3º. Campo: composto pelas posições 16ª. à 25ª. do campo livre e dígito  verificador (módulo 10) deste campo;
		// 4º. Campo: Dígito verificador do código de barras (dígito de autoconferência);
		// 5º. Campo: Composto pelo Fator de  Vencimento (anexo 7) e o Valor Nominal do documento, com a inclusão  de  zeros entre eles até compor as 14 posições do campo,  e sem edição de ponto e vírgula. 
		//            Quando se tratar de bloqueto sem discriminação de valor no código de barras a representação deverá ser com zeros. 
		
		$campo1 = substr($linha,0,3).substr($linha,3,1).substr($linha,19,5);
		$campo1Digito = self::mod_10($campo1);
		
		$campo2 = substr($linha,24,10);
		$campo2Digito = self::mod_10($campo2);
		
		$campo3 = substr($linha,34,10);
		$campo3Digito = self::mod_10($campo3);
		
		$campo4 = substr($linha,4,1);
		
		$campo5 = substr($linha,5,4).substr($linha,9,10);
		
		// Monta linha digitável como deve imprimir
		$linha = substr($campo1,0,5).".".substr($campo1,5).$campo1Digito." ".substr($campo2,0,5).".".substr($campo2,5).$campo2Digito." ".substr($campo3,0,5).".".substr($campo3,5).$campo3Digito." ".$campo4." ".$campo5;
		return $linha;
	}
	
	static function calculaCampoLivre($num)
	{
		//self::$recalcular; // Caso resto de D2 seja 10 e retorne 1 será registrado como false para sair do loop de calculo 
		self::$digitoUm   = self::mod_10($num);
		self::$digitoDois = 1;

		$loop=0;
		while (self::$digitoDois == 1) {
			self::$digitoDois = self::mod_11($num.self::$digitoUm);
			if (!self::$recalcular){
				break;
			}
		}

		return $num.self::$digitoUm.self::$digitoDois;
	}
	
	static function createBarcode($codigo)
	{
		// Posição 	Conteúdo
		// 1 a 3    Número do banco
		// 4        Código da Moeda - 9 para Real ou 8 - outras moedas
		// 5 		Dígito de auto-conferência
		// 6 a 9    Fator vencimento
		// 10 a 19  Valor do título (10 posições)
		// 20 a 30  Código do cedente
		// 31 a 37  Nosso numero (7 digitos)
		// 38 a 39  Zeros
		// 40 a 42  033 (Código do banco)
		// 43		1º Dígito verificador
		// 44		2º Dígito verificador
		
		// 1. Primeiro Grupo - composto pela identificação do banco
		$campo1  = substr($codigo,0,3);
		// 2. Segundo Grupo - composto pelo código da moeda
		$campo2  = substr($codigo,3,1);
		// 4. Quarto Grupo - composto pelo fator de vencimento (4 dígitos) e valor do título (10 dígitos)
		$campo4  = substr($codigo,4,14);
		// 5. Quinto Grupo - composto pelo código do cedente
		$campo5  = substr($codigo,18,11);
		// 6. Sexto Grupo - composto pelo nosso número
		$campo6  = substr($codigo,29,7);
		// 7. Sétimo Grupo - composto por 2 zeros
		$campo7  = substr($codigo,36,2);
		// 8. Oitavo Grupo - composto pelo código do banco
		$campo8  = substr($codigo,38,3);
		// 9. Nono Grupo - composto pelo 1º Dígito verificador
		$campo9  = substr($codigo,41,1);
		// 10. Décimo Grupo - composto pelo 2º Dígito verificador
		$campo10 = substr($codigo,42,1);


		$fator = 2;
		$soma = 0;
		
		for ($i = strlen($codigo); $i > 0; $i--) 
		{
			$soma += substr($codigo,$i-1,1) * $fator;
			if($fator >= 9){
				$fator = 2;
			} else {
				$fator++;
			}
		}
		$resto = $soma % 11;

		if (($resto==0) || ($resto==1) || ($resto==10)){
			$campo3 = 1;
		} else {
			$campo3 = 11 - $resto;
		}
		
		return $campo1.$campo2.$campo3.$campo4.$campo5.$campo6.$campo7.$campo8.$campo9.$campo10; 
	}
	
	static function renderBarcode($code)
	{
		$fino = 1;
		$largo = 3 ;
		$altura = 50 ;
		$barcodes[0] = "00110" ;
		$barcodes[1] = "10001" ;
		$barcodes[2] = "01001" ;
		$barcodes[3] = "11000" ;
		$barcodes[4] = "00101" ;
		$barcodes[5] = "10100" ;
		$barcodes[6] = "01100" ;
		$barcodes[7] = "00011" ;
		$barcodes[8] = "10010" ;
		$barcodes[9] = "01010" ;
		for($f1=9;$f1>=0;$f1--){ 
			for($f2=9;$f2>=0;$f2--){  
				$f = ($f1 * 10) + $f2 ;
				$texto = "" ;
				for($i=1;$i<6;$i++){ 
					$texto .=  substr($barcodes[$f1],($i-1),1) . substr($barcodes[$f2],($i-1),1);
				}
				$barcodes[$f] = $texto;
			}
		}
		//Guarda inicial
		$return = '<img src="/sui/img/p.png" width="1" height="'.$altura.'" />';
		$return .= '<img src="/sui/img/b.png" width="1" height="'.$altura.'" />';
		$return .= '<img src="/sui/img/p.png" width="1" height="'.$altura.'" />';
		$return .= '<img src="/sui/img/b.png" width="1" height="'.$altura.'" />';

		$texto = $code;
		if((strlen($texto) % 2) <> 0){
			$texto = "0" . $texto;
		}

		// Draw dos dados
		while (strlen($texto) > 0) {
			$i = round(substr($texto,0,2));
			$texto = substr($texto,2,strlen($texto)-2);
			$f = $barcodes[$i];
			for($i=1;$i<11;$i+=2){
				if (substr($f,($i-1),1) == "0") {
					$f1 = $fino ;
				}else{
					$f1 = $largo ;
				}
				$return .= '<img src="/sui/img/p.png" width="'.$f1.'" height="'.$altura.'" />';
				if (substr($f,$i,1) == "0") {
					$f2 = $fino ;
				}else{
					$f2 = $largo ;
				}
				$return .= '<img src="/sui/img/b.png" width="'.$f2.'" height="'.$altura.'" />';
			}
		}

		// Draw guarda final

		$return .= '<img src="/sui/img/p.png" width="3" height="'.$altura.'" />';
		$return .= '<img src="/sui/img/b.png" width="1" height="'.$altura.'" />';
		$return .= '<img src="/sui/img/p.png" width="1" height="'.$altura.'" />';
		
		return $return;
	}
	
	function mod_11($num)
	{
		$soma=0;
		$fator=2;
		
		for ($i = strlen($num); $i > 0; $i--) {
			$soma += substr($num,$i-1,1) * $fator;
			$tmp = substr($num,$i-1,1) * $fator;;
			$fator++;
			if($fator==8){
				$fator=2;
			}
		}
		
		// Calcula resto
		$resto = $soma%11;

		if ($resto==0){
			$digito = 0;
		} elseif ($resto==1){
			// Caso D1 igual a 9, passa a valer 0 (zero)
			if (self::$digitoUm==9){
				self::$digitoUm=0;
			// Caso contrario adiciona mais 1 para, pois D2 não pode ser igual a 1
			} else {
				self::$digitoUm++;		
			}
			self::$recalcular = true;
		
			// Digito igual a um para continuar no loop após retornar
			$digito=1;
		} else {
			$digito = 11-$resto;
			self::$recalcular = false;
		}

		return $digito;
	}
	
	function mod_10($num)
	{
		$numtotal10 = 0;
		$fator = 2;

		// Separacao dos numeros
		for ($i = strlen($num); $i > 0; $i--) {
			// pega cada numero isoladamente
			$numeros[$i] = substr($num,$i-1,1);
			// Efetua multiplicacao do numero pelo (falor 10)
			// 2002-07-07 01:33:34 Macete para adequar ao Mod10 do Itaú
			$temp = $numeros[$i] * $fator; 
			$temp0=0;
			foreach (preg_split('//',$temp,-1,PREG_SPLIT_NO_EMPTY) as $k=>$v) {
				$temp0 += $v; 
			}
			$parcial10[$i] = $temp0; //$numeros[$i] * $fator;
			// monta sequencia para soma dos digitos no (modulo 10)
			$numtotal10 += $parcial10[$i];
			if ($fator == 2) { 
				$fator = 1;
			} else {
				$fator = 2; // intercala fator de multiplicacao (modulo 10)
			}
		}
		
		// várias linhas removidas, vide função original
		// Calculo do modulo 10
		$resto = $numtotal10 % 10;
		$digito = 10 - $resto;
		if ($resto == 0) {
			$digito = 0;
		}
		
		return $digito;
	}
}
