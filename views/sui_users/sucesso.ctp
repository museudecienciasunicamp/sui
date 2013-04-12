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

echo $this->Bl->sbox(null, array('size' => array('M' => 4), 'type' => 'cloud'));
	echo $this->Bl->h1Dry(__d('sui', 'Sucesso', true));
	echo $this->Bl->pDry('Em instantes você receberá um e-mail com as instruções para validar o seu cadastro. Lembre-se de checar a caixa de spam de seu e-mail, e também de desativar qualquer bloquador automático de endereços desconhecidos (ou então adicionar o endereço "nao-responda@museudeciencias.com.br" na lista de e-mails conhecidos/autorizados/confiáveis).');
echo $this->Bl->ebox();
