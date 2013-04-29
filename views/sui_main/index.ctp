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

$flash = $this->Session->flash();
if (!empty($flash))
{
	echo $this->Popup->popup('error',
		array(
			'type' => 'notice',
			'title' => '',
			'content' => $flash
		)
	);
	$this->BuroOfficeBoy->addHtmlEmbScript('showPopup.curry("error").defer();');
}
echo $this->Bl->sbox(array('class' => 'sui_user_data_container'), array('size' => array('M' => 12, 'g' => -1), 'type' => 'cloud'));
	
	// Banner
	echo $this->Bl->div(
		array('class' => 'sui_user_welcome'), null,
		$this->Bl->divDry(
			$this->Bl->h3Dry(sprintf(__d('sui', 'Olá, %s!', true), $museuUserData['SuiUser']['full_name']))
			. $this->Bl->spanDry(__d('sui', 'Esta é sua área. Cuide bem ;)', true))
		)
	);
	
	// Current user data
	echo $this->Bl->sboxContainer(null, array('size' => array('M' => 12), 'type' => 'column_container'));
		
		echo $this->Bl->sdiv(array('class' => 'sui_user_image'));
		echo $this->Bl->ediv();
	
		echo $this->Bl->sbox(null, array('size' => array('M' => 4, 'g' => -1), 'type' => 'inner_column'));
			echo $this->Bl->h4Dry(__d('sui', 'Seus dados', true));
			echo $this->Bl->sp();
				echo h($museuUserData['SuiUser']['full_name']);
				echo $this->Bl->br();
				if ($museuUserData['SuiUsersType']['id'] == 1) // Students
				{
					echo sprintf(__d('sui', '%s do %s em %s &ndash; %s', true), $museuUserData['SuiUsersType']['name'], $museuUserData['SuiGrade']['name'], $museuUserData['SuiUser']['city'], $museuUserData['SuiUser']['state']);
				}
				else
				{
					$subjectsList = $this->Text->toList(Set::extract('/SuiSubject/name', $museuUserData), 'e');
					echo sprintf(__d('sui', '%s de %s em %s &ndash; %s', true), $museuUserData['SuiUsersType']['name'], $subjectsList, $museuUserData['SuiUser']['city'], $museuUserData['SuiUser']['state']);
				}
			echo $this->Bl->ep();
			echo $this->Bl->br();
			echo $this->Bl->anchor(null, array('url' => array('controller' => 'sui_users','action' => 'cadastro')), __d('sui', 'Editar informações', true));
		echo $this->Bl->ebox();
	
		echo $this->Bl->sbox(null, array('size' => array('M' => 3, 'g' => -1), 'type' => 'inner_column'));
			echo $this->Bl->h4Dry(__d('sui', 'Contato', true));
			echo $this->Bl->sp();
				echo h($museuUserData['SuiUser']['email']);
				echo $this->Bl->br(), $this->Bl->formatString(null, array('type' => 'phone'), h($museuUserData['SuiUser']['phone1']));
				if (!empty($museuUserData['SuiUser']['phone2']))
					echo $this->Bl->br(), $this->Bl->formatString(null, array('type' => 'phone'), h($museuUserData['SuiUser']['phone2']));
			echo $this->Bl->ep();
		echo $this->Bl->ebox();

		echo $this->Bl->sbox(null, array('size' => array('M' => 3, 'g' => -1), 'type' => 'inner_column'));
			echo $this->Bl->h4Dry(__d('sui', 'Instituições', true));
			echo $this->Bl->sol();
				foreach ($museuUserData['SuiInstitution'] as $SuiInstitution)
					echo $this->Bl->liDry($SuiInstitution['name']);
			echo $this->Bl->eol();
		echo $this->Bl->ebox();
		
	echo $this->Bl->eboxContainer();
	echo $this->Bl->floatBreak();
	
echo $this->Bl->ebox();

$hasSubscriptionCloud = 
	!empty($incompleteApplications) || 
	!empty($completedApplications) || 
	!empty($expiredApplications) || 
	!empty($missingPayments) ||
	!empty($availableSubscriptions) ||
	!empty($applicationHistory) ||
	!empty($activeApplications) ||
	!empty($notifications)
;

$hasNotices = 
	!empty($notices)
;

$hasAvailables = 
	!empty($availableSubscriptions)
;

if ($hasSubscriptionCloud || $hasNotices || $hasAvailables)
{
	echo $this->Bl->box(array(), array('size' => array('M' => 7)),
		$this->Bl->h2Dry('Inscrições')
	);
}

$this->Html->script('/sui/js/sui', array('inline' => false));

if ($hasSubscriptionCloud)
{
	echo $this->Bl->sboxContainer(null, array('size' => array('M' => 7)));
	
		// Active subscriptions
		if (!empty($activeApplications))
		{
			echo $this->Bl->box(
				null, array('size' => array('M' => 7, 'g' => -1), 'type' => 'cloud'),
				$this->Jodel->insertModule('Sui.SuiApplication', array('list', 'active'), $activeApplications)
			);
			echo $this->Bl->floatBreak();
		}

		
		// Pending
		if (!empty($incompleteApplications))
		{
			echo $this->Bl->sbox(null, array('size' => array('M' => 7, 'g' => -1), 'type' => 'cloud'));
				echo $this->Bl->h2Dry('pendências');
				echo $this->Bl->verticalSpacer();

				$grouped_applications = array();
				foreach ($incompleteApplications as $application)
				{
					$group = false;
					switch ($application['SuiApplication']['current_step'])
					{
						case 'pagamento': 
							if ($application['SuiApplication']['step_status'] == 'started')
								$group = 'need_payment';
							elseif ($application['SuiApplication']['step_status'] == 'generated')
								$group = 'waiting_payment';
							break;

						case 'aprovacao':
							if ($application['SuiApplication']['step_status'] == 'waiting_user_feedback')
								$group = 'waiting_user_feedback';
							elseif ($application['SuiApplication']['step_status'] == 'waiting_approval')
								$group = 'waiting_approval';
							else
								$group = 'generic';
							break;
						
						case 'confirmacao_email':
							$group = 'waiting_email_validations';
							break;
						
						default:
							$group = 'generic';
					}
					
					$grouped_applications[$group][] = $application;
				}
				
				foreach ($grouped_applications as $group => $applications)
				{
					echo $this->Bl->ssuiFoldableList(
						array('class' => $group),
						array(
							'count' => count($applications),
							'title' => __d('sui', "Step:$group", true)
						));
						
						if ($group == 'waiting_payment')
						{
							foreach ($waitingPayments as $payment)
								echo $this->Jodel->insertModule('Sui.SuiPayment', array('preview'), $payment);
						}
						
						echo $this->Jodel->insertModule('Sui.SuiApplication', array('preview', 'incomplete'), $applications);
						
					echo $this->Bl->esuiFoldableList();
				
				}

			echo $this->Bl->ebox();
		}
		
		if (!empty($completedApplications))
		{
			echo $this->Bl->sbox(null, array('size' => array('M' => 7, 'g' => -1), 'type' => 'cloud'));
				echo $this->Bl->h2Dry('completas');
				echo $this->Bl->verticalSpacer();
				
				$grouped_applications = array();
				foreach ($completedApplications as $application)
				{
					$grouped_applications[$application['SuiApplication']['sui_subscription_id']][] = $application;
				}
				
				foreach ($grouped_applications as $applications)
				{
					echo $this->Bl->ssuiFoldableList(array(), array(
							'count' => count($applications),
							'title' => $applications[0]['SuiSubscription']['title']
						));
						
						echo $this->Jodel->insertModule('Sui.SuiApplication', array('preview', 'complete'), $applications);
					
					echo $this->Bl->esuiFoldableList();
				}
				
			echo $this->Bl->ebox();
		}
		
		// History
		// Cancelled
		if (!empty($expiredApplications))
		{
			echo $this->Bl->sbox(null, array('size' => array('M' => 7, 'g' => -1), 'type' => 'cloud'));
				echo $this->Bl->h2Dry('expiradas');
				echo $this->Bl->verticalSpacer();
				
				echo $this->Bl->pDry('Abaixo, uma lista de inscrições que não puderam ser completadas pois o prazo máximo para a incrição foi atingido.');
				echo $this->Bl->br();
				
				$grouped_applications = array();
				foreach ($expiredApplications as $application)
				{
					$grouped_applications[$application['SuiApplication']['sui_subscription_id']][] = $application;
				}
				
				foreach ($grouped_applications as $applications)
				{
					echo $this->Bl->ssuiFoldableList(array(), array(
							'count' => count($applications),
							'title' => $applications[0]['SuiSubscription']['title']
						));
						
						echo $this->Jodel->insertModule('Sui.SuiApplication', array('preview', 'expired'), $applications);
					
					echo $this->Bl->esuiFoldableList();
				}
				
			echo $this->Bl->ebox();
		}

	echo $this->Bl->eboxContainer();
}

if ($hasNotices)
{
	echo $this->Bl->sboxContainer(null, array('size' => array('M' => 5)));
		echo $this->Bl->box(
			null, array('size' => array('M' => 5, 'g' => -1)),
			$this->Bl->h2Dry('Notificações')
		);
	
		echo $this->Bl->sbox(null, array('size' => array('M' => 5, 'g' => -1), 'type' => 'cloud'));
		echo $this->Bl->ebox();
	echo $this->Bl->eboxContainer();
}

if ($hasAvailables)
{
	echo $this->Bl->sboxContainer(null, array('size' => array('M' => 5)));
		echo $this->Bl->sbox(null, array('size' => array('M' => 5, 'g' => -1), 'type' => 'cloud'));
			echo $this->Bl->h5Dry(__d('sui', 'Programas e eventos com inscrições abertas', true));
			foreach ($availableSubscriptions as $subscription)
			{
				echo $this->Jodel->insertModule('Sui.SuiSubscription', array('preview', 'available'), $subscription);
			}
		echo $this->Bl->ebox();
	echo $this->Bl->eboxContainer();
}

echo $this->Bl->floatBreak();
