DROP TABLE IF EXISTS `gm_contents`;
CREATE TABLE `gm_contents` (
  `gm_contents_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `languages_id` int(10) unsigned NOT NULL DEFAULT '0',
  `gm_key` varchar(255) NOT NULL DEFAULT '',
  `gm_value` text NOT NULL,
  `gm_group_id` int(11) NOT NULL DEFAULT '0',
  `gm_sort_order` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gm_contents_id`),
  UNIQUE KEY `gm_content` (`languages_id`,`gm_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

INSERT INTO `gm_contents` SET `gm_key` = 'EMAIL_BILLING_SUBJECT_ORDER', `languages_id` = 1, `gm_value` = 'Your order {$nr}, {$date}', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'EMAIL_BILLING_SUBJECT_ORDER', `languages_id` = 2, `gm_value` = 'Ihre Bestellung {$nr}, am {$date}', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_BOOKMARKS_ARTICLES', `languages_id` = 0, `gm_value` = '1', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_BOOKMARKS_CATEGORIES', `languages_id` = 0, `gm_value` = '1', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_BOOKMARKS_REST', `languages_id` = 0, `gm_value` = '1', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_BOOKMARKS_START', `languages_id` = 0, `gm_value` = '1', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_CONTACT', `languages_id` = 1, `gm_value` = 'you@example.com', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_CONTACT', `languages_id` = 2, `gm_value` = 'admin@shop.de', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_DESCRIPTION', `languages_id` = 1, `gm_value` = 'Add this Search to your Browser Instant Search', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_DESCRIPTION', `languages_id` = 2, `gm_value` = 'Super Such Beschreibung', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_LINK', `languages_id` = 1, `gm_value` = 'Add this Search to your Browser Instant Search', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_LINK', `languages_id` = 2, `gm_value` = 'Suche zu Ihrer Browser-Schnellsuche hinzufügen.', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_LONGNAME', `languages_id` = 1, `gm_value` = 'Shop Instant Search', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_LONGNAME', `languages_id` = 2, `gm_value` = 'Unsere Shopsuche findet alles', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_SHORTNAME', `languages_id` = 1, `gm_value` = 'Shop Search', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_SHORTNAME', `languages_id` = 2, `gm_value` = 'Shop Suche', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_TAGS', `languages_id` = 1, `gm_value` = 'Instant Search', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_TAGS', `languages_id` = 2, `gm_value` = 'Shopsuche Suchen Shop', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_TEXT', `languages_id` = 1, `gm_value` = 'Instant Search', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_OPENSEARCH_TEXT', `languages_id` = 2, `gm_value` = 'Browser-Schnellsuche', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_COMPANY_ADRESS_LEFT', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_COMPANY_ADRESS_LEFT', `languages_id` = 2, `gm_value` = 'Ihr Name, Ihre Strasse 1, 12345 Ihr Ort', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_COMPANY_ADRESS_RIGHT', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_COMPANY_ADRESS_RIGHT', `languages_id` = 2, `gm_value` = 'Ihr Name \nIhre Strasse 1 \n\n12345 Ihr Ort', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_CONDITIONS', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_CONDITIONS', `languages_id` = 2, `gm_value` = 'Hier könnten Ihre AGB stehen.', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_EMAIL_SUBJECT', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_EMAIL_SUBJECT', `languages_id` = 2, `gm_value` = 'Ihre Rechnung der Nr. {INVOICE_ID} vom {DATE} mit der Bestellnummer {ORDER_ID}', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_SALUTATION_MALE', `languages_id` = 1, `gm_value` = 'Mr.', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_SALUTATION_MALE', `languages_id` = 2, `gm_value` = 'geehrter Herr', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_SALUTATION_FEMALE', `languages_id` = 1, `gm_value` = 'Mrs.', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_SALUTATION_FEMALE', `languages_id` = 2, `gm_value` = 'geehrte Frau', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_FOOTER_CELL_1', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_FOOTER_CELL_1', `languages_id` = 2, `gm_value` = 'Ihr Name \nIhre Strasse 1 \n\n12345 Ihr Ort', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_FOOTER_CELL_2', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_FOOTER_CELL_2', `languages_id` = 2, `gm_value` = 'Ihre Telefonnummer\nIhre Faxnummer\nIhre Homepage\nIhre E-Mail', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_FOOTER_CELL_3', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_FOOTER_CELL_3', `languages_id` = 2, `gm_value` = 'Ihre Steuernummer\nIhre Ust. ID. Nr.\nIhre Gerichtsbarkeit\nIhre Informationen', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_FOOTER_CELL_4', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_FOOTER_CELL_4', `languages_id` = 2, `gm_value` = 'Zusätzliche\nInformationen\nin der vierten\nSpalte\n', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_CONDITIONS', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_CONDITIONS', `languages_id` = 2, `gm_value` = 'Unsere AGB', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_INFO_TEXT_INVOICE', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_INFO_TEXT_INVOICE', `languages_id` = 2, `gm_value` = 'Rechnungshinweis', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_INFO_TEXT_PACKINGSLIP', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_INFO_TEXT_PACKINGSLIP', `languages_id` = 2, `gm_value` = 'Lieferhinweis', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_INVOICE', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_INVOICE', `languages_id` = 2, `gm_value` = 'Ihre Rechnung', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_PACKINGSLIP', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_PACKINGSLIP', `languages_id` = 2, `gm_value` = 'Ihr Lieferschein', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_WITHDRAWAL', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_HEADING_WITHDRAWAL', `languages_id` = 2, `gm_value` = 'Unser Widerrufsrecht', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_INFO_TEXT_INVOICE', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_INFO_TEXT_INVOICE', `languages_id` = 2, `gm_value` = 'Ihr Hinweistext für die Rechnung', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_INFO_TEXT_PACKINGSLIP', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_INFO_TEXT_PACKINGSLIP', `languages_id` = 2, `gm_value` = 'Ihr Hinweistext für den Lieferschein', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_INFO_TITLE_INVOICE', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_INFO_TITLE_INVOICE', `languages_id` = 2, `gm_value` = 'Beachten Sie bitte', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_INFO_TITLE_PACKINGSLIP', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_INFO_TITLE_PACKINGSLIP', `languages_id` = 2, `gm_value` = 'Beachten Sie bitte', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_WITHDRAWAL', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_PDF_WITHDRAWAL', `languages_id` = 2, `gm_value` = 'Hier könnte Ihr Widerrufsrecht stehen.', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_SCROLLER_CONTENT', `languages_id` = 1, `gm_value` = '<p>News</p>', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_SCROLLER_CONTENT', `languages_id` = 2, `gm_value` = '<p style="text-align: center;"><strong>Hier ist genug Platz für Ihre Texte, Links und Bilder!</strong></p>\r\n<p style="text-align: center;"> </p>\r\n<p style="text-align: center;"><a href="https://www.gambio.de"><strong><img height="64" width="145" src="images/gambio-logo.jpg" alt="" /></strong></a></p>', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_TITLE_STANDARD_META_TITLE', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_TITLE_STANDARD_META_TITLE', `languages_id` = 2, `gm_value` = '', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_TITLE_STANDARD_META_TITLE_SEPARATOR', `languages_id` = 1, `gm_value` = ' - ', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'GM_TITLE_STANDARD_META_TITLE_SEPARATOR', `languages_id` = 2, `gm_value` = ' - ', `gm_group_id` = 0;
INSERT INTO `gm_contents` SET `gm_key` = 'date', `languages_id` = 2, `gm_value` = '', `gm_group_id` = 1;
INSERT INTO `gm_contents` SET `gm_key` = 'keywords', `languages_id` = 1, `gm_value` = '', `gm_group_id` = 1;
INSERT INTO `gm_contents` SET `gm_key` = 'keywords', `languages_id` = 2, `gm_value` = 'keywords,kommagetrennt', `gm_group_id` = 1;
INSERT INTO `gm_contents` SET `gm_key` = 'robots', `languages_id` = 1, `gm_value` = 'index,follow', `gm_group_id` = 1;
INSERT INTO `gm_contents` SET `gm_key` = 'robots', `languages_id` = 2, `gm_value` = 'index,follow', `gm_group_id` = 1;
