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

echo $this->Bl->sbox(null, array('size' => array('M' => 7, 'g' => -1), 'type' => 'cloud'));
	echo $this->Bl->h2Dry(__d('sui', 'pendências', true));
	if (empty($incompleteApplications))
	{
		echo $this->Bl->pDry(
			__d('sui', 'Não há pendências para você.', true)
		);
	}
echo $this->Bl->ebox();

if (!empty($completedApplications))
{
	echo $this->Bl->sbox(null, array('size' => array('M' => 5, 'g' => -1), 'type' => 'cloud'));
		echo $this->Bl->h2Dry(__d('sui', 'completas', true));
	echo $this->Bl->ebox();
}


if (!empty($availableSubscriptions))
{
	echo $this->Bl->sbox(array('class' => 'wandering_cloud'), array('size' => array('M' => 5, 'g' => -1), 'type' => 'cloud'));
		echo $this->Jodel->insertModule('Sui.SuiSubscription', array('list', 'available'), $availableSubscriptions);
	echo $this->Bl->ebox();
}

