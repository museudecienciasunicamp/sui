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

$flash_message = $this->Session->read('Mexc.flash_message');
$this->Session->delete('Mexc.flash_message');
if (!is_array($flash_message) || count($flash_message) < 2)
	$flash_message = array(false, false);

switch ($flash_message[0])
{
	case 'e-mail-found':
		$flash = String::insert(__d('sui', 'Seu usuário é :email', true), array('email' => $this->Bl->bDry($flash_message[2])));
	break;
	
	case 'e-mail-not-found':
		$flash = __d('sui', 'Nenhum cadastro foi encontrado com os dados fornecidos.', true);
	break;
	
	case 'e-mail-not-valid':
		$flash = __d('sui', 'Digite seu e-mail corretamente.', true);
	break;
	
	case 'incorrect-data':
		$flash = __d('sui', 'Favor preencher o formulário corretamente.', true);
	break;
	
	case 'user-not-found': 
		$flash = __d('sui', 'Nenhum usuário foi encontrado com o e-mail fornecido.', true);
	break;
	
	case 'generic-error':
		$flash = __d('sui', 'Houve um problema ao enviar o e-mail de recuperação de senhas. Tente novamente mais tarde.', true);
	break;
	
	case 'e-mail-sent':
		$flash = __d('sui', 'Um e-mail foi enviado para você contendo instruções de como proceder.<br>Atenção: o e-mail enviado tem validade de 48 horas. Depois desse período você terá que preencher este formulário novamente.', true);
	break;
	
	case 'user-invited':
		$flash = __d('sui', 'Sua conta não possui senha, ainda. Você precisa realizar o processo de validação de e-mail, seguindo as instruções enviadas para o seu e-mail. Se você não recebeu essas instruções, você pode :link.', true);
		$flash = String::insert($flash, array(
			'link' => $this->Bl->anchor(
				array(), 
				array(
					'url' => array(
						'plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'reenviar',
						 urlencode($flash_message[2]['SuiUser']['email'])
					)
				),
				'reenviá-las'
			)
		));
	break;
	
	
}

if (!empty($flash))
{
	echo $this->Popup->popup('notice',
		array(
			'type' => $flash_message[1],
			'title' => '',
			'content' => $flash
		)
	);
	$this->BuroOfficeBoy->addHtmlEmbScript('showPopup.curry("notice").defer();');
}

echo $this->Bl->sbox(null, array('size' => array('M' => 6, 'g' => -1), 'type' => 'cloud'));
	echo $this->Bl->h2Dry(__d('sui', 'Recuperar dados da conta', true));
	
	echo $this->Bl->sdiv(array('class' => 'sui_form_container sui_lost'), array());
		echo $this->Bl->sdiv(array('class' => 'sui_lost_data'), array());
			echo $this->Form->create('SuiUser', array('url' => array('plugin' => 'sui', 'controller' => 'sui_users', 'action' => 'recuperar')));
			
				echo $this->Buro->input(null,
					array(
						'label' => __d('sui', 'Perdeu a senha ou usuário (e-mail) com o qual se cadastrou?', true),
						'fieldName' => 'lost',
						'type' => 'radio',
						'options' => array(
							'default' => 'senha',
							'hiddenField' => false,
							'options' => array(
								'senha' => __d('sui', 'Perdi minha senha', true),
								'usuario' => __d('sui', 'Não sei que usuário / e-mail usei', true)
							)
						)
					)
				);
				
				echo $this->Bl->sdiv(array('id' => 'sui_lost_senha'));
					echo $this->Buro->input(null,
						array(
							'fieldName' => 'SuiUser.email',
							'label' => __d('sui', 'E-mail', true),
							'type' => 'text',
						)
					);
				echo $this->Bl->ediv();
				
				echo $this->Bl->sdiv(array('id' => 'sui_lost_email'));
					echo $this->Buro->input(null,
						array(
							'fieldName' => 'SuiUser.telefone',
							'label' => __d('sui', 'Telefone', true),
							'instructions' => __d('sui', 'Informe o telefone cadastrado para recuperar o usuário', true),
							'type' => 'text',
						)
					);
					$scripts[] = "new MaskedInput('#SuiUserTelefone', '(99) 9999-9999?9');";
					
					echo $this->Buro->input(null,
						array(
							'fieldName' => 'SuiUser.senha',
							'label' => __d('sui', 'Senha', true),
							'instructions' => __d('sui', 'E informe também a senha', true),
							'type' => 'password',
						)
					);
				echo $this->Bl->ediv();
				echo $this->Bl->floatbreak();
				echo $this->Bl->verticalSpacer();
				
				echo $this->Bl->button(
					array('type' => 'submit'),
					array(),
					__d('sui', 'Enviar', true)
				);
				
				$this->Html->script('maskedinput', array('inline' => false));
				echo $this->BuroOfficeBoy->addHtmlEmbScript(implode($scripts));
	
			echo $this->Form->end();
		echo $this->Bl->ediv();
	echo $this->Bl->ediv();
		
	
	$script = "
		new (Class.create({
			initialize: function()
			{
				if (document.loaded) this.loaded();
				else document.observe('dom:loaded', this.loaded.bind(this));
			},
			loaded: function(ev)
			{
				this.inputUsuario = $('SuiUserLostUsuario').observe('change', this.inputChange.bind(this));
				this.inputSenha = $('SuiUserLostSenha').observe('change', this.inputChange.bind(this));
				this.render();
			},
			inputChange: function(ev)
			{
				this.render();
			},
			render: function()
			{
				if (this.inputSenha.checked) 
				{
					$('sui_lost_senha').show();
					$('sui_lost_email').hide().select('input').each(function(input){input.value = '';});
				}
				else 
				{
					$('sui_lost_email').show();
					$('sui_lost_senha').hide().select('input').each(function(input){input.value = '';});
				}
			}
		}))();
	";
	
	echo $this->BuroOfficeBoy->addHtmlEmbScript($script);	
echo $this->Bl->ebox();
