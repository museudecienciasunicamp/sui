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

	switch ($type[0])
	{
		case 'buro':
			switch ($type[1])
			{
				case 'view':
					switch ($type[2])
					{
						case 'belongsto':
							if (isset($data['SuiUsersType']))
								echo $this->Bl->sinput(array('disabled' => 'disabled', 'type' => 'text', 'value' => $data['SuiUsersType']['name']), array());
						break;
					}
				break;
			}
		break;
	}
