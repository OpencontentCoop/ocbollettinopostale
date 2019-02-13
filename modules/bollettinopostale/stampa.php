<?php

$tpl = eZTemplate::factory();
$ini = eZINI::instance();
$http = eZHTTPTool::instance();
$module = $Params['Module'];
$user = eZUser::currentUser();

$UserObject = eZContentObject::fetch( eZUser::currentUserID() );
$tpl->setVariable( "userobject", $UserObject );

$OrderID = isset($Params['orderid']) ? $Params['orderid'] : 0;
$Debug = isset($Params['debug']) ? true: false;
$tpl->setVariable( "debug", $Debug );

if ( $OrderID == 0 ) 
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$access = false;
$order = eZOrder::fetch( $OrderID );
if ( !$order )
{
    return $module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$accessToAdministrate = $user->hasAccessTo( 'shop', 'administrate' );
$accessToAdministrateWord = $accessToAdministrate['accessWord'];

$accessToBuy = $user->hasAccessTo( 'shop', 'buy' );
$accessToBuyWord = $accessToBuy['accessWord'];

if ( $accessToAdministrateWord != 'no' )
{
    $access = true;
}
elseif ( $accessToBuyWord != 'no' )
{
    if ( $user->isAnonymous() )
    {
        if( $OrderID != $http->sessionVariable( 'UserOrderID' ) )
        {
            $access = false;
        }
        else
        {
            $access = true;
        }
    }
    else
    {
        if ( $order->attribute( 'user_id' ) == $user->id() )
        {
            $access = true;
        }
        else
        {
            $access = false;
        }
    }
}
if ( !$access )
{
    return $module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$tpl->setVariable( "order", $order );

if ( $Debug )
{
    echo $tpl->fetch( 'design:bollettinopostale/stampa.tpl' );
}
else
{
    return $tpl->fetch( 'design:bollettinopostale/stampa.tpl' );
}
eZExecution::cleanExit();

?>