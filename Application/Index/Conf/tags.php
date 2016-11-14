<?php
defined('THINK_PATH') or exit();
return array(
        'app_begin'     =>  array(
            'Index\Behavior\CheckTemplateBehavior',
        ),    
        'view_filter'   =>  array(
            'Index\Behavior\TemplateReplaceBehavior',
        ),    
);