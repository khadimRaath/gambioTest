<?php

/* -----------------------------------------------------------------------------------------
   Copyright (c) 2011 mediafinanz AG

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see http://www.gnu.org/licenses/.
   ---------------------------------------------------------------------------------------

 * @author Marcel Kirsch
 */

/**
 * Class to produce a dummy error in payment process
 *
 * @author Marcel Kirsch
 * @version 2008-11-01
 *
 */
class MF_DummyError
{
    private $text = '';



    /**
     * Constructor
     *
     * @param string $text
     * @return MF_DummyError
     */
    public function MF_DummyError($text)
    {
        $this->text = $text;
    }



    /**
     * Needed to display the error
     *
     * @return array;
     */
    public function get_error()
    {
        $error = array ('title' => 'Allgemeiner Fehler', 'error' => stripslashes($this->text));
        return $error;
    }
}