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

echo $this->Html->doctype();
echo $this->Bl->shtml(array(
		'xmlns' => 'http://www.w3.org/1999/xhtml',
		'xml:lang' => 'pt-br',
		'lang' => 'pt-br'
	)
);

	echo $this->Bl->shead();
		echo $this->Html->charset();
		echo $this->Bl->title(null, null, $title_for_layout);
		echo $this->Bl->style(array('type' => 'text/css'), array(), "
			*{
				margin:0;
				padding:0;
				clear:both;
			}
			td{
				vertical-align:top;
			}
			table{
				margin-left:2px;
			}
			.fonte1{
				vertical-align:top;
				font-family: Arial, sans-serif;
				font-size:9px;
				margin-left:7px;
			}
			.fonte2{
				vertical-align:bottom;
				margin-left:7px;
				font-family:Arial;
				font-size:10px;
				font-weight:bold;
			}
			.fonte3{
				vertical-align:bottom;

				font-family:Arial;
				font-size:15px;
				font-weight:bold;
			}
			.fonte4{
				vertical-align:bottom;
				font-family:Arial;
				font-size:20px;
				font-weight:bold;
			}
			.border1{
				border-left-style:solid;
				border-left-width:1px;
				border-color:#000000;
				border-bottom-style:solid;
				border-bottom-width:1px;
			}
			.border2{
				border-color:#000000;
				border-bottom-style:dashed;
				border-bottom-width:2px;
			}
			.border3{	

				border-color:#000000;
				border-bottom-style:solid;
				border-bottom-width:2px;
				border-left-style:solid;
				border-left-width:2px;
			}
			.border4{	
				border-color:#000000;
				border-bottom-style:solid;
				border-bottom-width:2px;
			}
			.border5{	
				border-color:#000000;
				border-left-style:solid;
				border-left-width:1px;
			}
		");
		echo $scripts_for_layout;
	echo $this->Bl->ehead();
	echo $this->Bl->sbody();
		echo $content_for_layout;
	echo $this->Bl->ebody();
echo $this->Bl->ehtml();
