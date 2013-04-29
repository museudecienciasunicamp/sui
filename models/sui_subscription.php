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

if (!function_exists('yaml_parse'))
{
	App::import('Vendor', 'spyc'.DS.'spyc');
	
	function yaml_parse($yaml)
	{
		return Spyc::YAMLLoad($yaml);
	}
};

class SuiSubscription extends SuiAppModel
{
	var $name = 'SuiSubscription';

	var $displayField = 'title';
	
	var $belongsTo = array(
		'SuiCurrentApplicationPeriod' => array(
			'className' => 'Sui.SuiApplicationPeriod',
			'foreignKey' => 'sui_application_period_id'
		),
		'MexcSpace.MexcSpace',
	);
	
	var $hasMany = array(
		'Sui.SuiApplication',
		'Sui.SuiText',
		'SuiApplicationPeriod' => array(
			'className' => 'Sui.SuiApplicationPeriod',
			'order' => array('SuiApplicationPeriod.start' => 'asc')
		)
	);

	var $hasAndBelongsToMany = array(
		'Sui.SuiPaymentInterval'
	);

	protected $YAMLCache = array();

/**
 * Overwriting the default __contruct method so we can use __d() funcion on validation messages
 * 
 * @access public
 */
	function __construct($id = false, $table = null, $ds = null)
	{
		$this->validate = array(
			'subscription_model' => array(
				'rule' => 'notEmpty',
				'message' => __d('sui', 'O modelo de inscrições é obrigatório.', true)
			),
			'title' => array(
				'rule' => array('between', 4, 255),
				'message' => __d('sui', 'O título do processo de inscrição é obrigatório e não deve passar dos 255 caracteres', true),
			),
			'slug' => array(
				'uniq' => array(
					'rule' => 'isUnique',
					'message' => __d('sui', 'O código deve ser único. O código fornecido já está em uso.', true)
				),
				'valid' => array(
					'rule' => '/^[a-z0-9_]*$/',
					'message' => __d('sui', 'Use somente letras minúsculas, números e sub-traço (_).', true)
				),
				'empty' => array(
					'rule' => 'notEmpty',
					'message' => __d('sui', 'O código é obrigatório', true)
				)
			)
		);
		return parent::__construct($id, $table, $ds);
	}

/**
 * createEmpty method: part of Backstage contract
 *
 * This method also starts the sequence steps of a subscription lifespan:
 *  - before_model: it exists, but there is no model of subscription configured
 *  - editing_config: when the administrator is allowed, will be a step where he can edit the YAML before continue
 *  - editing: when the subscription is being edited by the administrator
 *  - ready: when the editing is not allowed anymore, but it is not available to application
 *  - in_proccess: when the subscription is open
 *  - closed: the subscription proccess is closed
 *  - aborted: the crafting process was aborted by the user
 * 
 * @access public
 */
	function createEmpty()
	{
		$data = array(
			$this->alias => array(
				'subscription_status' => 'before_model'
			)
		);
		return $this->save($data, false);
	}

/**
 * Find for subscription to feed the burocrata form.
 * 
 * @access public
 */
	function findBurocrata($sui_subscription_id)
	{
		return $this->find('first', array(
			'conditions' => array('SuiSubscription.id' => $sui_subscription_id),
			'contain' => array(
				'SuiApplicationPeriod' => 'SuiPeriodCost',
				'SuiText',
				'SuiPaymentInterval'
			)
		));
	}

/**
 * Kludge to inject a conditions on backstage filter
 * 
 * @access public
 */
	function getBackstageListData()
	{
		return array(
			'conditions' => array(
				'not' => array('SuiSubscription.subscription_status' => 'aborted')
			)
		);
	}

/**
 * This method is used on SuiMainController::index() to retrieve
 * the list of subscriptions that are accepting applications
 * 
 * @access public
 * @return array The list on find('all') format
 */
	function getActiveSubscriptions($limit = 10)
	{
		return $this->find('all', array(
			'contain' => 'SuiCurrentApplicationPeriod',
			'conditions' => array(
				'SuiCurrentApplicationPeriod.start < NOW()',
				'SuiCurrentApplicationPeriod.end > NOW()',
				'SuiSubscription.subscription_status' => 'in_proccess'
			),
			'limit' => $limit,
			'order' => array('SuiSubscription.id' => 'desc')
		));
	}

/**
 * This method is used to retrieve data for a step of subscription,
 * given the subscription slug and the step code.
 * 
 * @access public
 * @return array The array of data
 */
	function getStep($slug, $step)
	{
		$this->contain(array('SuiText', 'SuiCurrentApplicationPeriod' => 'SuiPeriodCost'));
		$data = $this->findBySlug($slug);

		if (!empty($data[$this->alias]['configuration']))
		{
			$data['SuiStep'] = array();
			if (isset($data[$this->alias]['configuration']['subscription_steps'][$step]))
				$data['SuiStep'] =& $data[$this->alias]['configuration']['subscription_steps'][$step];
		
			$data['SuiStepNumber'] = array_search($step, array_keys($data[$this->alias]['configuration']['subscription_steps']));
			if ($data['SuiStepNumber'] !== false)
				$data['SuiStepNumber']++;
		}
		
		return $data;
	}

/**
 * After find callback
 * 
 * Parses the `configuration` parameter, converting YAML to PHP array.
 * 
 * @access public
 * @param array $results
 * @param boolean $primary
 * @return void
 */
	public function afterFind($results, $primary)
	{
		if (isset($results[0]))
		{
			foreach($results as &$result)
			{
				if (!empty($result[$this->alias]['configuration']))
				{
					$result[$this->alias]['config_pure_yaml'] = $result[$this->alias]['configuration'];
					$result[$this->alias]['configuration'] = $this->parse($result[$this->alias]['configuration']);
				}
			}
		}
		else
		{
			if (!empty($results['configuration']))
			{
				$results['config_pure_yaml'] = $results['configuration'];
				$results['configuration'] = $this->parse($results['configuration']);
			}
		}
			
		return $results;
	}

/**
 * Callback beforeSave
 *
 * This callback is used for updating the sui_subscriptions.configuration column when the form sends it
 * 
 * @access public
 */
	function beforeSave($options)
	{
		if (!empty($this->data[$this->alias]['config_pure_yaml']) && !empty($this->allowUpdateYAML))
		{
			$this->data[$this->alias]['configuration'] = $this->data[$this->alias]['config_pure_yaml'];
		}
		return true;
	}

/**
 * Method used to parse and cache the YAML
 * 
 * @access protected
 * @param string $config The YAML configuration string
 * @return array The parsed YAML
 */
	protected function parse($config)
	{
		$hash = sha1($config);
		if (isset($this->YAMLCache[$hash]))
			return $this->YAMLCache[$hash];

		if (function_exists('yaml_parse'))
			return $this->YAMLCache[$hash] = yaml_parse($config);

		return $this->YAMLCache[$hash] = Spyc::YAMLLoad($config);
	}

/**
 * SiteFactory "API"
 *
 * This method is called by SiteFactory plugin for each action that the model has to perform.
 * 
 * @access public
 */
	function factoryActions($action, $data)
	{
		if ($action == 'save')
		{
			$FactSite = ClassRegistry::init('SiteFactory.FactSite');
			$FactSection = ClassRegistry::init('SiteFactory.FactSection');
			$site = $FactSite->findById($data['FactSection']['fact_site_id']);
			
			$data = $FactSection->save($data);
			
			if (!empty($site['FactSite']['mexc_space_id']))
			{
				$CorkCorktile = & ClassRegistry::init('Corktile.CorkCorktile');
				$slug_section = Inflector::slug($data['FactSection']['name']);
				if (isset($data['FactSection']['metadata']['subscription_help_text']))
				{
					$options = array(
						'key' => "secao_{$slug_section}_{$data['FactSection']['id']}_texto_de_ajuda",
						'type' => 'cs_cork',
						'title' => 'Seção '.$data['FactSection']['name'] . ' - texto de ajuda',
						'editorsRecommendations' => 'Texto de ajuda opcional para aparecer na seção.',
						'options' => array(
							'cs_type' => 'text_and_title',
						),
						'location' => array('public_page', 'fact_sites', $site['FactSite']['mexc_space_id'], 'sui_subscription'.$data['FactSection']['id'])
					);
					$corkData = $CorkCorktile->getData($options);
				}
				$options = array(
					'key' => "secao_{$slug_section}_{$data['FactSection']['id']}_introducao",
					'type' => 'text_cork',
					'title' => 'Seção '.$data['FactSection']['name'] . ' - introdução',
					'editorsRecommendations' => 'Um pequeno texto para ser mostrado no início do site.',
					'options' => array(
						'textile' => true,
						'enabled_buttons' => array('bold', 'italic', 'link')
					),
					'location' => array('public_page', 'fact_sites', $site['FactSite']['mexc_space_id'], 'sui_subscription'.$data['FactSection']['id'])
				);
				$corkData = $CorkCorktile->getData($options);
			}
		}
		
		return $data;
	}
}
