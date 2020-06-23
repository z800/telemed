<?php

namespace MyApp\QUERY;

use MyApp\QUERY\QueryBuilder;
use MyApp\PARSING\Filtering;

class ActKonfigurasi  {

  public function saveFormInstansi(){

      $listargs 		      = func_get_args();
      $output             = false;

      $resValidation      = $listargs[0];
      $formData           = $listargs[1];

      if ( $resValidation && $formData ) {

        $filter             = new Filtering;
        $builder            = new QueryBuilder;

        $jenisInstansi      = $filter->getReplace( "num", $formData->jenis_instansi );
        $namaInstansi       = $filter->getReplace( "address", $formData->nama_instansi );
        $ijinInstansi       = $filter->getReplace( "address", $formData->ijin_instansi );
        $penanggungJawab    = $filter->getReplace( "address", $formData->penanggung_jawab );
        $alamat             = $filter->getReplace( "address", $formData->alamat );
        $prokake            = isset( $formData->koke ) ? explode( ",", $formData->koke ) : false;

        $provinsi           = $prokake ? $filter->getReplace( "num", $prokake[0] ) : false;
        $kabupaten          = $prokake ? $filter->getReplace( "num", $prokake[1] ) : false;
        $kecamatan          = $prokake ? $filter->getReplace( "num", $prokake[2] ) : false;

        $validationProv     = $builder->arangosh()->has( 'field', $provinsi ) ? $provinsi : "";
        $validationKab      = $builder->arangosh()->has( 'field', $kabupaten ) ? $kabupaten : "";
        $validationKec      = $builder->arangosh()->has( 'field', $kecamatan ) ? $kecamatan : "";

        $save               = $builder->saveFormInstansi( $resValidation->_key, $jenisInstansi, $namaInstansi, $ijinInstansi, $penanggungJawab, $alamat, $validationProv, $validationKab, $validationKec )->builder();

        $output             = json_decode( $save[0] );

      }

      return $output;

  }

  public function validationToken() {

    $listargs 		= func_get_args();
    $output       = false;

    $data         = explode( ":", $listargs[0] );

    if ( count( $data ) == 2 ) {

      $token      = $data[0];
      $id         = $data[1];

      $builder    = new QueryBuilder();

      $check      = $builder->arangosh()->has( 'rs', $id );

      if ( $check ) {

        $dataCheck      = $builder->arangosh()->getById( 'rs', $id );
        $decDataCheck   = json_decode( $dataCheck );

        if ( $token === $decDataCheck->token ) {

          $validationProv   = $builder->arangosh()->has( 'field', $decDataCheck->provinsi );
          $validationKab    = $builder->arangosh()->has( 'field', $decDataCheck->kabupaten );
          $validationKec    = $builder->arangosh()->has( 'field', $decDataCheck->kecamatan );

          if ( $validationProv && $validationKab && $validationKec ) {

            $prov       = json_decode( $builder->arangosh()->getById( 'field', $decDataCheck->provinsi ) );
            $kab        = json_decode( $builder->arangosh()->getById( 'field', $decDataCheck->kabupaten ) );
            $kec        = json_decode( $builder->arangosh()->getById( 'field', $decDataCheck->kecamatan ) );

            $decDataCheck->text     = $prov->name.", ".$kab->name.", ".$kec->name;

          }

          $output       = $decDataCheck;

        }

      }

    }

    return $output;

  }

  public function cariKoKe(){

    $listargs 		= func_get_args();
    $output       = false;

    $builder      = new QueryBuilder();

    $firstFilter  = $builder->cariKoKe( $listargs[0] )->builder();

    $k            = 0;

    if ( $firstFilter ) {

      for ( $i=0;$i<count( $firstFilter );$i++ ) {

        $decRes           = json_decode( $firstFilter[$i] );

        $findKabKec       = $decRes->jenis === "kabupaten" ? $builder->findKecByKab( $decRes->idx )->builder() : $builder->findKabupaten( $decRes->kabupaten )->builder();

        for ( $z=0;$z<count( $findKabKec );$z++ ) {

          $decKabKec          = json_decode( $findKabKec[$z] );

          if ( $decKabKec->jenis === "kecamatan" ) {

            $kabupaten        = $builder->findKabupaten( $decKabKec->kabupaten )->builder();
            $decKab           = json_decode( $kabupaten[0] );

          }

          $findProvinsi                  = $builder->findProvinsi( $decKabKec->provinsi )->builder();
          $decProv                       = json_decode( $findProvinsi[0] );

          $output[$k]['provinsi']        = $decProv->_key;
          $output[$k]['kabupaten']       = $decKabKec->jenis === "kabupaten" ? $decKabKec->_key : $decKab->_key;
          $output[$k]['kecamatan']       = $decKabKec->jenis === "kabupaten" ? $decRes->_key : $decKabKec->_key;

          $output[$k]['text']            = $decProv->name.", ".( $decKabKec->jenis === "kabupaten" ? $decKabKec->name : $decKab->name ).", ".( $decKabKec->jenis === "kabupaten" ? $decRes->name : $decKabKec->name );

          $k++;

        }

      }

    }

    return $output;

  }

}
