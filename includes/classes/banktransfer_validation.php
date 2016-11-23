<?php
/* --------------------------------------------------------------
  banktransfer_validation.php 2014-07-15 gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2014 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------

  $Id: banktransfer_validation.php 899 2006-04-29 02:40:57Z mz $

  Copyright (c) 2006 xt:Commerce

  -----------------------------------------------------------------------------------------

  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(banktransfer_validation.php,v 1.17 2003/02/18 18:33:15); www.oscommerce.com
  (c) 2003	 nextcommerce (banktransfer_validation.php,v 1.4 2003/08/1); www.nextcommerce.org
  (c) 2004 - 2006 fmce.de
  (c) 2004 - 2006 discus24.de
  (c) 2004 - 2006 Frank Maroke

 *    Released under the GNU General Public License

  -----------------------------------------------------------------------------------------

  Third Party contributions:

  OSC German Banktransfer v0.85a       	Autor:	Dominik Guder <osc@guder.org>

  Extensioncode: 							Marcel Bossert-Schwab <info@opensourcecommerce.de> (mbs)

  New methods 2005 - 2006: 				Frank Maroke (FrankM) <info@fmce.de>



  Released under the GNU General Public License

  --------------------------------------------------------------------------------------- */

class AccountCheck_ORIGIN
{
	/* Folgende Returncodes werden übergeben                                      */
	/*                                                                            */
	/* 0 -> Kontonummer & BLZ OK                                                  */
	/* 1 -> Kontonummer & BLZ passen nicht                                        */
	/* 2 -> Für diese Kontonummer ist kein Prüfziffernverfahren definiert         */
	/* 3 -> Dieses Prüfziffernverfahren ist noch nicht implementiert              */
	/* 4 -> Diese Kontonummer ist technisch nicht prüfbar                         */
	/* 5 -> BLZ nicht gefunden                                                    */
	/* 8 -> Keine BLZ übergeben                                                   */
	/* 9 -> Keine Kontonummer übergeben                                           */
	/* 10 -> Kein Kontoinhaber übergeben                                          */
	/* 128 -> interner Fehler,der zeigt, das eine Methode nicht implementiert ist */
	/*                                                                            */
	var $Bankname; // Enthält den Namen der Bank bei der Suche nach BLZ
	var $PRZ; //Enthält die Prüfziffer

////
// Diese function gibt die Bankinformationen aus der csv-Datei zurück*/

	function csv_query($p_blz)
	{
		$c_data_array = -1;
		$t_blz_file = DIR_WS_INCLUDES . 'data/blz.csv';
		if(file_exists($t_blz_file))
		{
			$t_blz_data = file_get_contents($t_blz_file);
			if(strlen(trim($t_blz_data)))
			{
				$t_position_start = strpos($t_blz_data, $p_blz);
				if($t_position_start !== false)
				{
					$t_position_end = strpos($t_blz_data, "\n", $t_position_start);
					if ($t_position_end !== false)
					{
						$t_data_string = trim(substr($t_blz_data, $t_position_start, $t_position_end-$t_position_start));
						$t_data_array = explode(';', $t_data_string);
						$c_data_array = array(
							'blz' => $t_data_array[0],
							'bankname' => $t_data_array[1],
							'prz' => $t_data_array[3]);
					}
				}
			}
		}

		if($c_data_array == -1)
		{
			$c_data_array = $this->db_query($p_blz);
		}
		return $c_data_array;
	}

////
// Diese function gibt die Bankinformationen aus der Datenbank zurück*/
	function db_query($blz)
	{
		$blz_query = xtc_db_query("SELECT * from banktransfer_blz WHERE blz = '" . xtc_db_input($blz) . "'");
		if(xtc_db_num_rows($blz_query))
		{
			$data = xtc_db_fetch_array($blz_query);
		}else
			$data = -1;
		return $data;
	}

////
// Diese function gibt die Bankinformationen aus der Datenbank zurück*/
	function query($blz)
	{
		if(MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ == 'true' && defined(MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ))
			$data = $this->db_query($blz);
		else
			$data = $this->csv_query($blz);
		return $data;
	}

	/* -------- Dies ist die wichtigste function ---------- */

	function CheckAccount($banktransfer_number, $banktransfer_blz)
	{
		$KontoNR = preg_replace('/[^0-9]/', '', $banktransfer_number); // Hetfield - 2009-08-19 - replaced deprecated function ereg_replace with preg_replace to be ready for PHP >= 5.3
		$BLZ = preg_replace('/[^0-9]/', '', $banktransfer_blz); // Hetfield - 2009-08-19 - replaced deprecated function ereg_replace with preg_replace to be ready for PHP >= 5.3

		$Result = 0;
		if($BLZ == '' || strlen($BLZ) < 8)
		{
			return 8;  /* Keine BLZ übergeben */
		}
		if($KontoNR == '')
		{
			return 9;  /* Keine Kontonummer übergeben */
		}

		/*     Beginn Implementierung */
		$adata = $this->query($BLZ);
		if($adata == -1)
		{
			$Result = 5; // BLZ nicht gefunden;
			$PRZ = -1;
			$this->PRZ = $PRZ;
			$this->banktransfer_number = ltrim($banktransfer_number, "0");
			$this->banktransfer_blz = $banktransfer_blz;
		}
		else
		{
			$this->Bankname = $adata['bankname'];
			$this->PRZ = str_pad($adata['prz'], 2, "0", STR_PAD_LEFT);
			$this->banktransfer_number = ltrim($banktransfer_number, "0");
			//$this->banktransfer_number=$this->ExpandAccount($banktransfer_number);
			$this->banktransfer_blz = $banktransfer_blz;

			$PRZ = $adata['prz'];
			
			$coo_blz_validation = MainFactory::create_object('BLZValidation');

			switch($PRZ)
			{
				case "52" : $Result = $coo_blz_validation->Mark52($KontoNR, $BLZ);
					break;
				case "53" : $Result = $coo_blz_validation->Mark53($KontoNR, $BLZ);
					break;
				/* --- Added FrankM 20060112 --- */
				case "B6" : $Result = $coo_blz_validation->MarkB6($KontoNR, $BLZ);
					break;
				case "C0" : $Result = $coo_blz_validation->MarkC0($KontoNR, $BLZ);
					break;
				default:
					$Result = $coo_blz_validation->call_method("Mark$PRZ", $KontoNR);
			} /* end switch */
		} /* end if num_rows */

		return $Result;
	}

/* End of CheckAccount */
}
/* End Class AccountCheck */