<?php

namespace MauticPlugin\CustomPopUpFieldsBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class CustomPopUpFieldsIntegration extends AbstractIntegration
{
    public function getName()
    {
        return 'CustomPopUpFields';
    }

    /**
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'none';
    }
}
