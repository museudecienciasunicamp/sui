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

class SuiShell extends Shell
{
	public $tasks = array('CreateReport', 'CheckPayments');
	
	function startup()
	{
		// Intentionally left blank (avoid parent startup)
	}
	
	function main()
	{
		$this->help();
	}
	
	function help()
	{
		$this->out();
		$this->out('NAME');
		$this->out("\tSUI shell");
		
		$this->out();
		$this->out('SYNOPSIS');
		$this->out("\tcake sui <command> <args> <options>");


		$this->out();
		$this->out('COMMANDS');
		$this->out("\tcreate_report [-path full_path]");
		$this->out(wordwrap("\t\tFor creating a report of generated billets, use the `create_report` command. The report is compatible with Funcamp only.", 47, "\n\t\t"));
		$this->out();
		$this->out("\t\tThe default path is APP/tmp/");
		$this->out();

		$this->out("\tcheck_payments");
		$this->out(wordwrap("\t\tThe payment is automaticaly confirmed by an upload made by some Funcamp employee. Then, one must run the `check_payments` command so the software can match payments and applications. Those matches must be made regularly, since without them, one application do not receive its payment.", 47, "\n\t\t"));

		$this->out();
		$this->out('OPTIONS');
		$this->out("\t-quiet Supress any kind of output (error and standard)");
		$this->out();
	}
}
