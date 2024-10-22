<?php
/**
 * MySQL Storage
 *
 * PHP Version 5.3
 *
 * @category  Payment
 * @package   KlarnaAPI
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */

/**
 * Include the {@link PCStorage} interface.
 */
require_once dirname(__FILE__).'/storage.intf.php';

/**
 * MySQL storage class for KlarnaPClass
 *
 * This class is an MySQL implementation of the PCStorage interface.<br>
 * Config field pcURI needs to match format:
 * user:passwd@addr:port/dbName.dbTable<br>
 * Port can be omitted.<br>
 *
 * <b>Acceptable characters</b>:<br>
 * Username: [A-Za-z0-9_]<br>
 * Password: [A-Za-z0-9_]<br>
 * Address:  [A-Za-z0-9_.]<br>
 * Port:     [0-9]<br>
 * DB name:  [A-Za-z0-9_]<br>
 * DB table: [A-Za-z0-9_]<br>
 *
 * To allow for more special characters, and to avoid having<br>
 * a regular expression that is too hard to understand, you can<br>
 * use an associative array:<br>
 * <code>
 * array(
 *   "user" => "myuser",
 *   "passwd" => "mypass",
 *   "dsn" => "localhost",
 *   "db" => "mydatabase",
 *   "table" => "mytable"
 * );
 * </code>
 *
 * @category  Payment
 * @package   KlarnaAPI
 * @author    MS Dev <ms.modules@klarna.com>
 * @copyright 2012 Klarna AB (http://klarna.com)
 * @license   http://opensource.org/licenses/BSD-2-Clause BSD-2
 * @link      http://integration.klarna.com/
 */
class MySQLStorage extends PCStorage
{

    /**
     * Database name.
     *
     * @var string
     */
    protected $dbName;

    /**
     * Database table.
     *
     * @var string
     */
    protected $dbTable;

    /**
     * Database address.
     *
     * @var string
     */
    protected $addr;

    /**
     * Database username.
     *
     * @var string
     */
    protected $user;

    /**
     * Database password.
     *
     * @var string
     */
    protected $passwd;

    /**
     * MySQL DB link resource.
     *
     * @var resource
     */
    protected $link;

    /**
     * return the name of the storage type
     *
     * @return string
     */
    public function getName()
    {
        return "mysql";
    }

    /**
     * Connects to the DB and checks if DB and table exists.
     *
     * @throws KlarnaException
     * @return void
     */
    protected function connect()
    {
	    $port   = isset(explode(':', $this->addr)[1]) && is_numeric(explode(':', $this->addr)[1]) ? (int)explode(':', $this->addr)[1] : null;
	    $socket = isset(explode(':', $this->addr)[1]) && !is_numeric(explode(':', $this->addr)[1]) ? explode(':', $this->addr)[1] : null;

	    $this->link = mysqli_connect(explode(':', $this->addr)[0],  $this->user,  $this->passwd, $this->dbName, $port, $socket);
        if ($this->link === false) {
            throw new Klarna_DatabaseException(
                'Failed to connect to database! ('.mysqli_error($this->link).')'
            );
        }

        if (!mysqli_query(
            $this->link
        ,
            "CREATE DATABASE IF NOT EXISTS `{$this->dbName}`")
        ) {
            throw new Klarna_DatabaseException(
                'Failed to create! ('.mysqli_error($this->link).')'
            );
        }

        $create = mysqli_query( $this->link
        ,
            "CREATE TABLE IF NOT EXISTS `{$this->dbName}`.`{$this->dbTable}` (
                `eid` int(10) unsigned NOT NULL,
                `id` int(10) unsigned NOT NULL,
                `type` tinyint(4) NOT NULL,
                `description` varchar(255) NOT NULL,
                `months` int(11) NOT NULL,
                `interestrate` decimal(11,2) NOT NULL,
                `invoicefee` decimal(11,2) NOT NULL,
                `startfee` decimal(11,2) NOT NULL,
                `minamount` decimal(11,2) NOT NULL,
                `country` int(11) NOT NULL,
                `expire` int(11) NOT NULL,
                KEY `id` (`id`)
            )");

        if (!$create) {
            throw new Klarna_DatabaseException(
                'Table not existing, failed to create! ('.mysqli_error($this->link).')'
            );
        }
    }

    /**
     * Splits the URI in format: user:passwd@addr/dbName.dbTable<br>
     *
     * To allow for more special characters, and to avoid having<br>
     * a regular expression that is too hard to understand, you can<br>
     * use an associative array:<br>
     * <code>
     * array(
     *   "user" => "myuser",
     *   "passwd" => "mypass",
     *   "dsn" => "localhost",
     *   "db" => "mydatabase",
     *   "table" => "mytable"
     * );
     * </code>
     *
     * @param string|array $uri Specified URI to database and table.
     *
     * @throws KlarnaException
     * @return void
     */
    protected function splitURI($uri)
    {
        if (is_array($uri)) {
            $this->user = $uri['user'];
            $this->passwd = $uri['passwd'];
            $this->addr = $uri['dsn'];
            $this->dbName = $uri['db'];
            $this->dbTable = $uri['table'];
        } else if (preg_match(
            '/^([\w-]+):([\w-]+)@([\w\.-]+|[\w\.-]+:[\d]+)\/([\w-]+).([\w-]+)$/',
            $uri,
            $arr
        ) === 1
        ) {
            /*
              [0] => user:passwd@addr/dbName.dbTable
              [1] => user
              [2] => passwd
              [3] => addr
              [4] => dbName
              [5] => dbTable
            */
            if (count($arr) != 6) {
                throw new Klarna_DatabaseException(
                    'URI is invalid! Missing field or invalid characters used!'
                );
            }

            $this->user = $arr[1];
            $this->passwd = $arr[2];
            $this->addr = $arr[3];
            $this->dbName = $arr[4];
            $this->dbTable = $arr[5];
        } else {
            throw new Klarna_DatabaseException(
                'URI to MySQL is not valid! ( user:passwd@addr/dbName.dbTable )'
            );
        }
    }

    /**
     * Load pclasses
     *
     * @param string $uri pclass uri
     *
     * @throws KlarnaException
     * @return void
     */
    public function load($uri)
    {
        $this->splitURI($uri);
        $this->connect();
        $result = mysqli_query(
            $this->link
        ,
            "SELECT * FROM `{$this->dbName}`.`{$this->dbTable}`");
        if ($result === false) {
            throw new Klarna_DatabaseException(
                'SELECT query failed! ('.mysqli_error($this->link).')'
            );
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $this->addPClass(new KlarnaPClass($row));
        }
    }

    /**
     * Save pclasses to database
     *
     * @param string $uri pclass uri
     *
     * @throws KlarnaException
     * @return void
     */
    public function save($uri)
    {
        $this->splitURI($uri);

        $this->connect();
        if (!is_array($this->pclasses) || count($this->pclasses) == 0) {
            return;
        }

        foreach ($this->pclasses as $pclasses) {
            foreach ($pclasses as $pclass) {
                //Remove the pclass if it exists.
                mysqli_query($this->link,
                    "DELETE FROM `{$this->dbName}`.`{$this->dbTable}`
                     WHERE `id` = '{$pclass->getId()}'
                     AND `eid` = '{$pclass->getEid()}'"
                );

                //Insert it again.
                $insertQuery =  "INSERT INTO `{$this->dbName}`.`{$this->dbTable}`
                       (`eid`,
                        `id`,
                        `type`,
                        `description`,
                        `months`,
                        `interestrate`,
                        `invoicefee`,
                        `startfee`,
                        `minamount`,
                        `country`,
                        `expire`
                        )
                     VALUES
                       ('{$pclass->getEid()}',
                        '{$pclass->getId()}',
                        '{$pclass->getType()}',
                        '{$pclass->getDescription()}',
                        '{$pclass->getMonths()}',
                        '{$pclass->getInterestRate()}',
                        '{$pclass->getInvoiceFee()}',
                        '{$pclass->getStartFee()}',
                        '{$pclass->getMinAmount()}',
                        '{$pclass->getCountry()}',
                        ':expire')";
                $expireTimestamp = (int)$pclass->getExpire();
                $expireTimestamp = $expireTimestamp == 0 ? strtotime('2037-12-31') : $expireTimestamp;
                $insertQuery = strtr($insertQuery, array(':expire' => $expireTimestamp));
                $result = mysqli_query( $this->link, $insertQuery);
                if ($result === false) {
                    throw new Klarna_DatabaseException(
                        'INSERT INTO query failed! ('.mysqli_error($this->link).')'."\n".$insertQuery
                    );
                }
            }
        }
    }

    /**
     * Clear the pclasses
     *
     * @param string $uri pclass uri
     *
     * @throws KlarnaException
     * @return void
     */
    public function clear($uri)
    {
        try {
            $this->splitURI($uri);
            unset($this->pclasses);
            $this->connect();

            mysqli_query(
                $this->link
            ,
                "DELETE FROM `{$this->dbName}`.`{$this->dbTable}`");
        } catch(Exception $e) {
            throw new Klarna_DatabaseException(
                $e->getMessage(), $e->getCode()
            );
        }
    }
}
