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
?>

<br />
<input type="button" value="Imprimir" style="margin: 30px; width: 150px;" onClick="window.print()" />
			<br />
			<br />
		<table cellspacing="0" cellpadding="0" width="665" border="0">
        	<tr>
				<td class="border4" rowspan="2" width="150">
				<img height="30" border="0" width="144" src="/sui/img/logosantander.jpg" />
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td class="border3" valign="bottom"  width="58">
					<p class="fonte4"><?php echo $calculated['codigo_banco_com_dv']; ?></p> 
				</td>
				<td class="border3" width="452" valign="bottom" align="right">
					<p class="fonte3"><?php 
						$calculated['linha_digitavel'] = SuiBoleto::linhaDigitavel($payment['SuiPayment']['barcode']);
						echo $calculated['linha_digitavel'];
					?></p> 
				</td>
        	</tr>
        </table>
        <table id="corpo" cellspacing="0" cellpadding="0" width="665" border="0">
        	<tr>
			<td class="border1" width="305">
				<p class="fonte1">Cedente</p>
				<p class="fonte2"><?php echo $config['cedente']; ?></p> 
			</td>
			<td class="border1" width="133">
				<p class="fonte1">Agência/Código do Cedente</p>
				<p class="fonte2"><?php echo $calculated['agencia_conta']; ?></p> 
			</td>
			<td class="border1" width="46">
				<p class="fonte1">Espécie</p>
				<p class="fonte2"><?php echo $config['especie']; ?></p> 
			</td>
			<td class="border1" width="59">
				<p class="fonte1">Quantidade</p>
				<p class="fonte2"><?php echo $config['quantidade']; ?></p> 
			</td>
			<td class="border1" width="127"  align="right">
				<p class="fonte1">Nosso Número</p>
				<p class="fonte2"><?php echo $calculated['nosso_numero']; ?></p> 
			</td>
        	</tr>
        </table>
        <table id="corpo" cellspacing="0" cellpadding="0" width="665" border="0">
          	<tr>
				<td class="border1" width="199">
					<p class="fonte1">Número do Documento</p>
					<p class="fonte2"><?php echo $calculated['numero_documento']; ?></p> 
				</td>
				<td class="border1" width="139">
					<p class="fonte1">CPF/CNPJ</p>

					<p class="fonte2"><?php echo $config['cpf_cnpj']; ?></p> 
				</td>
				<td class="border1" width="140">
					<p class="fonte1">Vencimento</p>
					<p class="fonte2"><?php echo date('d/m/Y', strtotime($payment['SuiPayment']['due_date'])); ?></p>
				</td>
				<td class="border1" width="187" align="right">
					<p class="fonte1">Valor do Documento</p>
					<p class="fonte2"><?php echo str_replace('.', ',', sprintf('%.2f', $payment['SuiPayment']['total_price'])); ?></p> 
				</td>
          	</tr>
        </table>
        <table id="corpo" cellspacing="0" cellpadding="0" width="665" border="0">
        	<tr>
				<td class="border1" width="120">
					<p class="fonte1">(-) Descontos/Abatimentos</p>
					<p class="fonte2">&nbsp;</p> 
				</td>
				<td class="border1" width="119">
					<p class="fonte1">Outras Deduções</p>
					<p class="fonte2">&nbsp;</p> 
				</td>
				<td class="border1" width="120">
					<p class="fonte1">(+) Mora/Multa</p>
					<p class="fonte2">&nbsp;</p> 
				</td>
				<td class="border1" width="118">
					<p class="fonte1">(+) Outros Acréscimos</p>
					<p class="fonte2">&nbsp;</p> 
				</td>
				<td class="border1" width="187">
					<p class="fonte1">(=) Valor Cobrado</p>
					<p class="fonte2">&nbsp;</p> 
				</td>
			</tr>
        </table>
		<table id="corpo" cellspacing="0" cellpadding="0" width="665" border="0">
		    <tr>
				<td class="border1" width="665">
					<p class="fonte1">Sacado</p>
					<p class="fonte2"><?php echo $payment['SuiPayment']['responsible_name']; ?></p> 
				</td>
			</tr>
		</table>
		<table id="corpo" cellspacing="0" cellpadding="0" width="665" border="0">
	        <tr>
			<td width="535">
				<p class="fonte1">Demonstrativo</p>
			</td>
			<td width="123">
				<p class="fonte1">Autenticação Mecânica</p>
			</td>
	   	</tr>
		<tr>
			<td>
				 <p class="fonte2"><?php 
				 	
				 	echo 'Pagamento das seguntes inscrições para ',$subscription['SuiSubscription']['title'],':'; 
				 	echo '<br>';
				 	foreach ($payment['SuiApplication'] as $application)
				 		echo String::insert('Equipe ":team_name" (:code)', $application), '; &ensp;';
				 ?>
				 </p>
			</td>
		</tr>
		<tr>
			<td class="border2" width="535">&nbsp;
			</td>
			<td class="border2" width="123">
				<p class="fonte1">Corte na Linha Pontilhada</p>
			</td>
		</tr>
		</table>
		<br />
		<br />

		<table cellspacing="0" cellpadding="0" width="665" border="0">
        	<tr>
			<td class="border4" rowspan="2" width="58">
				<img alt="" height="30" width="150" src="/sui/img/logosantander.jpg" />
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="border3" valign="bottom"  width="58">
				<p class="fonte4"><?php echo $calculated['codigo_banco_com_dv']; ?></p> 
			</td>
			<td class="border3" width="452" valign="bottom" align="right">
				<p class="fonte3"><?php echo $calculated['linha_digitavel']; ?></p>
			</td>
        	</tr>
        </table>
		<table cellspacing="0" cellpadding="0" width="665" border="0">
		    <tr>
				<td class="border1" width="479">
					<p class="fonte1">Local de Pagamento</p>
					<p class="fonte2">Pagável em qualquer Banco ou Casa Lotérica até o vencimento</p> 
				</td>
				<td class="border1" width="187" align="right">
					<p class="fonte1">Vencimento</p>
					<p class="fonte2"><?php echo date('d/m/Y', strtotime($payment['SuiPayment']['due_date'])); ?></p> 
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" width="665" border="0">
		    <tr>
				<td class="border1" width="479">
					<p class="fonte1">Cedente</p>
					<p class="fonte2"><?php echo $config['cedente']; ?></p> 
				</td>
				<td class="border1" width="187" align="right">
					<p class="fonte1">Agência/Código Cedente</p>
					<p class="fonte2"><?php echo $calculated['agencia_conta']; ?></p> 
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" width="665" border="0">
		    <tr>
				<td class="border1" width="121">
					<p class="fonte1">Data do Documento</p>
					<p class="fonte2"><?php echo date('d/m/Y', strtotime($payment['SuiPayment']['generated'])); ?></p>
				</td>
				<td class="border1" width="154">
					<p class="fonte1">No do Documento</p>
					<p class="fonte2"><?php echo $calculated['numero_documento']; ?></p> 
				</td>
				<td class="border1" width="69">
					<p class="fonte1">Espécie Doc.</p>
					<p class="fonte2"><?php echo $config['especie_doc']; ?></p>
				</td>
				<td class="border1" width="41">
					<p class="fonte1">Aceite</p>
					<p class="fonte2"><?php echo $config['aceite']; ?></p> 
				</td>
				<td class="border1" width="89">
					<p class="fonte1">Data Processamento</p>
					<p class="fonte2"><?php echo date('d/m/Y', strtotime($payment['SuiPayment']['generated'])); ?></p>
				</td>
				<td class="border1" width="187" align="right">
					<p class="fonte1">Nosso Número</p>
					<p class="fonte2"><?php echo $calculated['nosso_numero']; ?></p> 
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" width="665" border="0">
		    <tr>
				<td class="border1" width="121">
					<p class="fonte1">Uso do Banco</p>
					<p class="fonte2">&nbsp;</p> 
				</td>
				<td class="border1" width="90">
					<p class="fonte1">Carteira</p>
					<p class="fonte2"><?php echo $config['carteira']; ?></p> 
				</td>
				<td class="border1" width="60">
					<p class="fonte1">Espécie</p>
					<p class="fonte2"><?php echo $config['especie']; ?></p> 
				</td>
				<td class="border1" width="122">
					<p class="fonte1">Quantidade</p>
					<p class="fonte2"><?php echo $config['quantidade']; ?></p> 
				</td>
				<td class="border1" width="82" align="right">
					<p class="fonte1">Valor do Documento</p>
					<p class="fonte2"><?php echo $config['valor_unitario']; ?></p> 
				</td>
				<td class="border1" width="187">
					<p class="fonte1">(=) Valor do Documento</p>
					<p class="fonte2"><?php echo str_replace('.', ',', sprintf('%.2f', $payment['SuiPayment']['total_price'])); ?></p> 
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" width="665" border="0">
		    <tr>
				<td class="border1" rowspan="5" width="479" valign="top">
					<p class="fonte1">Instruções</p>
					<p class="fonte2"><?php echo $config['instrucoes1']; ?></p>
				</td>
				<td class="border1" width="187">
					<p class="fonte1">(-) Desconto / Abatimentos</p>
					<p class="fonte2">&nbsp;</p> 
				</td>
			</tr>
			<tr>
				<td class="border1" width="187">
					<p class="fonte1">(-) Outras deduções</p>
					<p class="fonte2">&nbsp;</p>
				</td>
			</tr>
			<tr>
				<td class="border1" width="187">
					<p class="fonte1">(+) Mora / Multa</p>
					<p class="fonte2">&nbsp;</p> 
				</td>
			</tr>
			<tr>
				<td class="border1" width="187">
					<p class="fonte1">(+) Outros acréscimos</p>
					<p class="fonte2">&nbsp;</p> 
				</td>
			</tr>
			<tr>
				<td class="border1" width="187">
					<p class="fonte1">(=) Valor cobrado</p>
					<p class="fonte2">&nbsp;</p> 
				</td>
			</tr>
			</table>
		<table cellspacing="0" cellpadding="0" width="665" border="0">
		    <tr>
				<td class="border1" width="478" valign="top">
					<p class="fonte1">Sacado</p>
					<p class="fonte2"><?php echo $payment['SuiPayment']['responsible_name']; ?></p> 
				</td>
				<td class="border1" width="187" valign="bottom">
					<p class="fonte1">Cód. baixa</p>
				</td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" width="665" border="0">
		    <tr>
				<td  width="400" valign="top">
					<p class="fonte1">Sacador/Avalista</p>
				</td>
				<td  width="265" align="right">
				<p class="fonte1">Autenticação mecânica</p>
<p class="fonte2"> - Ficha de Compensação </p>
</td>
			</tr>
			<tr>
				<td height="50" align="left" valign="bottom">
				<?php
					echo SuiBoleto::renderBarcode($payment['SuiPayment']['barcode']);
				?>
				 </td>
			</tr>
			<tr>
				<td class="border2" width="571">
					&nbsp;
				</td>
				<td class="border2" width="95" align="right">
					<p class="fonte1">Corte na Linha Pontilhada</p>
				</td>
			</tr>
			
		</table>
