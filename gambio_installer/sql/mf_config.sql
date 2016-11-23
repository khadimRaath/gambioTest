DROP TABLE IF EXISTS `mf_config`;
CREATE TABLE `mf_config` (
  `config_key` varchar(255) NOT NULL DEFAULT '',
  `config_value` varchar(255) NOT NULL DEFAULT '',
  KEY `config_name` (`config_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('person.active', '1');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('company.active', '1');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('sandbox', '1');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('applicationLicence', 'ff24b9634bb8e0b24a15a7cbbddc5fe6');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('clientLicence', '');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('clientId', '');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('recheckSuspect', '30');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('buergel.score', '2.3');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('paymentModules', 'invoice,banktransfer');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('orderTotal', '100');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('allowPaymentWithNoResult', '0');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('daysUntilClaimStart', '20');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('orderStatusIdMarked', '1');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('displayClaimsCount', '5');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('overdueFees', '5.00');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('lastStatusUpdate', '1207030153');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('statusUpdateInterval', '3');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('daysFromLastReminder', '10');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('defaultType', '1');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('requestType', 'always');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('requestOnModules', 'invoice,banktransfer');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('paymentErrorText', 'Leider ist die ausgewählte Zahlart zur Zeit nicht verfügbar. Bitte wählen Sie eine andere Zahlart aus.');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('minAmountForRequest', '0');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('maxAmountForRequest', '0');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('allowPaymentUnderMinAmount', '0');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('allowPaymentOverMaxAmount', '0');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('defaultPersonScore', 'MF_Score_BuergelClient');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('defaultCompanyScore', 'MF_Score_AccumioClient');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('accumio.score', '2');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('accumio.minSimilarity', '80');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('version', '0.3.2');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('applicationId', '317');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('registrationKey', '');
INSERT INTO `mf_config` (`config_key`, `config_value`) VALUES('serviceLicenceKey', '9c889dd2408ca44b1cd9e12ba40333fa');