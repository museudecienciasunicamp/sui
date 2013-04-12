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

$config['Sui.Spreadsheets.gd'] = array(
		array(
			'titulo_secao' => 'Equipe',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Identificação',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Inscrição completa',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Nome da equipe',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Participantes',		'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Categoria',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',				'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo de escola',		'largura' => 10),
			)
		),
		array(
			'titulo_secao' => 'Dono da inscrição',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',					'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',				'largura' => 10),
			)
		),
		array(
			'titulo_secao' => 'Pagamento',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome no boleto',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone',				'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Código boleto',		'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Data de vencimento',	'largura' => 5),
				array('tipo' => 'number',	'titulo' => 'Valor',				'largura' => 5),
				array('tipo' => 'date',		'titulo' => 'Data de emissão',		'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Pagou',				'largura' => 5),
				array('tipo' => 'date',		'titulo' => 'Quando pagou',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Documento responsável','largura' => 10),
			)
		),
		array(
			'titulo_secao' => 'Inscrição',
			'colunas' => array(
				array('tipo' => 'date',		'titulo' => 'Se inscreveu em',		'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Período',				'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Completa?',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Houve alteração?',		'largura' => 5),
				array('tipo' => 'date',		'titulo' => 'Data última alteração','largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Capitão',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Nascimento',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Série',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Membro 1',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Nascimento',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Série',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Membro 2',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Nascimento',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Série',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Membro 3',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Nascimento',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Série',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Membro 4',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Nascimento',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Série',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Membro 5',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Nascimento',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Série',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Membro 6',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Nascimento',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Série',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Orientador',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		)
	);


$config['Sui.Spreadsheets.onhb'] = array(
		array(
			'titulo_secao' => 'Equipe',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Identificação',		'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Inscrição completa',	'largura' => 4),
				array('tipo' => 'string',	'titulo' => 'Nome da equipe',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Escola',				'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo de escola',		'largura' => 4),
				array('tipo' => 'string',	'titulo' => 'Cidade',				'largura' => 6),
				array('tipo' => 'string',	'titulo' => 'Estado',				'largura' => 3),
			)
		),
		array(
			'titulo_secao' => 'Dono da inscrição',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',					'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',				'largura' => 10),
			)
		),
		array(
			'titulo_secao' => 'Pagamento',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome no boleto',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone',				'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Código boleto',		'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Data de vencimento',	'largura' => 5),
				array('tipo' => 'number',	'titulo' => 'Valor',				'largura' => 5),
				array('tipo' => 'date',		'titulo' => 'Data de emissão',		'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Pagou',				'largura' => 5),
				array('tipo' => 'date',		'titulo' => 'Quando pagou',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Documento responsável','largura' => 10),
			)
		),
		array(
			'titulo_secao' => 'Inscrição',
			'colunas' => array(
				array('tipo' => 'date',		'titulo' => 'Se inscreveu em',		'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Completa?',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Houve alteração?',		'largura' => 5),
				array('tipo' => 'date',		'titulo' => 'Data última alteração','largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Membro 1',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Nascimento',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Série',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Membro 2',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Nascimento',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Série',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Membro 3',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'date',		'titulo' => 'Nascimento',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Escola',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Tipo',			'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Série',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		),
		array(
			'titulo_secao' => 'Orientador',
			'colunas' => array(
				array('tipo' => 'string',	'titulo' => 'Nome',			'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'E-mail',		'largura' => 10),
				array('tipo' => 'string',	'titulo' => 'Telefone 1',	'largura' => 5),
				array('tipo' => 'string',	'titulo' => 'Telefone 2',	'largura' => 5)
			)
		)
	);
	
	
$config['Sui.Spreadsheets.users'] = array(
		array(
			'titulo_secao' => 'Pessoa',
			'colunas' => array(
				array('tipo' => 'string', 'titulo' => 'Nome',				'largura' => 10),
				array('tipo' => 'string', 'titulo' => 'Status',				'largura' => 5),
				array('tipo' => 'string', 'titulo' => 'Tipo',				'largura' => 5),
				array('tipo' => 'string', 'titulo' => 'E-mail',				'largura' => 10),
				array('tipo' => 'string', 'titulo' => 'Data de nascimento',	'largura' => 5),
				array('tipo' => 'string', 'titulo' => 'CPF',				'largura' => 5),
				array('tipo' => 'string', 'titulo' => 'RG',					'largura' => 5),
				array('tipo' => 'string', 'titulo' => 'Telefone 1',			'largura' => 5),
				array('tipo' => 'string', 'titulo' => 'Telefone 2',			'largura' => 5),
				array('tipo' => 'string', 'titulo' => 'Endereço',			'largura' => 15),
				array('tipo' => 'string', 'titulo' => 'Complemento',		'largura' => 5),
				array('tipo' => 'string', 'titulo' => 'Número',				'largura' => 5),
				array('tipo' => 'string', 'titulo' => 'Cidade',				'largura' => 10),
				array('tipo' => 'string', 'titulo' => 'UF',					'largura' => 3),
				array('tipo' => 'string', 'titulo' => 'Instituições',		'largura' => 25),
			)
		),
	);
	

$config['Sui.Spreadsheets.templates'] = array(
		'basic' => array(
			'titulo_secao' => 'Inscrição',
			'colunas' => array(
				array('tipo' => 'string', 'titulo' => 'Identificação',      'largura' => 6, 'value' => '$application:/SuiApplication/code' ),
				array('tipo' => 'string', 'titulo' => 'Estado',		        'largura' => 4, 'value' => '$application:/SuiApplication/status', 'map' => array('in_proccess' => 'Parcial', 'expired' => 'Expirada', 'cancelled' => 'Cancelada', 'completed' => 'Completa', 'rejected' => 'Rejeitada')),
				array('tipo' => 'string', 'titulo' => 'Etapa',		        'largura' => 4, 'value' => '$application:/SuiApplication/current_step', 'map' => array('aprovacao' => 'Aprovação', 'confirmacao_dados' => 'Confirmação de dados', 'confirmacao_email' => 'Confirmação de membros', 'dados_especificos' => 'Formulário específico', 'instituicao' => 'Instituição', 'modalidade' => 'Modalidade', 'pagamento' => 'Pagamento', 'participantes' => 'Participantes', 'regulamento' => 'Regulamento', 'relatorio'	=> 'Relatório final')),
				array('tipo' => 'string', 'titulo' => 'Sub-etapa',	        'largura' => 5, 'value' => '$application:/SuiApplication/step_status', 'map' => array('started' => ' ', 'generated' => 'Pagamento gerado', 'waiting_approval' => 'Agd. aprovação', 'waiting_user_feedback' => 'Agd. resposta do usuário', 'approved' => 'Inscrição aprovada')),
				array('tipo' => 'money' , 'titulo' => 'Taxa',               'largura' => 3, 'value' => '$application:/SuiApplication/subscription_fee' ),
				array('tipo' => 'string', 'titulo' => 'Teste',              'largura' => 2, 'value' => '$application:/SuiApplication/is_test', 'map' => array(0 => '', 1 => 'Sim')),
				array('tipo' => 'date'  , 'titulo' => 'Iniciada em',        'largura' => 4, 'value' => '$application:/SuiApplication/started_at'),
				array('tipo' => 'date'  , 'titulo' => 'Pagto confirmado',   'largura' => 4, 'value' => '$application:/SuiApplication/payment_data_at'),
				array('tipo' => 'date'  , 'titulo' => 'Última modif.',		'largura' => 4, 'value' => '$application:/SuiApplication/modified'),
			)
		),
		'modalidade' => array(
			'titulo_secao' => '',
			'colunas' => array(
				array('tipo' => 'string', 'titulo' => 'Modalidade', 'largura' => 8, 'value' => '$application:/SuiApplication/modality', 'map' => '$modalidades')
			)
		),
		'instituicao' => array(
			'titulo_secao' => '',
			'colunas' => array(
				array('tipo' => 'number', 'titulo' => 'Id',           'largura' => 2, 'value' => '$application:/SuiInstitution/id'),
				array('tipo' => 'string', 'titulo' => 'Tipo',         'largura' => 4, 'value' => '$application:/SuiInstitution/sui_institution_type_id', 'map' => '$institutionTypes'),
				array('tipo' => 'string', 'titulo' => 'Nome',         'largura' => 8, 'value' => '$application:/SuiInstitution/name'),
				array('tipo' => 'string', 'titulo' => 'Unidade',      'largura' => 4, 'value' => '$application:/SuiInstitution/unit_name'),
				array('tipo' => 'string', 'titulo' => 'Públ/privada', 'largura' => 3, 'value' => '$application:/SuiInstitution/type', 'map' => array('public'=> 'Pública', 'private' => 'Privada')),
				array('tipo' => 'string', 'titulo' => 'est/mun/fed',  'largura' => 3, 'value' => '$application:/SuiInstitution/sub_type', 'map' => array('state' => 'Estadual', 'municipal' => 'Municipal', 'federal' => 'Federal')),
				array('tipo' => 'string', 'titulo' => 'Modalidades',  'largura' => 6, 'value' => '$application:/SuiInstitution/SuiInstitutionsMode/name'),
				array('tipo' => 'string', 'titulo' => 'UF',           'largura' => 2, 'value' => '$application:/SuiInstitution/state'),
				array('tipo' => 'string', 'titulo' => 'Cidade',       'largura' => 4, 'value' => '$application:/SuiInstitution/city'),
				array('tipo' => 'string', 'titulo' => 'CEP',          'largura' => 3, 'value' => '$application:/SuiInstitution/cep'),
			)
		),
		'responsavel' => array(
			'titulo_secao' => 'Responsável',
			'colunas' => array(
				array('tipo' => 'number', 'titulo' => 'Id',         'largura' => 2, 'value' => '$application:/SuiUser/id'),
				array('tipo' => 'string', 'titulo' => 'E-mail',		'largura' => 8, 'value' => '$application:/SuiUser/email'),
				array('tipo' => 'string', 'titulo' => 'Tipo',       'largura' => 3, 'value' => '$application:/SuiUser/sui_users_type_id', 'map' => '$usersTypes'),
				array('tipo' => 'string', 'titulo' => 'Nome',       'largura' => 8, 'value' => '$application:/SuiUser/full_name'),
				array('tipo' => 'date',	  'titulo' => 'Nascimento',	'largura' => 4, 'value' => '$application:/SuiUser/birthdate'),
				array('tipo' => 'string', 'titulo' => 'Série',		'largura' => 7, 'value' => '$application:/SuiUser/sui_grade_id', 'map' => '$grades'),
				array('tipo' => 'string', 'titulo' => 'Matérias',	'largura' => 7, 'value' => '$application:/SuiUser/SuiSubject/name'),
				array('tipo' => 'string', 'titulo' => 'CPF',		'largura' => 4, 'value' => '$application:/SuiUser/cpf'),
				array('tipo' => 'string', 'titulo' => 'RG',			'largura' => 4, 'value' => '$application:/SuiUser/rg'),
				array('tipo' => 'string', 'titulo' => 'Tel 1',		'largura' => 4, 'value' => '$application:/SuiUser/phone1'),
				array('tipo' => 'string', 'titulo' => 'Tel 2',		'largura' => 4, 'value' => '$application:/SuiUser/phone2'),
			
			)
		),
		'participante' => array(
			'titulo_secao' => '',
			'colunas' => array(
				array('tipo' => 'number', 'titulo' => 'Id',         'largura' => 2, 'value' => '$member:/SuiMember/id'),
				array('tipo' => 'string', 'titulo' => 'E-mail',		'largura' => 8, 'value' => '$member:/SuiMember/email'),
				array('tipo' => 'string', 'titulo' => 'Tipo',       'largura' => 3, 'value' => '$member:/SuiMember/sui_users_type_id', 'map' => '$usersTypes'),
				array('tipo' => 'string', 'titulo' => 'Nome',       'largura' => 8, 'value' => '$member:/SuiMember/full_name'),
				array('tipo' => 'date',	  'titulo' => 'Nascimento',	'largura' => 4, 'value' => '$member:/SuiMember/birthdate'),
				array('tipo' => 'string', 'titulo' => 'Série',		'largura' => 7, 'value' => '$member:/SuiMember/sui_grade_id', 'map' => '$grades'),
				array('tipo' => 'string', 'titulo' => 'CPF',		'largura' => 4, 'value' => '$member:/SuiMember/cpf'),
				array('tipo' => 'string', 'titulo' => 'RG',			'largura' => 4, 'value' => '$member:/SuiMember/rg'),
				array('tipo' => 'string', 'titulo' => 'Tel 1',		'largura' => 4, 'value' => '$member:/SuiMember/phone1'),
				array('tipo' => 'string', 'titulo' => 'Tel 2',		'largura' => 4, 'value' => '$member:/SuiMember/phone2'),
			)
		),
		'dados_especificos' => array(
			'titulo_secao' => '',
			'colunas' => array(
				'texto'            => array('tipo' => 'string', 'largura' => 8),
				'texto_grande'     => array('tipo' => 'string', 'largura' => 16),
				'opcoes'           => array('tipo' => 'string', 'largura' => 8),
				'multiplas_opcoes' => array('tipo' => 'string', 'largura' => 12)
			)
		),
		'pagamento' => array(
			'titulo_secao' => '',
			'colunas' => array(
				array('tipo' => 'number', 'titulo' => 'Id',              'largura' => 2, 'value' => '$application:/SuiPayment/id'),
				array('tipo' => 'string', 'titulo' => 'Estado',          'largura' => 4, 'value' => '$application:/SuiPayment/status', 'map' => array('waiting' => 'Agdo confirmação','expired' => 'Expirado', 'user_cancelled' => 'Cancelado','received' => 'Recebido', 'misreceived' => 'Recebido com erro')),
				array('tipo' => 'date'  , 'titulo' => 'Gerado em',       'largura' => 4, 'value' => '$application:/SuiPayment/generated'),
				array('tipo' => 'money' , 'titulo' => 'Preço',           'largura' => 3, 'value' => '$application:/SuiPayment/total_price'),
				array('tipo' => 'money' , 'titulo' => 'P. líq.',         'largura' => 3, 'value' => '$application:/SuiPayment/net_value'),
				array('tipo' => 'money' , 'titulo' => 'Recebido',	     'largura' => 3, 'value' => '$application:/SuiPayment/received_value'),
				array('tipo' => 'string', 'titulo' => 'Código de barra', 'largura' => 14,'value' => '$application:/SuiPayment/barcode'),
				array('tipo' => 'string', 'titulo' => 'Responsável',     'largura' => 8, 'value' => '$application:/SuiPayment/responsible_name'),
				array('tipo' => 'string', 'titulo' => 'Pessoa', 	     'largura' => 3, 'value' => '$application:/SuiPayment/responsible_type', 'map' => array('pf' => 'Física', 'pj' => 'Jurídica')),	
				array('tipo' => 'string', 'titulo' => 'Documento', 	     'largura' => 4, 'value' => '$application:/SuiPayment/responsible_document'),
				array('tipo' => 'string', 'titulo' => 'Tel', 	 	     'largura' => 4, 'value' => '$application:/SuiPayment/responsible_telephone'),
				array('tipo' => 'string', 'titulo' => 'E-mail',	 	     'largura' => 8, 'value' => '$application:/SuiPayment/responsible_email'),
			)
		),
		'aprovacao' => array(
			'titulo_secao' => 'Registro de conversas',
			'colunas' => array(
				array('tipo' => 'string', 'titulo' => 'Registro', 'largura' => 20, 'value' => '$application:/SuiFeedback/qaa')
			)
		)
	);
