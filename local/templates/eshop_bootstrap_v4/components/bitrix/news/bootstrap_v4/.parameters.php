<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
{
	die();
}

/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Iblock\PropertyTable;

if (!Loader::includeModule('iblock'))
{
	return;
}

$mediaProperty = array(
	"" => GetMessage("MAIN_NO"),
);
$sliderProperty = array(
	"" => GetMessage("MAIN_NO"),
);
$iblockExists = (!empty($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0);
if ($iblockExists)
{
	$propertyList = CIBlockProperty::GetList(
		["sort" => "asc", "name" => "asc"],
		["ACTIVE" => "Y", "IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"]]
	);
	while ($property = $propertyList->Fetch())
	{
		$id = $property["CODE"] ?: $property["ID"];
		if ($property["PROPERTY_TYPE"] === PropertyTable::TYPE_STRING)
		{
			$mediaProperty[$id] = "[" . $id . "] " . $property["NAME"];
		}
		if ($property["PROPERTY_TYPE"] === PropertyTable::TYPE_FILE)
		{
			$sliderProperty[$id] = "[" . $id . "] " . $property["NAME"];
		}
	}
}

$arTemplateParameters = array(
	"DISPLAY_DATE" => array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PICTURE" => array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PREVIEW_TEXT" => array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"USE_SHARE" => array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_USE_SHARE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" =>"N",
		"REFRESH"=> "Y",
	),
	"LIST_USE_SHARE" => array(
		"NAME" => GetMessage("TP_BN_MEDIA_PROPERTY"),
		"TYPE" => "LIST",
		"VALUES" => $mediaProperty,
	),
	"SLIDER_PROPERTY" => array(
		"NAME" => GetMessage("TP_BN_SLIDER_PROPERTY"),
		"TYPE" => "LIST",
		"VALUES" => $sliderProperty,
	),
    "META_SPECIALDATE" => array(
        "NAME" => GetMessage("META_SPECIALDATE"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" =>"N",
    ),
);

if (($arCurrentValues['USE_SHARE'] ?? 'N') === 'Y')
{
	$arTemplateParameters["LIST_USE_SHARE"] = array(
		"NAME" => GetMessage("TP_BN_LIST_USE_SHARE"),
		"TYPE" => "CHECKBOX",
		"VALUE" => "Y",
		"DEFAULT" => "N",
	);

	$arTemplateParameters["SHARE_TEMPLATE"] = array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_SHARE_TEMPLATE"),
		"DEFAULT" => "",
		"TYPE" => "STRING",
		"MULTIPLE" => "N",
		"COLS" => 25,
		"REFRESH"=> "Y",
	);

	$shareComponentTemplate = (trim((string)($arCurrentValues["SHARE_TEMPLATE"] ?? '')));
	if ($shareComponentTemplate === '')
	{
		$shareComponentTemplate = false;
	}

	include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/bitrix/main.share/util.php");

	$arHandlers = __bx_share_get_handlers($shareComponentTemplate);

	$arTemplateParameters["SHARE_HANDLERS"] = array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_SHARE_SYSTEM"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arHandlers["HANDLERS"],
		"DEFAULT" => $arHandlers["HANDLERS_DEFAULT"],
	);

	$arTemplateParameters["SHARE_SHORTEN_URL_LOGIN"] = array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_SHARE_SHORTEN_URL_LOGIN"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);

	$arTemplateParameters["SHARE_SHORTEN_URL_KEY"] = array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_SHARE_SHORTEN_URL_KEY"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
}

$arThemes = array();
$arThemes['blue'] = GetMessage('TP_BN_THEME_BLUE');
$arThemes['green'] = GetMessage('TP_BN_THEME_GREEN');
$arThemes['red'] = GetMessage('TP_BN_THEME_RED');
$arThemes['yellow'] = GetMessage('TP_BN_THEME_YELLOW');

if (ModuleManager::isModuleInstalled('bitrix.eshop'))
{
	$arThemes['site'] = GetMessage('TP_BN_THEME_SITE');
}

$arTemplateParameters['TEMPLATE_THEME'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage("TP_BN_TEMPLATE_THEME"),
	'TYPE' => 'LIST',
	'VALUES' => $arThemes,
	'DEFAULT' => 'blue',
	'ADDITIONAL_VALUES' => 'Y',
);