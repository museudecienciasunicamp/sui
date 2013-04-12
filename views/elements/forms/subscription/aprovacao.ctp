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

function printLog($_this)
{
	echo $_this->Bl->boxContainer(
		array(), array('size' => array('M' => 9, 'g' => -1, 'm' => -2)),
		$_this->Bl->hr(array('class' => 'dotted'))
	);
	echo $_this->Bl->floatBreak();
	echo $_this->Bl->br();
	echo $_this->Bl->h4Dry(__d('sui', 'Histórico', true));
	echo $_this->Bl->br();
	echo $_this->element('forms/subscription/aprovacao_log', array('plugin' => 'sui', 'viewer' => 'user'));
}

function lastFeedback($_this, $last_feedback)
{
	if (empty($last_feedback['comment']))
	{
		return;
	}
	
	echo $_this->Bl->h6Dry(__d('sui', 'Última mensagem do museu para você:', true));
	echo $_this->Bl->stextarea(array('disabled' => 'disabled', 'rows' => 7));
		echo $last_feedback['comment'];
	echo $_this->Bl->etextarea();
	echo $_this->Bl->span(
		array(), array(),
		String::insert(
			__d('sui', 'Enviada em :date às :time', true),
			array(
				'date' => date('j/n/Y', strtotime($last_feedback['created'])),
				'time' => date('H:i', strtotime($last_feedback['created']))
			)
		)
	);
}

echo $this->Buro->sform(
		null, array(
			'model' => 'Sui.SuiApplication',
			'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'save_step', $data['SuiSubscription']['slug'], $sui_application_id, $step),
			'callbacks' => array(
				'onComplete' => array('js' => 'if((json = response.responseJSON) && json.redirect) location.href = json.redirect')
			)
		)
	);
echo $this->Buro->eform();


switch ($application['SuiApplication']['status'])
{
	case 'in_proccess':
		switch ($application['SuiApplication']['step_status'])
		{
			case 'started':
				echo $this->Bl->h4Dry(__d('sui', 'Sua inscrição foi enviada ao museu e está aguardando aprovação. Quando for aprovada, você receberá uma mensagem no seu e-mail.', true));
				break;

			case 'waiting_user_feedback':
				echo $this->Bl->h4Dry(__d('sui', 'Sua inscrição necessita de atenção. Veja a mensagem do museu e responda no campo abaixo.', true));

				echo $this->Bl->boxContainer(
					array(), array('size' => array('M' => 9, 'g' => -1, 'm' => -2)),
					$this->Bl->hr(array('class' => 'dotted'))
				);
				echo $this->Bl->floatBreak();

				$last_feedback = $application['SuiFeedback'][0];
				lastFeedback($this, $last_feedback);

				echo $this->Bl->boxContainer(
					array(), array('size' => array('M' => 9, 'g' => -1, 'm' => -2)),
					$this->Bl->hr(array('class' => 'dotted'))
				);
				echo $this->Bl->floatBreak();

				echo $this->Buro->sform(
						null, array(
							'model' => 'Sui.SuiFeedback',
							'url' => array('plugin' => 'sui', 'controller' => 'sui_feedbacks', 'action' => 'answer', $sui_application_id),
							'callbacks' => array(
								'onSave' => array('js' => 'location.reload()'),
								'onRejected' => array('popup' => 'asd')
							)
						)
					);
				
					echo $this->Buro->input(
						array(),
						array(
							'fieldName' => 'answer',
							'label' => __d('sui', 'Sua resposta', true),
							'type' => 'textarea',
						)
					);

					if ($last_feedback['allow_attachment'])
					{
						echo $this->Buro->input(
							array(),
							array(
								'fieldName' => 'attachment',
								'label' => __d('sui', 'Anexo', true),
								'type' => 'upload'
							)
						);
					}

					echo $this->Bl->br();

					echo $this->Buro->submit(
						array(),
						array('label' => __d('sui', 'Enviar mensagem', true))
					);
					
				echo $this->Buro->eform();
				echo $this->Bl->br();

				printLog($this);
				
				break;

			case 'waiting_approval':
				echo $this->Bl->h4Dry(__d('sui', 'A equipe do museu está analisando sua inscrição. Quando for aprovada, você receberá uma mensagem no seu e-mail.', true));

				printLog($this);

				break;

			case 'approved':
				echo $this->Bl->h4Dry(__d('sui', 'A sua inscrição foi aprovada pelo Museu e poderá seguir adiante. Prossiga com a sua inscrição clicando no botão "Próxima etapa" logo abaixo.', true));
				echo $this->Bl->hr(array('class' => 'dotted'));

				lastFeedback($this, $application['SuiFeedback'][0]);

				echo $this->Bl->hr(array('class' => 'dotted'));
				echo $this->Bl->br();
				
				printLog($this);
				
				break;
		}
		break;

	case 'rejected':
		echo $this->Bl->h4Dry(__d('sui', 'Não será mais possível continuar pois a equipe do museu não aprovou a sua inscrição.', true));
		echo $this->Bl->hr(array('class' => 'dotted'));

		lastFeedback($this, $application['SuiFeedback'][0]);

		echo $this->Bl->hr(array('class' => 'dotted'));
		echo $this->Bl->br();
			
			printLog($this);
			
			break;
}
