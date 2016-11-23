<?php
/**
 * @version SOFORT iDEAL - $Date: 2012-09-06 13:49:09 +0200 (Thu, 06 Sep 2012) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofortInstallIdeal.php 5326 2012-09-06 11:49:09Z boehm $
 */

//Notice: DB-field orders_status_name is varchar(32)!
//if following strings are longer, they will be cut!
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_TEMP_GERMAN', 'Bezahlung schwebend');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_CONFIRMED_GERMAN', 'Bestätigt');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_ABORTED_GERMAN', 'Bezahlung abgebrochen');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_LOSS_GERMAN', 'Storniert');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_COM_GERMAN', 'Teilrückerstattung');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_REF_GERMAN', 'Erstattung');

define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_TEMP_ENGLISH', 'Payment pending');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_CONFIRMED_ENGLISH', 'Confirmed');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_ABORTED_ENGLISH', 'Payment aborted');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_LOSS_ENGLISH', 'Cancelled');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_COM_ENGLISH', 'Partial refund');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_REF_ENGLISH', 'Refund');

define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_TEMP_POLISH', 'P³atno¶æ w toku');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_CONFIRMED_POLISH', 'Potwierdzone');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_ABORTED_POLISH', 'P³atno¶æ zosta³a przerwana');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_LOSS_POLISH', 'Anulowano');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_COM_POLISH', 'Czê¶ciowy zwrot');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_REF_POLISH', 'Zwrot');

define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_TEMP_DUTCH', 'In afwachting van betaling');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_CONFIRMED_DUTCH', 'Bevestigd');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_ABORTED_DUTCH', 'Betaling afgebroken');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_LOSS_DUTCH', 'Gestorneerd');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_COM_DUTCH', 'Gedeeltelijke terugbetaling');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_REF_DUTCH', 'vergoeden');

define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_TEMP_FRENCH', 'Paiement en attente');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_CONFIRMED_FRENCH', 'Confirmé');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_ABORTED_FRENCH', 'Paiement annulé');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_LOSS_FRENCH', 'Annulé');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_COM_FRENCH', 'Remboursement partiel');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_REF_FRENCH', 'Remboursement');

define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_TEMP_ITALIAN', 'Pagamento in sospeso');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_CONFIRMED_ITALIAN', 'Confermato');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_ABORTED_ITALIAN', 'Pagamento interrotto');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_LOSS_ITALIAN', 'Annullato');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_COM_ITALIAN', 'Rimborso parziale');
define('MODULE_PAYMENT_SOFORT_IDEAL_STATE_REF_REF_ITALIAN', 'Rimborso');
