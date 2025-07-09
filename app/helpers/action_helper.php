<?php


class ActionHelper
{

        public static function iconButon(String $iconData)
    {
        return  <<<HTML
       <i class="{$iconData} action-icon"></i>
HTML;
    }


  public static function bntIcon( String $iconData = 'bi bi-plus', String $href = "#", String $typeBnt='btn-success',): String
    {
        return  <<<HTML
        <a class="btn btn-sm {$typeBnt}" href="{$href}" role="button"> <i class="{$iconData}"></i></a>
        
HTML;
    }

  

     public static function bntIconLabel(String $title,  String $iconData = 'bi bi-plus', String $href = "#", String $typeBnt='btn-success',): String
    {
        return  <<<HTML
        <a class="btn btn-sm {$typeBnt}" href="{$href}" role="button"> <i class="{$iconData}"></i>$title</a>
        
HTML;
    }

 
    public static function generateActions(array $actions)
    {
        return implode(' ', $actions);
    }

  
}