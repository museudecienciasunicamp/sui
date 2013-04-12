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

	function valueOrDash($value)
	{
		if (empty($value))
			$value = '&ndash;';
		return "<span>$value</span>";
	}

	$applicationStatuses = array(
		'rejected' => __d('sui', 'Rejeitada', true),
		'completed' => __d('sui', 'Finalizada', true),
		'cancelled' => __d('sui', 'Cancelada', true),
		'expired' => __d('sui', 'Perdeu prazo', true),
		'in_proccess' => __d('sui', 'Em processo', true)
	);

	Configure::load('Sui.types');
	$tipos = Configure::read('Sui.InstitutionTypes');
	$professorTypes = Configure::read('Sui.ProfessorTypes');

	echo $this->Bl->sboxContainer(array('class' => 'data_popin'), array('size' => array('M' => 9, 'g' => -1)));
		echo $bl->h2Dry(__('Dados da pessoa', true));

		echo $this->Bl->sboxContainer(array('class' => 'content_data_popin'));

			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->labelDry('Nome') . valueOrDash($data['SuiUser']['full_name'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 4, 'g' => -1)),
				$this->Bl->labelDry('E-mail') . valueOrDash($data['SuiUser']['email'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 2, 'g' => -1)),
				$this->Bl->labelDry('Tipo') . valueOrDash($data['SuiUsersType']['name'])
			);

			echo $this->Bl->floatBreak();
			echo $this->Bl->br();

			if ($data['SuiUsersType']['id'] == 'professor')
			{
				echo $this->Bl->box(
					array(), array('size' => array('M' => 3, 'g' => -1)),
					$this->Bl->labelDry('Tipo de professor') . valueOrDash($professorTypes[$data['SuiUser']['professor_type']])
				);

				$subjects = Set::extract($data, '/SuiSubject/name');
				echo $this->Bl->box(
					array(), array('size' => array('M' => 6, 'g' => -1)),
					$this->Bl->labelDry('Matérias') . valueOrDash(implode($subjects))
				);

				echo $this->Bl->floatBreak();
				echo $this->Bl->br();
			}

			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->labelDry('Situação da conta') . __d('sui', $data['SuiUser']['user_status'], true)
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 4, 'g' => -1)),
				$this->Bl->labelDry('Série') . valueOrDash($data['SuiGrade']['name'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 2, 'g' => -1)),
				$this->Bl->labelDry('Nascimento') . valueOrDash($data['SuiUser']['birthdate'])
			);

			echo $this->Bl->floatBreak();
			echo $this->Bl->br();

			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->labelDry('CPF') . valueOrDash($data['SuiUser']['cpf'])
			);

			echo $this->Bl->floatBreak();
			echo $this->Bl->br();
			echo $this->Bl->br();

			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -3)),
				$this->Bl->h3Dry(__d('sui','Endereço', true))
			);
			echo $this->Bl->floatBreak();

			echo $this->Bl->box(
				array(), array('size' => array('M' => 6, 'g' => -1)),
				$this->Bl->labelDry('Endereço') . valueOrDash($data['SuiUser']['address'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 1, 'g' => -1)),
				$this->Bl->labelDry('Número') . valueOrDash($data['SuiUser']['number'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 2, 'g' => -1)),
				$this->Bl->labelDry('CEP') . valueOrDash($data['SuiUser']['cep'])
			);

			echo $this->Bl->floatBreak();
			echo $this->Bl->br();

			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->labelDry('Complemento') . valueOrDash($data['SuiUser']['address_2'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->labelDry('Cidade') . valueOrDash($data['SuiUser']['city'] . ' - ' . $data['SuiUser']['state'])
			);

			echo $this->Bl->floatBreak();
			echo $this->Bl->br();

			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->labelDry('Tel 1') . valueOrDash($data['SuiUser']['phone1'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->labelDry('Tel 2') . valueOrDash($data['SuiUser']['phone2'])
			);
			
			echo $this->Bl->floatBreak();
		echo $this->Bl->eboxContainer();

		echo $this->Bl->floatBreak();
		echo $this->Bl->br();

		echo $this->Bl->sboxContainer(array('class' => 'content_data_popin'));
			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -3)),
				$this->Bl->h3Dry(__d('sui','Instituições', true))
			);
			echo $this->Bl->floatBreak();

			echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), $this->Bl->labelDry('Nome'));
			echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), $this->Bl->labelDry('Tipo'));
			echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), $this->Bl->labelDry('Cidade'));
			echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), $this->Bl->labelDry('CEP'));

			echo $this->Bl->floatBreak();
			
			foreach($data['SuiInstitution'] as $institution)
			{
				echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), valueOrDash($institution['name']));
				echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), valueOrDash($tipos[$institution['type']]));
				echo $this->Bl->box(array(), array('size' => array('M' => 3, 'g' => -1)), valueOrDash("{$institution['city']} - {$institution['state']}"));
				echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), valueOrDash($institution['cep']));
			}	
			echo $this->Bl->floatBreak();
		echo $this->Bl->eboxContainer();
		
		echo $this->Bl->floatBreak();
		echo $this->Bl->br();

		echo $this->Bl->sboxContainer(array('class' => 'content_data_popin'));
			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -3)),
				$this->Bl->h3Dry(__d('sui','Inscrições', true))
			);
			echo $this->Bl->floatBreak();

			if (empty($data['SuiApplication']) && empty($data['SuiApplicationsSuiUser']))
			{
				echo $this->Bl->box(
					array(), array('size' => array('M' => 5, 'g' => -1)),
					__d('sui', 'Usuário sem inscrições', true)
				);
			}
			else
			{
				echo $this->Bl->box(array(), array('size' => array('M' => 4, 'g' => -1)), $this->Bl->labelDry('Programa'));
				echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), $this->Bl->labelDry('Código'));
				echo $this->Bl->box(array(), array('size' => array('M' => 2, 'g' => -1)), $this->Bl->labelDry('Etapa'));
				echo $this->Bl->box(array(), array('size' => array('M' => 1, 'g' => -1)), $this->Bl->labelDry('Responsável'));

				echo $this->Bl->floatBreak();

				$responsible = Set::extract('/SuiApplication/id', $data);

				$allApplications = $data['SuiApplication'];
				if (!empty($data['SuiApplicationsSuiUser']))
					$allApplications += Set::extract('/SuiApplication/.', $data['SuiApplicationsSuiUser']);

				$printedApplications = array();
				foreach($allApplications as $application)
				{
					if (in_array($application['id'], $printedApplications))
						continue;
					$printedApplications[] = $application['id'];

					echo $this->Bl->box(array(), array('size' => array('M' => 4, 'g' => -1)), valueOrDash($application['SuiSubscription']['title']));
					echo $this->Bl->box(
						array(), array('size' => array('M' => 2, 'g' => -1)),
						$this->Bl->anchor(
							array('target' => '_blank'),
							array('url' => array('plugin' => 'backstage', 'controller' => 'back_contents', 'action' => 'edit', 'sui_application', $application['id'])),
							$application['code']
						)
					);
					echo $this->Bl->box(
						array(), array('size' => array('M' => 2, 'g' => -1)),
						valueOrDash($applicationStatuses[$application['status']])
					);
					echo $this->Bl->box(
						array(), array('size' => array('M' => 1, 'g' => -1)),
						in_array($application['id'], $responsible) ? 'Sim' : 'Não'
					);
					echo $this->Bl->floatBreak();
				}
			}
			echo $this->Bl->floatBreak();
		echo $this->Bl->eboxContainer();
		echo $this->Bl->floatBreak();
	echo $bl->eboxContainer();
