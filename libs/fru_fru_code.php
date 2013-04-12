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

class FruFruCode
{
	static $tabela = array(
		'default' => array(
				array(1,2,3,4,5,6,7,8,9,
					'tipo' => 'casa', 'base' => 9, 'num_digitos' => 1
				),
				array(1,2,3,4,5,6,7,8,9,
					'tipo' => 'casa', 'base' => 9, 'num_digitos' => 1
				),
				array(	'pa','pe','pi','po','pu','da','de','di',
						'do','du','ja','je','ji','jo','ju','la',
						'le','li','lo','lu','ka','ke','ki','ko',
						'ku','wa','we','wi','wo','wu','qa','qe',
						'tipo' => 'casa', 'base' => 32, 'num_digitos' => 2
				),
				array(	'la','le','li','lo','lu','ka','ke','ki',
						'jo','ju','xa','xe','xi','xo','xu','ha',
						'te','ti','to','tu','sa','se','si','so',
						'su','ba','be','bi','bo','bu','we','wi',
						'tipo' => 'casa', 'base' => 32, 'num_digitos' => 2
				),
				array(	'ya','ye','yi','yo','yu','ra','re','ri',
						'ro','ru','ga','ge','gi','go','gu','na','ho',
						'ne','ni','no','nu','xa','xe','xi','xo',
						'xu','za','ze','zi','zo','zu','fa','fe',
						'tipo' => 'casa', 'base' => 33, 'num_digitos' => 2
				),
				array(	'fa','fe','fi','fo','fu','ja','je','ji',
						'ko','ku','ma','me','mi','mo','mu','ta',
						'he','hi','ho','hu','va','ve','vi','vo',
						'vu','qa','qe','qi','qo','qu','po','pe',
						'tipo' => 'casa', 'base' => 32, 'num_digitos' => 2
				),
		)
	);
	
	static $base = 32;
	static $capacidade = array();			//este é o tamanho da string gerada em geral
	
	
	public static function encode ($num, $config = 'default')
	{
		self::initialize($config);
		
		if (!is_numeric($num))
		{
			trigger_error('FruFruCode::encode() - Only integers allowed.');
			return false;
		}
		
		if ($num > self::$capacidade)
		{
			trigger_error('FruFruCode::encode() - The number is too high.');
		}
		
		$num = self::trocaBits($num);
		$codigo = '';
		
		foreach (self::$tabela[$config] as $atual)
		{
			if ($atual['tipo'] == 'enchimento')
				$codigo = $atual['enchimento'] . $codigo;
			else
			{
				$codigo = $atual[($num % $atual['base'])] . $codigo;				
				$num = floor($num/$atual['base']);				
			}
		}
		return $codigo;
	}
	
	public static function decode($codigo, $config = 'default')
	{
		$num = 0;
		$multiplicador_atual = 1;
		$codigo = trim($codigo); //elimina os espacos em branco de trás e da frente e faz tudo minusculo
		
		foreach (self::$tabela[$config] as $atual)
		{
			$trecho = substr($codigo . ' ', -$atual['num_digitos']-1, -1); //hack pq não entendi o substr
			$codigo = substr($codigo, 0, -$atual['num_digitos']);
			
			if ($atual['tipo'] != 'enchimento')
			{				
				if (($valor_da_casa = array_search($trecho, $atual)) === false)
					$valor_da_casa = 0;
					
				$num += $multiplicador_atual * $valor_da_casa;
				$multiplicador_atual *= $atual['base'];
			}
		}
		return self::trocaBits($num);
	}
	
	
	protected static function trocaPos1Bit($num, $i_bit_perto, $i_bit_longe)  //tem que dizer o número do bit -- 0 é o primeiro bit
	{
		$bit_perto_puro = (1 << $i_bit_perto);
		$bit_longe_puro = (1 << $i_bit_longe);
	
		$bit_perto = $num & (1 << $i_bit_perto); // primeiro põe os bits no lugar
		$bit_longe = $num & (1 << $i_bit_longe);		
		
		$num_sem_bits = ($num & ~($bit_perto_puro | $bit_longe_puro));  // agora, tira os bits do número
		
		$bit_perto_deslocado = $bit_perto << ($i_bit_longe - $i_bit_perto); //agora desloca os dois bits para suas posicoes trocadas
		$bit_longe_deslocado = $bit_longe >> ($i_bit_longe - $i_bit_perto); 
		
		return $num_sem_bits | $bit_perto_deslocado | $bit_longe_deslocado; // insere os bits nas posições vazias
	}
	
	protected static function trocaBits($num)  //isto aqui serve para os códigos não serem tediosos! Mudarem mais...
	{
		$num = self::trocaPos1Bit($num,0,25);
		$num = self::trocaPos1Bit($num,1,13); //o dois é preservado para se observar mudanças nesta casa também
		$num = self::trocaPos1Bit($num,3,6 );
		$num = self::trocaPos1Bit($num,4,14);
		$num = self::trocaPos1Bit($num,5,23);
		$num = self::trocaPos1Bit($num,7,17);
		return $num;
	}
	
	protected static function initialize($config)
	{
		//cálculo da capacidade de armazenar números!
		if (!isset(self::$capacidade[$config]))
			self::$capacidade[$config] =  pow(self::$base, count(Set::extract('/.[tipo=casa]', self::$tabela[$config]))) - 1;
	}
}

