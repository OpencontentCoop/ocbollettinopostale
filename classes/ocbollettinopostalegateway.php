<?php

class OCBollettinoPostaleGateway extends eZPaymentGateway
{
    const WORKFLOW_TYPE_STRING = 'ocbollettinopostale';
    
    function execute( $process, $event )
    {
        $processParameters = $process->attribute( 'parameter_list' );
        $order = eZOrder::fetch( $processParameters['order_id'] );
        $order->setStatus( 1000 );
        $order->store();
        return eZWorkflowType::STATUS_ACCEPTED;
    }
    
}

eZPaymentGatewayType::registerGateway( OCBollettinoPostaleGateway::WORKFLOW_TYPE_STRING,
                                       "OCBollettinoPostaleGateway",
                                       "Bollettino postale" );

?>