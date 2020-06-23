<?php
namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use MyApp\QUERY\FormKonfigurasi;
use MyApp\QUERY\ActKonfigurasi;

class Socket implements MessageComponentInterface {

    protected $clients;
    protected $id2conn;

    public function __construct() {
      $this->clients = new \SplObjectStorage;
      $this->id2conn = array();
    }

    public function onOpen(ConnectionInterface $conn) {

      $this->clients->attach($conn);
      $this->id2conn[$conn->resourceId] = $conn;

      echo "New connection! ({$conn->resourceId})\n";

      $querystring = $conn->httpRequest->getUri()->getQuery();
      parse_str($querystring,$queryarray);

      $sending           = array();
      $actKonfigurasi    = new ActKonfigurasi();
      $resValidation     = $actKonfigurasi->validationToken( $queryarray['token'] );

      if ( $resValidation ) {

        $filterResValidation    = $resValidation;

        unset( $filterResValidation->created_at, $filterResValidation->_key, $filterResValidation->token );

        $sending['client']      = $filterResValidation;

        $ret                    = json_encode( $sending );

        $conn->send( $ret );

      } else {
        $this->id2conn[$conn->resourceId]->close();
      }

    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $querystring = $from->httpRequest->getUri()->getQuery();
        parse_str($querystring,$queryarray);

        $sending           = array();
        $statusRet         = false;

        $actKonfigurasi    = new ActKonfigurasi();

        $resValidation     = $actKonfigurasi->validationToken( $queryarray['token'] );

        if ( $resValidation ) {

          $proses            = json_decode( $msg );

          $type              = $proses->type;

          $sending           = array();

          switch ($type) {

            case 'cari_kabupaten':

                $sending['koke']          = $actKonfigurasi->cariKoKe( strtoupper( $proses->msg ) );
                $statusRet                = $sending['koke'] ? true : false;

              break;

            case 'form_instansi':

                $saveForm                 = $actKonfigurasi->saveFormInstansi( $resValidation, $proses );
                $statusRet                = $saveForm ? true : false;

                if ( $saveForm ) {

                  $act                      = new ActKonfigurasi();
                  $sending['client']        = $act->validationToken( $queryarray['token'] );

                  unset( $sending['client']->created_at, $sending['client']->_key, $sending['client']->token );

                }

              break;

            default:
                // $sending['info']          = "Invalid, Type command.";
              break;
          }

          $sending['status']        = $statusRet;
          $ret                      = json_encode( $sending );

          $from->send( $ret );

        } else {
          $this->id2conn[$from->resourceId]->close();
        }

    }

    public function onClose(ConnectionInterface $conn) {
      // The connection is closed, remove it, as we can no longer send it messages
      // $this->clients->detach($conn);
      // echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
      // echo "An error has occurred: {$e->getMessage()}\n";
      // $conn->close();
    }

}
