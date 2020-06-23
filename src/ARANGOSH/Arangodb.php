<?php

namespace MyApp\ARANGOSH;

use ArangoDBClient\Connection as Connection;
use ArangoDBClient\Collection as Collection;
use ArangoDBClient\Document as Document;
use ArangoDBClient\Statement as Statement;
use ArangoDBClient\CollectionHandler as CollectionHandler;
use ArangoDBClient\DocumentHandler as DocumentHandler;
use ArangoDBClient\ConnectionOptions as ConnectionOptions;
use ArangoDBClient\UpdatePolicy as UpdatePolicy;

class Arangodb {

  private $connectionOptions;
  private $ArangoCon;
  private $ArangoCollHandler;
  private $handler;
  private $getArrState;

  public function __construct( $q ){
    $this->connectionOptions = [
        ConnectionOptions::OPTION_DATABASE => '',               // database name
        // normal unencrypted connection via TCP/IP
        ConnectionOptions::OPTION_ENDPOINT => '',  // endpoint to connect to

        // // to use failover (requires ArangoDB 3.3 and the database running in active/passive failover mode)
        // // it is possible to specify an array of endpoints as follows:
        // ConnectionOptions::OPTION_ENDPOINT    => [ 'tcp://localhost:8531', 'tcp://localhost:8532' ]

        // // to use memcached for caching the currently active leader (to spare a few connection attempts
        // // to followers), it is possible to install the Memcached module for PHP and set the following options:
        // // memcached persistent id (will be passed to Memcached::__construct)
        // ConnectionOptions::OPTION_MEMCACHED_PERSISTENT_ID => 'arangodb-php-pool',
        // // memcached servers to connect to (will be passed to Memcached::addServers)
        // ConnectionOptions::OPTION_MEMCACHED_SERVERS       => [ [ '127.0.0.1', 11211 ] ],
        // // memcached options (will be passed to Memcached::setOptions)
        // ConnectionOptions::OPTION_MEMCACHED_OPTIONS       => [ ],
        // // key to store the current endpoints array under
        // ConnectionOptions::OPTION_MEMCACHED_ENDPOINTS_KEY => 'arangodb-php-endpoints'
        // // time-to-live for the endpoints array stored in memcached
        // ConnectionOptions::OPTION_MEMCACHED_TTL           => 600
        // // connection via SSL
        // ConnectionOptions::OPTION_ENDPOINT        => 'ssl://localhost:8529',  // SSL endpoint to connect to
        // ConnectionOptions::OPTION_VERIFY_CERT     => false,                   // SSL certificate validation
        // ConnectionOptions::OPTION_ALLOW_SELF_SIGNED => true,                  // allow self-signed certificates
        // ConnectionOptions::OPTION_CIPHERS         => 'DEFAULT',               // https://www.openssl.org/docs/manmaster/apps/ciphers.html
        // // connection via UNIX domain socket
        // ConnectionOptions::OPTION_ENDPOINT        => 'unix:///tmp/arangodb.sock',  // UNIX domain socket
        ConnectionOptions::OPTION_CONNECTION  => 'Close',            // can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
        ConnectionOptions::OPTION_AUTH_TYPE   => 'Basic',                 // use basic authorization
        // authentication parameters (note: must also start server with option `--server.disable-authentication false`)
        ConnectionOptions::OPTION_AUTH_USER   => '',                  // user for basic authorization
        ConnectionOptions::OPTION_AUTH_PASSWD => '',                      // password for basic authorization
        ConnectionOptions::OPTION_TIMEOUT       => 3,                      // timeout in seconds
        // ConnectionOptions::OPTION_TRACE         => $traceFunc,              // tracer function, can be used for debugging
        ConnectionOptions::OPTION_CREATE        => true,                   // do not create unknown collections automatically
        ConnectionOptions::OPTION_UPDATE_POLICY => UpdatePolicy::LAST,      // last update wins
    ];
    $this->ArangoCon              = new Connection( $this->connectionOptions );
    $this->ArangoCollHandler      = new CollectionHandler( $this->ArangoCon );
    $this->handler                = new DocumentHandler( $this->ArangoCon );
    $this->getArrState            = Array( "query" => ( $q ? $q : [] ), "count" => true, "batchSize" => 1000, "sanitize" => true );

  }

  public function has(){

    $listargs 		= func_get_args();

    return $this->handler->has( $listargs[0], $listargs[1] );

  }

  public function getById(){

    $listargs 		= func_get_args();

    return $this->handler->getById( $listargs[0], $listargs[1] );

  }

  public function builder(){

    $statement            = new Statement( $this->ArangoCon, $this->getArrState );
    $resQ                 = $statement->execute();
    $getRes               = $resQ->getAll();

    return $getRes;

  }

}
