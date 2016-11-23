<?php
/* --------------------------------------------------------------
  TerminalSdkLibrary.php 2015-04-24 nik
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class TerminalSdkLibrary {
    
        const VERSION = 'V2';
        
	private static $initDone = FALSE;
	private static $autoloadDirs = array();
	private static $logger = NULL;

        public static function init() {
            if(self::$initDone == TRUE)
                    return;
            
            self::$initDone = TRUE;
            $LIBRARY_DIR = dirname(__FILE__);
            
            if(!class_exists('Raa_TerminalClient', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/TerminalClient.php';
            }

            // Inherits from Raa_TerminalClient
            if(!class_exists('Raa_AbstractClient', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/AbstractClient.php';
            }
            
            if(!class_exists('Raa_CheckedSignatureData', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/CheckedSignatureData.php';
            }
            
            if(!class_exists('Raa_ClientException', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/ClientException.php';
            }

            // Inherits from Raa_ClientException
            if(!class_exists('Raa_ClientTimestampException', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/ClientTimestampException.php';
            }

            if(!class_exists('Raa_Logger', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/Logger.php';
            }

            if(!class_exists('Raa_MatchResponse', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/MatchResponse.php';
            }

            // Inherits from Raa_MatchResponse
            if(!class_exists('Raa_MatchResponseWithSecureConfirmation', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/MatchResponseWithSecureConfirmation.php';
            }

            // Inherits from Raa_MatchResponseWithSecureConfirmation
            if(!class_exists('Raa_MatchResponseWithSecureConfirmationAndIdentity', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/MatchResponseWithSecureConfirmationAndIdentity.php';
            }

            if(!class_exists('Raa_MatchServiceResolver', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/MatchServiceResolver.php';
            }

            // Inherits from Raa_MatchServiceResolver
            if(!class_exists('Raa_MatchServiceResolverAlgT', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/MatchServiceResolverAlgT.php';
            }

            // Inherits from Raa_MatchServiceResolver
            if(!class_exists('Raa_MatchServiceResolverFixedUri', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/MatchServiceResolverFixedUri.php';
            }

            if(!class_exists('Raa_QrCodeData', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/QrCodeData.php';
            }

            if(!class_exists('Raa_QrCodeDataFactory', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/QrCodeDataFactory.php';
            }

            // Inherits from Raa_QrCodeData
            if(!class_exists('Raa_QrCodeDataMatchVersion1', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/QrCodeDataMatchVersion1.php';
            }

            // Inherits from Raa_QrCodeDataMatchVersion1
            if(!class_exists('Raa_QrCodeDataMatchVersion2', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/QrCodeDataMatchVersion2.php';
            }

            // Inherits from Raa_QrCodeData
            if(!class_exists('Raa_QrCodeDataSyncVersion1', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/QrCodeDataSyncVersion1.php';
            }

            if(!class_exists('Raa_SellerAccountInfo', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/SellerAccountInfo.php';
            }

            if(!class_exists('Raa_ServerInfo', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/ServerInfo.php';
            }

            if(!class_exists('Raa_SignedData', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/SignedData.php';
            }

            // Inherits from Raa_SignedData
            if(!class_exists('Raa_SignedDataAmount', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/SignedDataAmount.php';
            }

            // Inherits from Raa_SignedData
            if(!class_exists('Raa_SignedDataRid', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/SignedDataRid.php';
            }

            if(!class_exists('Raa_Terminal', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/Terminal.php';
            }

            if(!class_exists('Raa_TerminalClient', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/TerminalClient.php';
            }

            // Inherits from Raa_AbstractClient
            if(!class_exists('Raa_TerminalClientDefault', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/TerminalClientDefault.php';
            }

            // Inherits from Raa_AbstractClient
            if(!class_exists('Raa_TerminalClientV1Default', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/TerminalClientV1Default.php';
            }

            if(!class_exists('Raa_TerminalInfo', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/TerminalInfo.php';
            }

            if(!class_exists('Raa_TransactionData', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/TransactionData.php';
            }

            // Inherits from Raa_TransactionData
            if(!class_exists('Raa_TransactionDataAmount', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/TransactionDataAmount.php';
            }

            if(!class_exists('Raa_TransactionResult', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/TransactionResult.php';
            }
            
            if(!class_exists('Raa_HistoryRecord', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/HistoryRecord.php';
            }
            
            if(!class_exists('Raa_HistoryResult', FALSE)) {
                require_once $LIBRARY_DIR . '/raa/HistoryResult.php';
            }

            
            // QRCode generator library
            if(!class_exists('QRcode', FALSE)) {
                // QRCode Library is not yet loaded
                require_once $LIBRARY_DIR . '/phpqrcode/qrlib.php';
            }

            
            // Raautil classes
            if(!class_exists('Raautil_Utils', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/Utils.php';
            }

            if(!class_exists('Raautil_TerminalData', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/TerminalData.php';
            }

            if(!class_exists('Raautil_ITerminalDataProvider', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/ITerminalDataProvider.php';
            }

            if(!class_exists('Raautil_TerminalDataProviderInifile', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/TerminalDataProviderInifile.php';
            }

            if(!class_exists('Raautil_DataStore', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/DataStore.php';
            }

            if(!class_exists('Raautil_IDataStoreProvider', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/IDataStoreProvider.php';
            }

            if(!class_exists('Raautil_DataStoreProviderFlatfile', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/DataStoreProviderFlatfile.php';
            }

            if(!class_exists('Raautil_DataStoreProviderSession', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/DataStoreProviderSession.php';
            }

            if(!class_exists('Raautil_TransactionException', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/TransactionException.php';
            }
            
            if(!class_exists('Raautil_TransactionHandler', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/TransactionHandler.php';
            }

            if(!class_exists('Raautil_AccountDisclosure', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/AccountDisclosure.php';
            }

            if(!class_exists('Raautil_QRCodeGenerator', FALSE)) {
                require_once $LIBRARY_DIR . '/raautil/QRCodeGenerator.php';
            }
            
        } // End init
        
	/**
	 * Adds a directory, and optional its subdirectories to the class autoloader directories.
	 *
	 * @param string $dir
	 *	The directory to add. The directory is only added if it is an existing directory
	 *	and it is not yet contained in the list of autoload directories.
	 *
	 * @param boolean $includeSubdirs
	 *	Use TRUE if you want to include also the subdirectories of $dir, FALSE otherewise.
	 *	Default is FALSE
	 */
	public static function addAutoloadDirectory($dir = '', $includeSubdirs = FALSE) {
		$dir = isset($dir) ? (is_string($dir) ? trim($dir) : '') : '';
		$includeSubdirs =
				isset($includeSubdirs) ? (is_bool($includeSubdirs) ? $includeSubdirs : FALSE) : FALSE;
		if($dir != '') {
			$dir = rtrim($dir, "/\\");
			if(is_dir($dir)) {
				if(!in_array($dir, self::$autoloadDirs)) {
					self::$autoloadDirs[] = $dir;
				}

				if($includeSubdirs == TRUE) {
					$fileList = scandir($dir);
					if(isset($fileList)) {
						if(is_array($fileList)) {
							foreach($fileList as $subdir) {
								if ($subdir === '.' || $subdir === '..') {
									continue;
								}

								$subdir = $dir . DIRECTORY_SEPARATOR . $subdir;
								if(is_dir($subdir)) {
									self::addAutoloadDirectory($subdir, TRUE);
								}
							}
						}
					}
				}
			}
		}
	} // End addAutoloadDirectory
        
        
	/**
	 * Callable function for spl_autoload_register(). This function is called
	 * automatically if a class or interface will be loaded but the scriptfile
	 * containing the class- or interface description is not yet included.
	 *
	 * The script file names that are searched for depend on the name of the class
	 * or interface. If the classname is something like 'MyClass', then scripts
	 * are searched with the filenames '<dir>/MyClass.php', '<dir>/MyClass.class.php'
	 * or '<dir>/MyClass.class.inc'.
	 *
	 * If the classname is something like 'My_Awsome_Class', then scripts are
	 * searched with the filenames '<dir>/my/awsome/Class.php',
	 * '<dir>/my/awsome/Class.class.php' or '<dir>/my/awsome/Class.class.inc'.
	 *
	 * This function will include all matching scriptfiles in the list or search
	 * directories.
	 *
	 * @param string $name
	 *	Name of the class or interface
	 *
	 * @return boolean
	 */
	public static function autoloadClassOrInterface($name = '') {
		$name = isset($name) ? (is_string($name) ? trim($name) : '') : '';
		if($name == '')
			return FALSE;

		// If a classname is for instance "Foo_Bar_MyClass", the variable
		// $packageClassName will be 'foo/bar/MyClass'.
		// If a classname is for instance "MyClass", the variable
		// $packageClassName will be 'MyClass'.
		$exploded = explode('_', $name);
		$count = count($exploded);
		$packageClassName = '';
		for($i = 0; $i < $count; $i++) {
			$part = $exploded[$i];
			if(($i + 1) < $count) {
				$part = strtolower($part);
			}
			if($packageClassName == '')
				$packageClassName = $part;
			else
				$packageClassName = $packageClassName . '/' . $part;
		}

		$found = FALSE;
		foreach(self::$autoloadDirs as $dir) {
			// Check if the filename = $dir/$packageClassName.php
			$filename = $dir . '/' . $packageClassName . '.php';
			if(is_file($filename)) {
				$found = TRUE;
				require_once $filename;
			}

			// Check if the filename = $dir/$packageClassName.class.php
			$filename = $dir . '/' . $packageClassName . '.class.php';
			if(is_file($filename)) {
				$found = TRUE;
				require_once $filename;
			}

			// Check if the filename = $dir/$packageClassName.class.inc
			$filename = $dir . '/' . $packageClassName . '.class.inc';
			if(is_file($filename)) {
				$found = TRUE;
				require_once $filename;
			}
		}

		return $found;
	} // End autoloadClassOrInterface
        
        
	/**
	 * Sets the reference of an instance or subclass of Raa_Logger.
	 *
	 * @param Raa_Logger $logger
	 *   The reference of an instance or subclass of Raa_Logger
	 */
	public static function setLogger($logger = null) {
		if(isset($logger)) {
			if($logger instanceof Raa_Logger) {
				self::$logger = $logger;
			}
		}
	} // End setLogger
        
	/**
	 * Returns the reference of an instance or subclass of Raa_Logger
	 * that was previously set with setLogger($logger). If no class was set before,
	 * the default implementation Raa_Logger will be returned.
	 *
	 * @return Raa_Logger
	 *   The reference of an instance or subclass of Raa_Logger.
	 *   If no class was set before, the  default implementation Raa_Logger will
	 *   be returned
	 */
	public static function getLogger() {
		if(isset(self::$logger)) {
			return self::$logger;
		} else {
			self::$logger = new Raa_Logger();
			return self::$logger;
		}
	} // End getLogger
        
	/**
	 * Writes some logging output.
	 *
	 * @param string $type
	 *	The logging type.
	 *	This can be 'debug', 'info', 'warning' or 'warning'
	 *
	 * @param string $title
	 *	The title of the log output
	 *
	 * @param string $output
	 *	The content that should be written
	 */
	public static function log($type = 'debug', $title = '', $output = '') {
		$type = isset($type) ? (is_string($type) ? trim($type) : '') : '';
		if($type != 'debug' &&
		   $type != 'info' &&
		   $type != 'warning' &&
		   $type != 'error') {
			$type = 'debug';
		}
		$title = isset($title) ? (is_string($title) ? trim($title) : '') : '';
		$output = isset($output) ? (is_string($output) ? trim($output) : '') : '';
		$logger = self::getLogger();
		$logger->log($type, $title, $output);
	} // End log
        
        
	public static function append($result, $add, $sz = 0, $filler = 0) {
		$addLen = 0;
		if ($add != null) {
			$addLen = strlen($add);
			if ($addLen > 0) {
				$result = $result . $add;
			}
		}
		while ($addLen < $sz) {
			$result = $result . pack('C', $filler);
			++$addLen;
		}
		return $result;
	} // End append
        
}
TerminalSdkLibrary::init();
