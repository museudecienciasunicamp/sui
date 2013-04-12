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

	Configure::load('Sui.types');
	$tipos = Configure::read('Sui.InstitutionTypes');

	echo $this->Bl->sboxContainer(array('class' => 'data_popin'), array('size' => array('M' => 9, 'g' => -1)));
		echo $this->Bl->h2Dry(__('Dados da instituição', true));

		echo $this->Bl->sboxContainer(array('class' => 'content_data_popin'));

			echo $this->Bl->box(
				array(), array('size' => array('M' => 4, 'g' => -1)),
				$this->Bl->labelDry('Nome') . valueOrDash($data['SuiInstitution']['name'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->labelDry('Tipo de instituição') . valueOrDash($data['SuiInstitutionType']['name'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 2, 'g' => -1)),
				$this->Bl->labelDry('Tipo') . valueOrDash($tipos[$data['SuiInstitution']['type']])
			);

			echo $this->Bl->floatBreak();
			echo $this->Bl->br();

			echo $this->Bl->box(
				array(), array('size' => array('M' => 4, 'g' => -1)),
				$this->Bl->labelDry('Endereço') . valueOrDash($data['SuiInstitution']['address'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->labelDry('Complemento') . valueOrDash($data['SuiInstitution']['address_2'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 2, 'g' => -1)),
				$this->Bl->labelDry('Número') . valueOrDash($data['SuiInstitution']['number'])
			);

			echo $this->Bl->floatBreak();
			echo $this->Bl->br();

			echo $this->Bl->box(
				array(), array('size' => array('M' => 4, 'g' => -1)),
				$this->Bl->labelDry('Cidade') . valueOrDash($data['SuiInstitution']['city'] . ' - ' . $data['SuiInstitution']['state'])
			);
			echo $this->Bl->box(
				array(), array('size' => array('M' => 3, 'g' => -1)),
				$this->Bl->labelDry('CEP') . valueOrDash($data['SuiInstitution']['cep'])
			);

			echo $this->Bl->floatBreak();
			echo $this->Bl->br();

			echo $this->Bl->box(
				array(), array('size' => array('M' => 9, 'g' => -1)), $this->Bl->labelDry('Modalidades de ensino')
			);

			echo $this->Bl->sbox(array(), array('size' => array('M' => 9, 'g' => -1)));
				if (empty($data['SuiInstitutionsMode']))
					echo '&ndash;';
				foreach($data['SuiInstitutionsMode'] as $mode)
				{
					if (!empty($mode['name']))
						echo $this->Bl->pDry($mode['name']);
				}
			echo $this->Bl->ebox();
			echo $this->Bl->floatBreak();

		echo $this->Bl->eboxContainer();	
		echo $this->Bl->floatBreak();

	echo $this->Bl->eboxContainer();
	echo $this->Bl->floatBreak();
