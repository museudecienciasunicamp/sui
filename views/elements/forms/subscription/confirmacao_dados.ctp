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

	$subscription_config = $subscription['SuiSubscription']['configuration'];
	
	$text = Set::extract('/SuiText[type=confirmacao_dados]', $data);
	if (!empty($text[0]['SuiText']['text']))
		echo $this->Bl->paraDry(explode("\n", $text[0]['SuiText']['text'])), $this->Bl->br();
	
	
	echo $this->Bl->sdiv(array('style' => 'border: 1px solid black; padding: 5px;'));
		// Team name
		if (!empty($subscription_config['subscription_steps']['participantes']['equipe_com_nome']))
		{
			echo $this->Bl->h4Dry(__d('sui', 'Equipe', true));
			echo $this->Bl->pDry($application['SuiApplication']['team_name']);
		
			echo $this->Bl->br();
		}

	
		// Institution
		if (!empty($subscription_config['subscription_steps']['instituicao']))
		{
			echo $this->Bl->h4Dry(__d('sui', 'Escola / Instituição', true));
			if (empty($application['SuiInstitution']['id']))
			{
				echo $this->Bl->pDry('Esta inscrição não está vinculada com uma instituição.');
				echo $this->Bl->br();
			}
			else
			{
				echo $this->Bl->sp();
					echo $application['SuiInstitution']['name'];
					if (!empty($application['SuiInstitution']['unity_name']))
						echo ' &ndash; ', $application['SuiInstitution']['unity_name'];
				
					echo $this->Bl->br();
				
					echo $this->Bl->si();
						Configure::load('Sui.types');
						$type = Configure::read("Sui.InstitutionTypes.{$application['SuiInstitution']['type']}");
						if (!empty($type))
						{
							echo 'Instituição ', $type;
							if (!empty($application['SuiInstitution']['sub_type']))
								echo ', ', Configure::read("Sui.InstitutionSubTypes.{$application['SuiInstitution']['sub_type']}");
						}
					echo $this->Bl->ei();
					echo $this->Bl->br();
				
					echo $application['SuiInstitution']['address'], ', ', $application['SuiInstitution']['number'];
					echo $this->Bl->br();
					echo $application['SuiInstitution']['city'], ' &ndash; ', $application['SuiInstitution']['state'];
					echo $this->Bl->br();
				echo $this->Bl->ep();
				echo $this->Bl->br();
			}
		}
	
	
		// Members
		if (!empty($subscription_config['subscription_steps']['participantes']['papeis']))
		{
			foreach ($subscription_config['subscription_steps']['participantes']['papeis'] as $role => $role_config)
			{
				$users = Set::extract("/SuiApplicationsSuiUser[role_code=$role]", $application);
				if (count($users) == 1) $title = $role_config['title'];
				else					$title = $role_config['title_pl'];
			
				echo $this->Bl->br();
				echo $this->Bl->h4Dry($title);
			
				foreach ($users as $user)
				{
					echo $this->Jodel->insertModule('Sui.SuiUser', array('preview', 'confirmacao_dados_step'), $user['SuiApplicationsSuiUser']);
				}
			}
			echo $this->Bl->br();
		}


		// Extra data
		if (!empty($subscription_config['subscription_steps']['dados_especificos']))
		{
			foreach ($subscription_config['subscription_steps']['dados_especificos']['formulario'] as $field => $field_config)
			{
				$field_content = '';
				if (isset($application['SuiApplication']['extra_data'][$field]))
					$field_content = $application['SuiApplication']['extra_data'][$field];

				switch ($field_config['tipo'])
				{
					case 'texto_grande':
						$size = 6;
					case 'texto':
						$field_content = h($field_content);
						break;

					case 'opcoes':
						if (isset($field_config['opcoes'][$field_content]))
							$field_content = $field_config['opcoes'][$field_content];
						break;

					case 'multiplas_opcoes':
						$list = '';
						$size = 6;
						if (empty($field_content))
						{
							break;
						}

						foreach ($field_content as $item)
						{
							$list .= $this->Bl->liDry($field_config['opcoes'][$item]);
						}

						if (!empty($list))
						{
							$field_content = $this->Bl->ulDry($list);
						}
						break;

					case 'anexo':
						$field_content = $this->Bl->anchor(
							array(),
							array(
								'url' => $this->Bl->fileURL($field_content, '', true)
							),
							__d('sui', 'Baixar anexo', true)
						);
						break;
				}
				if (!empty($field_content))
				{
					echo $this->Bl->h4Dry($field_config['rotulo']);
					echo $this->Bl->br();
					echo $this->Bl->spanDry($field_content);
					echo $this->Bl->br();
					echo $this->Bl->br();
				}
			}
		}
	
	echo $this->Bl->ediv();

	echo $this->Bl->br(),$this->Bl->br();
	
	echo $this->Bl->sp();
		echo 'Caso haja algum dos seguintes erros:<br> ';
		echo '1. erro no e-mail de algum dos cadastrados;<br> ';
		echo '2. erro nos dados da instituição;<br> ';
		if (!empty($subscription_config['subscription_steps']['participantes']['equipe_com_nome']))
			echo '3. erro no nome de equipe;<br> ';
	echo $this->Bl->ep();
	echo $this->Bl->sp();
		echo 'Para corrigir é necessário cancelar esta inscrição e começar de novo. Você pode fazer isto ';
		echo $this->Bl->anchor(array(), array('url' => array('plugin' => 'sui', 'controller' => 'sui_applications', 'action' => 'cancelar', $application['SuiApplication']['id'])), 'clicando aqui');
		echo '.';
	echo $this->Bl->ep();

	if (!empty($subscription_config['subscription_steps']['participantes']))
		echo $this->Bl->pDry('Caso haja erro nos dados de alguns participantes, eles ainda podem ser corrigidos por eles mesmos ao confirmarem seu cadastro. Eles receberam um convite com um link para confirmar o cadastro, nesta confirmação poderão alterar os seus dados.');

	
	echo $this->Bl->br(),$this->Bl->br();
	
	
	echo $this->Buro->sform(null, array(
		'model' => 'Sui.SuiApplication',
		'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'save_step', $data['SuiSubscription']['slug'], $sui_application_id, $step),
		'callbacks' => array(
			'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
		)
	));

		if (!empty($application['SuiInstitution']['id']))
		{
			if (!empty($subscription_config['subscription_steps']['participantes']))
				$confirmo = 'Confirmo que estes dados estão corretos, que todos os alunos são alunos regulares da escola "'.h($application['SuiInstitution']['name']).'", e estou ciente que estes serão os dados que serão usados no certificado de participação e de que não poderei alterá-los.';
			else
				$confirmo = 'Confirmo que estes dados estão corretos, que estou vinculado à escola / instituição "'.h($application['SuiInstitution']['name']).'" e que estou ciente que estes serão os dados utilizados para a minha inscrição e que não poderei alterá-los.';
		}
		else
		{
			$confirmo = 'Confirmo que estes dados estão corretos, que estou ciente que estes serão os dados que serão usados no certificado de participação e que não poderei alterá-los.';
		}
		echo $this->Buro->input(null,
			array(
				'fieldName' => 'check',
				'type' => 'multiple_checkbox',
				'label' => false,
				'options' => array(
					'options' => compact('confirmo')
				)
			)
		 );

	echo $this->Buro->eform();
