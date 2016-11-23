<?php

/**
 * Class EkomiIntegrationModuleCenterModule
 *
 * This is a EkomiIntegration overload for the AbstractModuleCenterModule.
 * 
 * @see AbstractModuleCenterModule
 * 
 * @extends    AbstractModuleCenterModule
 * @category   System
 * @package    Modules
 * @author     Sandor Barics <sbarics@ekomi.de>
 */
class EkomiIntegrationModuleCenterModule extends AbstractModuleCenterModule {

    protected function _init() {
        $this->title = $this->languageTextManager->get_text('ekomiIntegration_title');
        $this->description = $this->languageTextManager->get_text('ekomiIntegration_description');
        $this->sortOrder = 87330;
    }

}
