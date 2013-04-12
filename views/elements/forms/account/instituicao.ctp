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
	
	echo $this->Buro->sform(null, 
		array(
			'model' => 'Sui.SuiUser', 
			'url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'validates', $uuid),
			'callbacks' => array(
				'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
			)
	));

		echo $this->Bl->br();
		echo $this->Bl->pDry(__d('sui', 'Possui vínculo com instituição?', true));
		echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'g' => -1), 'type' => 'column_container'));
			echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'g' => -1, 'm' => -1), 'type' => 'inner_column'));
				echo $this->Buro->input(
					array('id' => $has_institution_id = $this->uuid('input', 'cadastro')),
					array(
						'fieldName' => 'SuiUser.has_institution',
						'label' => false,
						'type' => 'select',
						'options' => array(
							'options' => array(
								'yes' => __d('sui', 'Sim', true),
								'no' => __d('sui', 'Não', true),
							),
							'default' => 'no'
						)
					)
				);
			echo $this->Bl->ebox();
		echo $this->Bl->eboxContainer();
		
		echo $this->Bl->floatBreak();
		
		$script = "
			new (Class.create({
				initialize: function()
				{
					if (document.loaded)	this.loaded();
					else document.observe('dom:loaded', this.loaded.bind(this));
				},
				loaded: function(ev)
				{
					this.input = $('$has_institution_id');
					this.input.on('change', this.render.bind(this));
					this.render();
				},
				render: function(ev)
				{
					if (this.input.value == 'yes')
						$('has_institution_yes').show();
					else
						$('has_institution_yes').hide();
				}
			}))();
		";
		
		echo $this->Bl->sdiv(array('id' => 'has_institution_yes'));

			echo $this->Bl->br();
			echo $this->Bl->sp();
				if (isset($thirdPerson) && $thirdPerson)
					__d('sui', 'Na instituição, a pessoa é');
				else
					__d('sui', 'Na instituição, você é');
			echo $this->Bl->ep();
			echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'g' => -1), 'type' => 'column_container'));
				echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'g' => -1, 'm' => -1), 'type' => 'inner_column'));
					echo $this->Buro->input(
						array('id' => $user_type_id = $this->uuid('input', 'cadastro')),
						array(
							'fieldName' => 'SuiUser.sui_users_type_id',
							'label' => false,
							'type' => 'select'
						)
					);
				echo $this->Bl->ebox();
			echo $this->Bl->eboxContainer();
			
			echo $this->Bl->sdiv(array('class' => 'sui_estudante_specific sui_specific_form'));
			
				echo $this->Bl->sboxContainer(null, array('size' => array('g' => 2, 'u' => 5), 'type' => 'column_container'));
					echo $this->Bl->sbox(null, array('size' => array('g' => 2), 'type' => 'inner_column'));
						echo $this->Bl->span(array('style' => 'line-height:40px'), null, 'do');
					echo $this->Bl->ebox();
				echo $this->Bl->eboxContainer();

			
				echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'g' => 1, 'm' => 1, 'u' => -5), 'type' => 'column_container'));
					echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'g' => 1, 'm' => 1, 'u' => -5), 'type' => 'inner_column'));
					echo $this->Buro->input(null,
						array(
							'fieldName' => 'SuiUser.sui_grade_id',
							'label' => false,
							'type' => 'select',
							'options' => array(
								'empty' => true
							)
						)
					);
					echo $this->Bl->ebox();
				echo $this->Bl->eboxContainer();
			
			echo $this->Bl->ediv();
			
			echo $this->Bl->sdiv(array('class' => 'sui_outro_specific sui_specific_form'));
				echo $this->Buro->pDry(__d('sui', 'O que?', true));
				echo $this->Buro->input(null,
					array(
						'fieldName' => 'SuiUser.user_type_description',
						'label' => false,
						'type' => 'text',
					)
				);
			echo $this->Bl->ediv();
			
			echo $this->Bl->sdiv(array('class' => 'sui_professor_specific sui_specific_form'));
				
				echo $this->Bl->sboxContainer(null, array('size' => array('g' => 2, 'u' => 5), 'type' => 'column_container'));
					echo $this->Bl->sbox(null, array('size' => array('g' => 2), 'type' => 'inner_column'));
						echo $this->Bl->span(array('style' => 'line-height:40px'), null, 'de');
					echo $this->Bl->ebox();
				echo $this->Bl->eboxContainer();


				$professorTypes = Configure::read('Sui.ProfessorTypes');
				if (empty($professorTypes))
				{
					Configure::load('Sui.types');
					$professorTypes = Configure::read('Sui.ProfessorTypes');
				}

				echo $this->Bl->sboxContainer(null, array('size' => array('M' => 2, 'g' => 1, 'm' => 1, 'u' => -5), 'type' => 'column_container'));
					echo $this->Bl->sbox(null, array('size' => array('M' => 2, 'g' => 1, 'm' => 1, 'u' => -5), 'type' => 'inner_column'));
					echo $this->Buro->input(array('id' => $professor_type_id = $this->uuid('input', 'cadastro')),
						array(
							'fieldName' => 'SuiUser.professor_type',
							'label' => false,
							'type' => 'select',
							'options' => array(
								'options' => $professorTypes
							)
						)
					);
					echo $this->Bl->ebox();
				echo $this->Bl->eboxContainer();

				echo $this->Bl->floatBreak();
				
				echo $this->Bl->sdiv(array('class' => 'sui_subjects_form'));
					echo $this->Buro->pDry(__d('sui', 'Que matérias ensina? *', true));
					echo $this->Buro->input(null,
						array(
							'fieldName' => 'SuiSubject',
							'label' => false,
							'type' => 'multiple_checkbox',
							'container' => array('class' => 'sui_subjects'),
						)
					);

					echo $this->Form->input('another', array(
						'type' => 'checkbox', 
						'label' => __d('sui', 'Outra', true),
						'id' => $another_checkbox_id = $this->uuid('input', 'cadastro')
					));
				
					echo $this->Bl->br();
					echo $this->Buro->input(
						array('id' => $another_input_id = $this->uuid('input', 'cadastro')),
						array(
							'fieldName' => 'another_subject',
							'label' => __d('sui','Qual?', true),
						)
					);
				echo $this->Bl->ediv();
			echo $this->Bl->ediv();

			echo $this->Bl->floatBreak();
			
			if (!(isset($showInstitutions) && $showInstitutions == false))
			{
			
				// List of all linked institutions
				$hasInstitution = !empty($data['SuiInstitution']);
				$this->set(compact('hasInstitution'));
			
				echo $this->Bl->sdiv(array('id' => 'sui_institutions_list'));
					echo $this->Buro->input(null, array('fieldName' => 'SuiInstitution', 'type' => 'hidden'));
					if ($hasInstitution)
						foreach ($data['SuiInstitution'] as $SuiInstitution)
							echo $this->Jodel->insertModule('Sui.SuiInstitution', array('preview'), compact('SuiInstitution'));
				echo $this->Bl->ediv();
			
				echo $this->Buro->input(null, 
					array(
						'fieldName' => 'sui_institution',
						'type' => 'hidden'
					)
				);
			
				echo $this->Jodel->insertModule('Sui.SuiInstitution', array('search'));
		
			}
		echo $this->Bl->ediv();
	
	echo $this->Buro->eform();
	
	$script .= "
		new (Class.create({
			initialize: function()
			{
				if (document.loaded)	this.loaded();
				else document.observe('dom:loaded', this.loaded.bind(this));
			},
			loaded: function(ev)
			{
				this.input = $('$user_type_id');
				this.input.observe('change', this.inputChange.bind(this));
				this.render();
			},
			inputChange: function(ev){this.render();},
			render: function()
			{
				$$('.sui_specific_form').invoke('hide');
				$$('.sui_'+this.input.value+'_specific').invoke('show');
			}
		}))();
	";
	
	$script .= "
		new (Class.create({
			initialize: function()
			{
				if (document.loaded) this.loaded();
				else document.observe('dom:loaded', this.loaded.bind(this));
			},
			loaded: function(ev)
			{
				this.input = $('$another_input_id');
				this.checkbox = $('$another_checkbox_id');
				this.checkbox.observe('change', this.inputChange.bind(this));
				this.render();
			},
			inputChange: function(ev){this.render();},
			render: function()
			{
				if (this.checkbox.checked) this.input.up('div.input').show();
				else this.input.up('div.input').hide();
			}
		}))();
	";
	
	$script .= "
		new (Class.create({
			initialize: function()
			{
				if (document.loaded)	this.loaded();
				else document.observe('dom:loaded', this.loaded.bind(this));
			},
			loaded: function(ev)
			{
				this.input = $('$professor_type_id');
				this.input.observe('change', this.inputChange.bind(this));
				this.render();
			},
			inputChange: function(ev){this.render();},
			render: function()
			{
				$$('.sui_subjects_form').invoke('hide');
				if (this.input.value == 'medio' || this.input.value == 'fundamental')
				{
					$$('.sui_subjects_form').invoke('show');
				}
			}
		}))();
	";
	
	
	echo $this->BuroOfficeBoy->addHtmlEmbScript($script);
	

