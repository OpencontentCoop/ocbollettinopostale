{*?template charset=utf8?*}
{set-block variable=$xhtml}
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href={'stylesheets/bollettino.css'|ezdesign()} media="print,screen"></link>
<title></title>
</head>

<body>
<!-- bollettino -->

{def $nome = concat( $order.account_information.last_name, ' ', $order.account_information.first_name )
	 $indirizzo = $order.account_information.street2
	 $cap = $order.account_information.zip
	 $provincia = $order.account_information.place
	 $prov = ''
	 $stato = $order.account_information.country
	 $ordine_euro = monospace($order.total_inc_vat)
	 $ordine_lettere_euro = transliteration($order.total_inc_vat)
	 $ordine_causale = concat("numero ordine: ", $order.order_nr)
     $numero_conto_corrente = ezini( 'Settings', 'NumeroContoCorrente', 'bollettinopostale.ini' )
     $intestatario_conto_corrente = ezini( 'Settings', 'IntestatarioContoCorrente', 'bollettinopostale.ini' )
}


<div id="background"><img src={'bollettinotd451.png'|ezimage()} /></div>

<!-- parte sinistra del bollettino -->

<div class="text-3 upper" id="cc-1">{$numero_conto_corrente}</div>

<div class="text-3 upper" id="intestato-1">{$intestatario_conto_corrente}</div>

<div class="text-1" id="euro-1">{$ordine_euro}</div>

<div class="text-2" id="importo-1">{$ordine_lettere_euro}</div>

<div class="text-3" id="causale-1">{$ordine_causale}</div>

<div class="text-5" id="eseguito-1-nome">
	{$nome}<br />
	{$indirizzo}<br />
	{$cap} {$provincia} {$prov} {$stato}
</div>


<!-- parte destra del bollettino -->

<div class="text-3 upper" id="cc-2">{$numero_conto_corrente}</div>

<div class="text-3 upper" id="intestato-2">{$intestatario_conto_corrente}</div>

<div class="text-1" id="euro-2">{$ordine_euro}</div>

<div class="text-2" id="importo-2">{$ordine_lettere_euro}</div>

<div class="text-3 upper" id="causale-2">{$ordine_causale} - {$nome}</div>

<div class="text-3 upper" id="eseguito-2">
	{$nome}<br />
    {$indirizzo}<br />
    {$cap} {$provincia} {$prov}<br />
    {$stato}
</div>

<div class="text-3 upper" id="cc-3">{$numero_conto_corrente}</div>

</body>
</html>
{/set-block}

{if $debug}
    {$xhtml}
{else}
    {def $paradoxpdf_params = hash('xhtml', $xhtml,
                                   'pdf_file_name', concat( 'bollettino_postale_', $order.id ) )}
    {paradoxpdf($paradoxpdf_params)}
{/if}