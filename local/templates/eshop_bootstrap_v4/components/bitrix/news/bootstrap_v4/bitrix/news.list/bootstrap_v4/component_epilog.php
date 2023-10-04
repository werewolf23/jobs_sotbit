<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
global $APPLICATION;

if(isset($arResult["META_SPECIALDATE_VALUE"]))
    $APPLICATION->SetPageProperty('specialdate', $arResult["META_SPECIALDATE_VALUE"]);