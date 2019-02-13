<?php
/*!
  \class   CookieOperator CookieOperator.php
  \ingroup eZTemplateOperators
  \brief   Wrapper to handle setting and getting cookies from a template
  \version 2007.08.30
  \date    2007.08.30
  \author  syorex (alex@soyrex.comrerer4)
  \licence GPL version 2.

*/

class BollettinoOperator
{
    private $Operators = array();

    function __construct()
    {
        $this->Operators = array(
            'monospace',
            'transliteration',
            'bollettinopostale_numero_conto_corrente',
            'bollettinopostale_intestatario_conto_corrente',
        );
    }

    function operatorList()
    {
        return $this->Operators;
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array(
            'monospace' => array(
                'input' => array(
                    'type' => 'string',
                    'required' => true,
                    'default' => '0')),

            'transliteration' => array(
                'input' => array(
                    'type' => 'string',
                    'required' => true,
                    'default' => '0')),

            'bollettinopostale_numero_conto_corrente' => array(
                'order' => array(
                    'type' => 'object',
                    'required' => true)),

            'bollettinopostale_intestatario_conto_corrente' => array(
                'order' => array(
                    'type' => 'object',
                    'required' => true)),
        );
    }

    function modify(&$tpl, &$operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters)
    {
        switch ($operatorName) {
            case 'monospace':
                {
                    $input = $namedParameters['input'];
                    $operatorValue = $this->FormattaMonospace($input);
                }
                break;

            case 'transliteration':
                {
                    $input = $namedParameters['input'];
                    $operatorValue = $this->Translittera($input);
                }
                break;

            case 'bollettinopostale_numero_conto_corrente':
            case 'bollettinopostale_intestatario_conto_corrente':
                {
                    $parameterKey = $operatorName == 'bollettinopostale_numero_conto_corrente' ? 'NumeroContoCorrente' : 'IntestatarioContoCorrente';
                    $operatorValue = eZINI::instance('bollettinopostale.ini')->variable('Settings', $parameterKey);
                    if (class_exists('OCPaymentRecipient') && in_array('ocpaymentrecipient', eZExtension::activeExtensions())){
                        /** @var eZOrder $order */
                        $order = $namedParameters['order'];
                        foreach ($order->productItems() as $product){
                            /** @var eZContentObject $productObject */
                            $productObject = $product['item_object']->attribute('contentobject');
                            $productPaymentRecipient = eZPaymentRecipientType::getPaymentRecipientFromContentObject($productObject);
                            if ($productPaymentRecipient instanceof OCPaymentRecipient){
                                if ($productPaymentRecipient->hasParameter($parameterKey)){
                                    $operatorValue = $productPaymentRecipient->getParameter($parameterKey);
                                    break;
                                }
                            }
                        }
                    }
                }
                break;
        }
    }

    function FormattaMonospace($cifra = false)
    {
        if (!$cifra) return false;
        $cifra = explode('.', $cifra);
        $numero = $cifra[0];
        $decimale = isset($cifra[1]) ? $cifra[1] : false;
        $decimale = $this->ControllaDecimale($decimale);
        $cifra = $numero . $decimale;

        $stringa = str_pad($cifra, 11, "-", STR_PAD_LEFT);

        $risultato = "";
        for ($i = 0; $i <= 11; $i++) {
            if (isset($stringa[$i])) {
                if ($stringa[$i] == "-") $risultato .= '<span class="zero">0</span>';
                else if ($stringa[$i] == ",") $risultato .= '<span class="space">,</span>';
                else $risultato .= '<span>' . $stringa[$i] . '</span>';
            }
        }
        return $risultato;
    }

    function ControllaDecimale($cifra = false, $StampaVirgola = true)
    {
        if ($cifra) {
            $cifra = str_pad($cifra, 2, "0");

            $cifra = substr($cifra, 0, 2);

            if ($StampaVirgola) {
                $cifra = ',' . $cifra;
            }

            return $cifra;
        }

        if ($StampaVirgola) {
            return ',00';
        }

        return '00';
    }

    function Translittera($cifra = false)
    {
        if (!$cifra) return false;
        $unita = array("", "uno", "due", "tre", "quattro", "cinque", "sei", "sette", "otto", "nove");
        $decina1 = array("dieci", "undici", "dodici", "tredici", "quattordici", "quindici", "sedici", "diciassette", "diciotto", "diciannove");
        $decine = array("", "dieci", "venti", "trenta", "quaranta", "cinquanta", "sessanta", "settanta", "ottanta", "novanta");
        $decineTroncate = array("", "", "vent", "trent", "quarant", "cinquant", "sessant", "settant", "ottant", "novant");
        $centinaia = array("", "cento", "duecento", "trecento", "quattrocento", "cinquecento", "seicento", "settecento", "ottocento", "novecento");

        $risultato = "";

        $cifra = explode('.', $cifra);
        $numero = $cifra[0];
        $decimale = isset($cifra[1]) ? $cifra[1] : false;

        $stringa = str_pad($numero, 9, "0", STR_PAD_LEFT);

        for ($i = 0; $i < 9; $i = $i + 3) {
            $tmp = "";
            $tmp .= $centinaia[$stringa[$i]];

            if ($stringa[$i + 1] != "1") {
                if ($stringa[$i + 2] == "1" || $stringa[$i + 2] == "8")
                    $tmp = $tmp . $decineTroncate[$stringa[$i + 1]];
                else
                    $tmp = $tmp . $decine[$stringa[$i + 1]];

                $tmp = $tmp . $unita[$stringa[$i + 2]];
            } else {
                $tmp .= $decina1[$stringa[$i + 2]];
            }

            if ($tmp != "" && $i == 0)
                $tmp .= "milioni";

            if ($tmp != "" && $i == 3)
                $tmp .= "mila";

            $risultato .= $tmp;

            if ($i == 0 && $stringa[$i] == "0" && $stringa[$i + 1] == "0")
                $risultato = str_replace("unomilioni", "unmilione", $risultato);

            if ($i == 3 && $stringa[$i] == "0" && $stringa[$i + 1] == "0")
                $risultato = str_replace("unomila", "mille", $risultato);
        }

        if ($risultato == "")
            return "zero" . $this->ControllaDecimale($decimale);
        else
            return $risultato . $this->ControllaDecimale($decimale);
    }


}
