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

class SuiAppController extends MexcAppController
{
/**
 * List of components
 * 
 * @var array
 * @access public
 */
	var $components = array(
		'RequestHandler',
		'Tradutore.TradLanguageSelector',
		'PageSections.SectSectionHandler',
		'Typographer.TypeLayoutSchemePicker',
		'JjUsers.JjAuth' => array(
			'userModel' => 'JjUsers.UserUser',
			'sessionKey' => 'JjAuth.UserUser',
			'authorize' => 'controller',
			'loginRedirect' => array(
				'plugin' => 'dashboard',
				'controller' => 'dash_dashboard',
				'action' => 'index',
			)
		),
		'Sui.SuiAuth'
	);

	var $helpers = array(
		'Typographer.TypeDecorator' => array(
			'name' => 'Decorator',
			'compact' => false,
			'receive_tools' => true
		),
		'Typographer.*TypeStyleFactory' => array(
			'name' => 'TypeStyleFactory',
			'receive_automatic_classes' => true, 
			'receive_tools' => true,
			'generate_automatic_classes' => true
		),
		'Typographer.*TypeBricklayer' => array(
			'name' => 'Bl',
			'receive_tools' => true,
		),
		'Corktile.Cork', 'JjUtils.Jodel', 'Text', 

		// Obligatory order: Officeboy and than Burocrata
		'Burocrata.BuroOfficeBoy',
		'Burocrata.*BuroBurocrata' => array(
			'name' => 'Buro'
		)
	);

	public function beforeFilter()
	{
		setlocale(LC_MONETARY, 'pt_BR', 'pt_BR.UTF-8', 'pt-br');
		
		$this->SuiAuth->loginError = __d('sui', 'Login failed. Invalid username or password.', true);
		$this->SuiAuth->authError = __d('sui', 'You are not authorized to access that location.', true);
		
		parent::beforeFilter();
		
		if ($this->museuUserLogged)
		{
			$humanName = sprintf(__d('sui', 'Área de %s', true), h($this->museuUserData['SuiUser']['name']));
			Configure::write('PageSections.sections.public_page.subSections.user_area.humanName', $humanName);
			$this->SectSectionHandler->sections['public_page']['subSections']['user_area']['humanName'] = $humanName;
		}
		
	}
}
