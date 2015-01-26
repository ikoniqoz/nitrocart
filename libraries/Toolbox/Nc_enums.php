<?php defined('BASEPATH') OR exit('No direct script access allowed');
/***{COMMON_CLASS_HEADER}***/
final class Nc_enums
{
	const Yes  	= 1;
	const No  	= 0;
}
final class NCParamType
{
	const String  	= 'string';
	const Integer  	= 'integer';
	const Float 	= 'float';
}
final class UserActivationMode
{
	const ACTIVATE_BY_ADMIN  = 0;
	const ACTIVATE_BY_EMAIL  = 1;
	const ACTIVATE_INSTANTLY = 2;
}
final class ProductVisibility
{
	const Invisible 		= 0;
	const Visible 			= 1;
}
final class NCSections
{
	const Products 		= 'products';
	const Orders 		= 'orders';
	const System 		= '--SYSTEM--';	
	const Variance  	= 'variance';
}