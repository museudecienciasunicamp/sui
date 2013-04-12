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

echo $this->Bl->h4Dry(__d('sui', 'Código de inscrição', true));
echo $this->Bl->pDry($application['SuiApplication']['code']);
echo $this->Bl->br();

$subscription_config = $subscription['SuiSubscription']['configuration'];

if (!empty($subscription_config['subscription_steps']['participantes']['equipe_com_nome']))
{
	echo $this->Bl->h4Dry(__d('sui', 'Equipe', true));
	echo $this->Bl->pDry($application['SuiApplication']['team_name']);
	echo $this->Bl->br();
}

echo $this->Bl->h4Dry(__d('sui', 'Escola / instituição', true));
echo $this->Bl->sp();
	echo h($application['SuiInstitution']['name']);
	echo '&ensp;(';
	echo h($application['SuiInstitution']['city']);
	echo ' &ndash; ';
	echo h($application['SuiInstitution']['state']);
	echo ')';
echo $this->Bl->ep();
echo $this->Bl->br();


$capitao = Set::extract('/SuiApplicationsSuiUser[role_code=capitao]/SuiUser/full_name', $application);
if (!empty($capitao))
{
	echo $this->Bl->h4Dry(__d('sui', 'Capitão da equipe', true));
	echo $this->Bl->pDry($capitao[0]);
	echo $this->Bl->br();
}

$orientador = Set::extract('/SuiApplicationsSuiUser[role_code=orientador]/SuiUser/full_name', $application);
if (!empty($orientador))
{
	echo $this->Bl->h4Dry(__d('sui', 'Orientador', true));
	if (!empty($orientador))
		echo $this->Bl->pDry($orientador[0]);
	else
		echo $this->Bl->p(array('class' => 'light small'), null, __d('sui', 'Equipe sem orientador', true));
	echo $this->Bl->br();
}

$membros = Set::extract('/SuiApplicationsSuiUser[role_code=membro]/SuiUser/full_name', $application);
if (!empty($membros))
{
	echo $this->Bl->h4Dry(__d('sui', 'Membros', true));
	echo $this->Bl->sp();
	foreach ($membros as $membro)
		echo h($membro), $this->Bl->br();
	echo $this->Bl->ep();
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
