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

if (empty($application['SuiFeedback']))
{
	echo $this->Bl->pDry(__d('sui', 'Nada no histórico, ainda.', true));
}

$viewer = isset($viewer) ? $viewer : 'user';
$last = count($application['SuiFeedback'])-1;

foreach ($application['SuiFeedback'] as $n => $feedback)
{
	if (!empty($feedback['answered']))
	{
		echo $this->Bl->spanDry(String::insert(
			__d('sui', ':user, em :date às :time', true),
			array(
				'user' => $viewer == 'admin' ? $application['SuiUser']['full_name'] : __d('sui', 'Você', true),
				'date' => date('j/n/Y', strtotime($feedback['answered'])),
				'time' => date('H:i', strtotime($feedback['answered']))
			)
		));
		echo $this->Bl->br();
		echo $this->Bl->sbox(array('class' => 'fixed'), array('size' => array('M' => 5, 'g' => 0)));
			echo h($feedback['answer']);
			if (!empty($feedback['attachment']))
			{
				echo $this->Bl->br();
				echo $this->Bl->anchor(
					array(), array('url' => $this->Bl->fileURL($feedback['attachment'], '', true)),
					__d('sui', 'Anexo enviado', true)
				);
			}
		echo $this->Bl->ebox();
		echo $this->Bl->floatBreak();

		
		echo $this->Bl->br();
	}

	if (!($n == 0 && $application['SuiApplication']['step_status'] == 'waiting_user_feedback') || $viewer == 'admin')
	{
		echo $this->Bl->spanDry(String::insert(
			__d('sui', ':user, em :date às :time', true),
			array(
				'user' => $viewer == 'admin' ? 'Museu (' . $feedback['UserUser']['name'] .')' : 'Museu',
				'date' => date('j/n/Y', strtotime($feedback['created'])),
				'time' => date('H:i', strtotime($feedback['created']))
			)
		));
		echo $this->Bl->br();

		if ($n == $last && $application['SuiApplication']['step_status'] == 'approved')
			echo $this->Bl->pDry('Esta inscrição foi aprovada.');
			
		echo $this->Bl->sbox(array('class' => 'fixed'), array('size' => array('M' => 5, 'g' => 0)));
			echo h($feedback['comment']);
		echo $this->Bl->ebox();
		echo $this->Bl->floatBreak();
		
		echo $this->Bl->br();
	}
}
