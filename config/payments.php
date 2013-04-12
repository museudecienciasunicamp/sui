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

Configure::write('Sui.Payments.funcamp', array(
	'agencia' => '0207',
	'carteira' => 'COB',
	'nome_da_agencia' => '',
	'codigo_cedente' => '20713464067',
	'codigo_banco' => '033', 
	'codigo_banco_dv' => '7', // ([(4*0) + (3*3) + (2*3)] * 10 % 11) = 7
	
	'cedente' => 'Fundação de Desenvolvimento da Unicamp - Funcamp',
	'identificacao' => 'Fundação de Desenvolvimento da Unicamp - Funcamp',
	'cpf_cnpj' => '49.607.336/0001-06',
	'endereco' => 'Avenida Érico Veríssimo, 1251 Campus Unicamp',
	'cidade_uf' => 'Campinas - SP',
	
	'instrucoes1' => '- Sr. Caixa, não receber após vencimento',
	
	'quantidade' => '',
	'valor_unitario' => '',
	'aceite' => '',		
	'especie' => 'R$',
	'moeda' => 9,
	'especie_doc' => '',
));
