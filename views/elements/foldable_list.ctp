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

$base = uniqid('');

echo $this->Bl->sdiv(array('class' => 'sui_pendencias'));

	echo $this->Bl->span(array('class' => 'cont'), array(), count($data));
	echo $this->Bl->anchor(array('id' => "link$base"), array('url' => ''), $title);
	echo $this->Bl->div(array('id' => 'plus'.$base, 'class' => 'plus_sign'), array(), '&nbsp;');
	
	echo $this->Bl->sdiv(array('id' => 'sui_pendencias_'.$base, 'class' => 'foldable_container'));
		echo $this->Form->create('SuiApplication', array('id' => uniqid('frm'), 'url' => array('plugin' => 'sui', 'controller' => 'sui_subscriptions', 'action' => 'post_router')));
			echo $this->Jodel->insertModule($module, $type, $data);
		echo $this->Form->end();
	echo $this->Bl->ediv();
	
echo $this->Bl->ediv();

echo $this->Html->script('/sui/js/sui', array('inline' => false));
echo $this->BuroOfficeBoy->addHtmlEmbScript("new Sui.FoldableList('$base');");

