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

	$iniciar = Router::url("/olimpiada_quatro/img/iniciar.png");
	$iniciada = Router::url("/olimpiada_quatro/img/iniciada.png");
?>

	<div id="iniciada" style="margin:auto; width:1280px; position:absolute; top:-2000px; height:1024px; background: url('<?php echo $iniciada; ?>') no-repeat;">
	
	</div>
	
	<div id="iniciar" style="margin:auto; width:1280px;  position:relative; height:1024px; background: url('<?php echo $iniciar; ?>') no-repeat;">
		<a href="#" style="height:124px; width:300px; position:absolute; top:345px; left:490px;" onclick="javascript:iniciar();">&nbsp;</a>
	</div>

	
<?php

	$url = Router::url(array('plugin' => 'sui', 'controller' => 'sui_applications', 'action' => 'avanca_proxima_fase'));
	echo $javascript->codeBlock("
		
		function iniciar()
		{
			$('iniciar').setOpacity(0.5);
			window.setTimeout(function() { abrir(); }, 8000);
			new Ajax.Request('".$url."', {
				method: 'get',
			});
		}
		
		function abrir()
		{
			$('iniciar').hide();
			document.body.setStyle({backgroundColor: '#C42079'});
			$('iniciada').setStyle({position: 'relative', top: 0});
			
			
		}
	");
?>
