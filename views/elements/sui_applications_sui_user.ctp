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

	if(!isset($type)) $type = array(null);
	if(!is_array($type)) $type = array($type);
	switch($type[0])
	{
		
		case 'buro':
			switch($type[1])
			{
				case 'view':
					switch($type[2])
					{
						case 'many_children':
							if (isset($data['SuiApplicationsSuiUser']['SuiUser']))
							{
								echo $this->Bl->strongDry($data['SuiApplicationsSuiUser']['SuiUser']['full_name']);
								echo $this->Bl->br();
								echo $this->Bl->spanDry($data['SuiApplicationsSuiUser']['SuiUser']['email']);
							}
							elseif (isset($data['SuiUser']))
							{
								echo $this->Bl->strongDry($data['SuiUser']['full_name']);
								echo $this->Bl->br();
								echo $this->Bl->spanDry($data['SuiUser']['email']);
							}
							echo $this->Bl->br();
							echo $this->Bl->spanDry($data['SuiApplicationsSuiUser']['role_code']);
						break;
					}
				break;
				
				case 'form':
					echo $this->Jodel->insertModule('Sui.SuiApplicationsSuiUser', array('form'), $data);
				break;
				
				
			}
		break;
		
		case 'form':
			echo $this->element('sui_applications_sui_user_form', array('plugin' => 'sui'));
		break;
	}
