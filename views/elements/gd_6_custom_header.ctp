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

if ($this->JjAuth->can(array('sui_application_sheet')))
{
	echo $this->Bl->sdiv(array('class' => 'sui_application_links'));

		/*echo $this->Popup->popup('planilha_gd', array(
			'type' => 'progress',
			'title' => __d('sui', 'Exportando a planilha do GD', true),
			'url' => array(
				'plugin' => 'sui',
				'controller' => 'sui_applications',
				'action' => 'planilha_gd',
				$headerData['SuiSubscription']['id']
			)
		));
		echo $this->Bl->anchor(
			array('id' => 'link_planilha'),
			array('url' => ''), 
			__d('sui','Baixar planilha das inscrições', true)
		);
		echo $this->Html->scriptBlock("$('link_planilha').on('click', function(ev){ev.stop(); showPopup('planilha_gd')});");
		
		echo "&ensp; "; */

		
		echo $this->Bl->anchor(
			array(),
			array('url' => array(
				'plugin' => 'grandedesafio',
				'edicao' => 6,
				'controller' => 'equipes',
				'action' => 'planilha_gd_videos'
			)), 
			__d('sui','Baixar planilha de votação nos vídeos', true)
		);

	echo $this->Bl->ediv();
}
