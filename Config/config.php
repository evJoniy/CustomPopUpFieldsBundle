<?php

return [
    'name' => 'CustomPopUpFields',
    'description' => 'Enables custom fields usage in pop-ups (focuses)',
    'author' => 'TED',
    'version' => '1.0.0',
    'services' => [
        'events' => [
            'mautic.custompopupfields.subscriber.contact_tracker' => [
                'class' => \MauticPlugin\CustomPopUpFieldsBundle\EventListener\TrackingSubscriber::class,
            ],
        ],
        'controllers' => [
            'mautic.popup.controller.callback' => [
                'class'     => \MauticPlugin\CustomPopUpFieldsBundle\Controller\UserDataController::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
        ],
    ],
    'routes' => [
        'public' => [
            'mautic_api_popup_get_data' => [
                'path'       => '/popup',
                'controller' => 'CustomPopUpFieldsBundle:UserData:send',
            ],
        ],
    ],
];
