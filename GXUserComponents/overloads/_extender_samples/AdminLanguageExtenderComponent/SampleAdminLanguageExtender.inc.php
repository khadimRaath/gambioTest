<?php
/* --------------------------------------------------------------
   SampleAdminLanguageExtender.inc.php 2016-03-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SampleAdminLanguageExtender
 *
 * This is a sample overload for the AdminLanguageExtenderComponent.
 *
 * @see AdminLanguageExtenderComponent
 */
class SampleAdminLanguageExtender extends SampleAdminLanguageExtender_parent
{
    /**
     * Overloaded "proceed" method.
     */
    public function proceed()
    {
        $logControl = MainFactory::create_object('LogControl', array(), true);

        switch($this->v_data_array['GET']['action'])
        {
            case 'insert':
                $logControl->notice('The language with ID ' .
                    (int)$this->v_data_array['insert_id'] .
                    ' was created.');
                break;
            case 'copy':
                $logControl->notice('The language with ID ' .
                    (int)$this->v_data_array['insert_id'] .
                    ' wurde von einer Sprache mit der ID ' .
                    (int)$this->v_data_array['POST']['ref_language_id'] .
                    ' was copied.');
                break;
            case 'save':
                $logControl->notice('The language with ID ' .
                    (int)$this->v_data_array['GET']['lID'] .
                    ' was changed.');
                break;
            case 'deleteconfirm':
                $logControl->notice('The language with ID ' .
                    (int)$this->v_data_array['GET']['lID'] .
                    ' was deleted.');
                break;
        }

        parent::proceed();
    }
}