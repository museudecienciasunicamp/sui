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

$validated = $notValidated = array();
foreach ($application['SuiApplicationsSuiUser'] as $user)
{
	if ($user['SuiUser']['user_status'] == 'validated')
		$validated[] = $user;
	else
		$notValidated[] = $user;
}

if (!empty($notValidated))
	$text = Set::extract('/SuiText[type=confirmacao_email]', $data);
else
	$text = Set::extract('/SuiText[type=confirmacao_email_tudo_confirmado]', $data);

if (!empty($text[0]['SuiText']['text']))
{
	echo $this->Bl->paraDry(explode("\n", $text[0]['SuiText']['text']));
	echo $this->Bl->br();
	echo $this->Bl->br();
}

if (!empty($notValidated))
{
	echo $this->Bl->anchor(null, array('url' => $this->here), __d('sui', 'Atualizar a lista', true));
	echo $this->Bl->br();
	echo $this->Bl->br();
}

echo $this->Bl->sboxContainer(null, array('size' => array('M' => 6), 'type' => 'column_container'));

	if (!empty($notValidated))
	{
		echo $this->Bl->sbox(null, array('size' => array('M' => 3, 'g' => -1), 'type' => 'inner_column'));
			echo $this->Bl->h4(array('class' => 'red'), null, __d('sui', 'Não confirmados', true));
			echo $this->Bl->hr();
			foreach ($notValidated as $user)
				echo $this->Jodel->insertModule('Sui.SuiUser', array('preview', 'validation_step'), $user);
		echo $this->Bl->ebox();
	
	}

	if (!empty($validated))
	{
		echo $this->Bl->sbox(null, array('size' => array('M' => 3, 'g' => -1), 'type' => 'inner_column'));
			echo $this->Bl->h4(array('class' => 'green'), null, __d('sui', 'Confirmados', true));
			echo $this->Bl->hr();
			foreach ($validated as $user)
				echo $this->Jodel->insertModule('Sui.SuiUser', array('preview', 'validation_step'), $user);
		echo $this->Bl->ebox();
	}

echo $this->Bl->eboxContainer();


echo $this->Buro->sform(null, array(
	'model' => 'Sui.SuiApplication',
	'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'save_step', $data['SuiSubscription']['slug'], $sui_application_id, $step),
	'callbacks' => array(
		'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
	)
));

echo $this->Buro->eform();
