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
 
$config['Sui.InstitutionTypes'] = array(
	'public' => __d('sui', 'Pública', true),
	'private' => __d('sui', 'Privada', true)
);

$config['Sui.InstitutionSubTypes'] = array(
	'municipal' => __d('sui', 'Municipal', true),
	'state' => __d('sui', 'Estadual', true),
	'federal' => __d('sui', 'Federal', true)
);

$config['Sui.ProfessorTypes'] = array(
	'pre_escolar' => __d('sui', 'Ensino pré-escolar', true),
	'fundamental' => __d('sui', 'Ensino fundamental', true),
	'medio' 	  => __d('sui', 'Ensino médio', true),
	'tecnico' 	  => __d('sui', 'Ensino técnico/profissionalizante', true),
	'superior' 	  => __d('sui', 'Ensino superior', true),
);
