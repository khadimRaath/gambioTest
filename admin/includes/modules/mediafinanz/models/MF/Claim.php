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
 * Class for a claim
 *
 * @author Marcel Kirsch
 * @version 2009-01-12
 *
 */
class MF_Claim
{
    private $invoice;
    private $type;
    private $reason;
    private $originalValue;
    private $overdueFees;
    private $dateOfOrigin;
    private $dateOfReminder;
    private $note;



    /**
     * Constructor
     *
     * @param string $invoice
     * @param int $type
     */
    public function __construct($invoice, $type)
    {
        $this->invoice = $invoice;
        $this->type    = $type;
    }



    /**
     * Get invoice number
     *
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }



    /**
     * Get the type of this claim
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }



    /**
     * Set the reason
     *
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }



    /**
     * Get the reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }



    /**
     * Set the original value of this claim
     *
     * @param double $value
     */
    public function setOriginalValue($value)
    {
        $this->originalValue = $value;
    }



    /**
     * Get the original value of this claim
     *
     * @return double
     */
    public function getOriginalValue()
    {
        return $this->originalValue;
    }



    /**
     * Set the overdue fees for this claim
     *
     * @param double $value
     */
    public function setOverdueFees($value)
    {
        $this->overdueFees = $value;
    }



    /**
     * get the overdue fees
     *
     * @return double
     */
    public function getOverdueFees()
    {
        return $this->overdueFees;
    }



    /**
     * set the date of origin for this claim (YYYY-MM-DD)
     *
     * @param string $date
     */
    public function setDateOfOrigin($date)
    {
        $this->dateOfOrigin = $date;
    }



    /**
     * get the date of origin for this claim (YYYY-MM-DD)
     *
     * @return string
     */
    public function getDateOfOrigin()
    {
        return $this->dateOfOrigin;
    }



    /**
     * Set the date for the reminder (YYYY-MM-DD)
     *
     * @param string $date
     */
    public function setDateOfReminder($date)
    {
        $this->dateOfReminder = $date;
    }



    /**
     * Get the date of the reminder (YYYY-MM-DD)
     *
     * @return string
     */
    public function getDateOfReminder()
    {
        return $this->dateOfReminder;
    }



    /**
     * Set an additional note
     *
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }



    /**
     * Get the additional note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }
}