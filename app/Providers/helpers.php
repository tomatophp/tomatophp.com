<?php


if(!function_exists('appTitle')){
    function appTitle(string $title=null): string
    {
        return ($title?$title . ' | ':null).(app()->getLocale() === 'en' ? str(setting('site_name'))->explode('|')[0]??setting('site_name') : str(setting('site_name'))->explode('|')[1]??setting('site_name'));
    }
}

if(!function_exists('appDescription')){
    function appDescription(string $description=null): string
    {
        return ($description?$description . ' | ':null).(app()->getLocale() === 'en' ? str(setting('site_description'))->explode('|')[0]??setting('site_description') : str(setting('site_description'))->explode('|')[1]??setting('site_description'));
    }
}

if(!function_exists('appKeywords')){
    function appKeywords(string $keywords=null): string
    {
        return ($keywords?$keywords . ' | ':null).(app()->getLocale() === 'en' ? str(setting('site_keywords'))->explode('|')[0]??setting('site_keywords') : str(setting('site_keywords'))->explode('|')[1]??setting('site_keywords'));
    }
}

