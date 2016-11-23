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
 * Interface for all scoring clients
 *
 * @author Marcel Kirsch
 * @version 2009-01-06
 *
 */
interface MF_Score_Interface
{
    /**
     * Gets a score
     * returns array on success and false otherwise
     *
     * @param MF_Suspect $suspect
     * @return mixed
     */
    public function getScore(MF_Suspect $suspect);



    /**
     * Returns if an valid score already exists
     *
     * @param MF_Suspect $suspect
     * @param double $orderTotal
     * @return boolean
     */
    public function hasValidScore(MF_Suspect $suspect, $orderTotal = 0);



    /**
     * Gets an stored score
     * Returns array on success and false otherwise
     *
     * @param MF_Suspect $suspect
     * @return mixed
     */
    public function getOldScore(MF_Suspect $suspect);



    /**
     * saves a score
     *
     * @param MF_Suspect $suspect
     * @param array $score
     */
    public function saveScore(MF_Suspect $suspect, $score);



    /**
     * Returns if a score is good enough to be accepted
     *
     * @param array $score
     * @return boolean
     */
    public function isPositiveScore($score);
}