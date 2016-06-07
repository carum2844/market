<?php
/**
*
* @package Carum - Market
* @copyright (c) 2016 Carlos Cusi ( Carum )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(

		'ACP_MARKET_TITLE'				=> 'Mercado',
		'ACP_MARKET_CONFIG_TITLE'		=> 'Configuración',
		'ACP_MARKET_ADS_TITLE'			=> 'Anuncios',
		'ACP_MARKET_USERS_TITLE'		=> 'Usuarios',

		'ACP_CARUM_MARKET_ENABLED'			=> 'Activar mercado',
		'ACP_CARUM_MARKET_BIDS_ENABLED'		=> 'Activar subastas',
		'ACP_CARUM_MARKET_BOOKS_ENABLED'	=> 'Activate reservas',

		'ACP_MARKET_AD_TITLE'				=> 'Editar anuncio',

		'ACP_ADS_SEARCH_USER'				=> 'Buscar anuncios del usuario',
		'ACP_AD_ID'							=> 'Num.',
		'ACP_AD_USER'						=> 'Usuario',
		'ACP_AD_STATE'						=> 'Estado',
		'ACP_AD_ITEM_NAME'					=> 'Título',
		'ACP_AD_REGDATE'					=> 'Fecha alta',
		'ACP_AD_ACTIVE'						=> 'Activo',
		'AD_ADD'							=> 'Añadir anuncio',
					
		'ACP_MARKET_SETTINGS_SAVED'			=> 'Configuración guardada satisfactoriamente',

		'MARKET_TITLE'				=> 'El Mercado',
		'MARKET_LINK'				=> 'mercado',
		'MARKET_SELECT_CATEGORY'	=> 'Seleccione categoria',
		'MARKET_CREATE_MARKET'		=> 'Crear anuncio',
		'MARKET_LINK_DISPLAY_ITEM'	=> 'mostrar_ficha',
		'MARKET_REQUEST'			=> 'Crear un anuncio para el mercado',
		'MARKET_LINK_ADD_AD'			=> 'crear_anuncio',
		
		'AD_AD'					=> 'Anuncio',
		'AD_ITEM'				=> 'La Cosa',
		'AD_USER'				=> 'Usuario',
		'AD_STATE'				=> 'Estado anuncio',
		'ACTIVE'				=> 'Activo',
		'DRAFT'					=> 'Borrador',
		'EXPIRED'				=> 'Expirado',
		'DELETED'				=> 'Borrado',
		'RESERVED'				=> 'Reservado',
		'SOLD'					=> 'Vendido',
		'AD_ITEM_ACTION'		=> 'Acción del anuncio',
		'SELL'					=> 'Vender',
		'BUY'					=> 'Comprar',
		'CATALOG'				=> 'Poner en catalogo',
		'AD_TYPE_ITEM'			=> 'Se trata de ...',
		'UNKNOWN'				=> 'Desconocido',
		'MACHINE'				=> 'Máquina',
		'ARTICLE'				=> 'Artículo',
		'SERVICE'				=> 'Servicio',
		'AD_CATEGORIES'			=> 'Categoria',
		'AD_ALLOW_AUCTION'		=> 'Permitir subasta en este anuncio',
		'AD_ITEM_NAME'			=> 'Titulo',
		'AD_ITEM_SHORT_DESCRIPTION'	=> 'Breve descripción',
		'AD_METHOD'				=> 'Metodo venta',
		'METHOD_ASK_PRICE'		=> 'Solicitar precio',
		'AD_METHOD_ASK_PRICE'	=> 'Precio',
		'AD_IS_BID'				=> 'Subasta',
		'METHOD_BID_OFFER'		=> 'Subasta / Hacer oferta',
		'AD_ITEM_PRICE'			=> 'Precio',
		'AD_PRICE_HIDDEN'		=> 'Oculto',
		'AD_ITEM_HIDE_PRICE'	=> 'Ocultar precio, (Sólo los usuarios registrados podrán verlo)',
		'AD_ITEM_BUY_NOW_PRICE'	=> 'Precio comprar ahora, cerrar subasta/ofertas.',
		'AD_ITEM_START_PRICE'	=> 'Precio de salida de la subasta.',
		'AD_ITEM_MIN_BID_PRICE'	=> 'Puja mínima',
		'AD_ITEM_LAST_BID'		=> 'Última puja',
		'AD_ITEM_NO_BIDS'		=> 'No hay pujas',
		'AD_ITEM_SHOW_MIN_BID'	=> 'mostrar en anuncio (Incremento mínimo de cada puja)',
		'AD_ITEM_ACTUAL_PRICE'	=> 'Precio actual según pujas/ofertas',
		'AD_ITEM_NEW_USED'		=> 'Estado',
		'NEW'					=> 'Nuevo',
		'USED'					=> 'Usado',
		'AD_ITEM_STATE'			=> 'Situación del bien',
		'NOT_DECLARED'			=> 'Estado no declarado',
		'WORKS'					=> 'Funciona',
		'NEED_REPAIR'			=> 'Necesita reparación',
		'NOT_WORKING'			=> 'Para piezas',
		'AD_ITEM_PRODUCT_NAME'	=> 'Identificación',
		'MARK'					=> 'Marca',
		'MODEL'					=> 'Modelo',
		'SERIAL'				=> 'Nº Serie',
		'NOT_IN_LIST'			=> 'No está en la lista',
		'NOT_EXIST'				=> 'No existe',
		'AD_ITEM_PRODUCTION_YEAR'	=> 'Año de fabricación',
		'AD_ITEM_ADQUIRED_YEAR'	=> 'Año de compra',
		'AD_ITEM_USABLE_WIDTH'	=> 'Ancho útil',
		'MILIMETRES'			=> 'Milímetros',
		'CENTIMETRES'			=> 'Centímetros',
		'METRES'				=> 'Metros',
		'INCHES'				=> 'Pulgadas',
		'AD_ITEM_WEIGHT'		=> 'Peso',
		'GRAMS'					=> 'Gramos',
		'KILOGRAMS'				=> 'Kilogramos',
		'TONS'					=> 'Toneladas',
		'POUNDS'				=> 'Libras',
		'AD_ITEM_SHIPMENT'		=> 'Pago del transporte',
		'AGREEMENT'				=> 'Acuerdo entre comprador y vendedor',
		'INCLUDED'				=> 'Incluido en el precio',
		'EXCLUDED'				=> 'A cargo comprador',
		'AD_ITEM_REGDATE'		=> 'Fecha alta anuncio',
		'AD_ITEM_VIEWS'			=> 'Visitas',
		'AD_ITEM_SOS'			=> 'Servicio Operación Segura',
		'AD_ACTIVE'				=> 'Activo',
		'AD_ITEM_BOOKED_BY'		=> 'Reservado por',
		'AD_ITEM_BOOKED_DATE'	=> 'Fecha reserva',
		'AD_DATE_FORMAT'		=> 'Formato fecha dd/mm/aaa',
		'AD_ITEM_YOUTUBE'		=> 'Video YouTube',
		'AD_ASKS'				=> 'Preguntas',
		'ACP_ASKS_USER'			=> 'Usario',
		'ACP_ASKS_ASK'			=> 'Pregunta',
		'ACP_ASKS_ANSWER'		=> 'Respuesta',
		'ACP_BIDS'				=> 'Pujas',
		'ACP_BID_USER'			=> 'Usuario',
		'ACP_BID_DATE'			=> 'Fecha de la puja',
		'ACP_BID_PRICE'			=> 'Precio',
		
		'AD_IMAGES'				=> 'Imagenes del anuncio',
		'FILE_DROP'				=> 'Arrastrar imagen',
		'FILE_TYPE_NOT_ALLOWED'	=> 'Formato de archivo no soportado. Pruebe con jpg, gif o png',
		'CHANGE_IMAGE'			=> 'Cambiar imagen',
		'DELETE_IMAGE'			=> 'Borrar imagen',
		
		'AD_ITEM_REGDATE_NEEDED'			=> 'La fecha de alta del anuncio es requerida.',
		'AD_ITEM_REGDATE_BAD'				=> 'La fecha de alta del anuncio parece que no es correcta.',
		'AD_USER_NEEDED'					=> 'Un usuario responsable de este anuncio es requerido.',
		'AD_ITEM_NAME_NEEDED'				=> 'Un título para el anuncio es requerido.',
		'AD_ITEM_START_PRICE_NEEDED'		=> 'Un precio de salida para la subasta es requerido.',
		'AD_ITEM_MIN_BID_PRICE_NEEDED'		=> 'Un precio de puja mínima para la subasta es requerido.',
		'AD_ITEM_NAME_NEEDED'				=> 'Un nombre / marca / modelo es requerido.',
		'AD_ITEM_BOOKED_DATE_BAD'			=> 'La fecha de la reserva parece que no es correcta.',
		'AD_ITEM_DATE_ACTUAL_PRICE_BAD'		=> 'La fecha del precio actual parece que no es correcta.',
		
		'MARKET_AD_SAVED'		=> 'Anuncio guardado satisfactoriamente',
		
		
		
		
		
		
		
		
		
	));
