<?php

namespace MyApp\QUERY;

use MyApp\ARANGOSH\Arangodb;

class QueryBuilder  {

  public function arangosh(){
    return new Arangodb("");
  }

  public function saveFormInstansi(){

    $listargs 		= func_get_args();

    $query        = 'UPDATE "'.$listargs[0].'" WITH {
                          "nama_lengkap": "'.$listargs[2].'",
                          "instansi": "'.$listargs[1].'",
                          "ijin_instansi": "'.$listargs[3].'",
                          "penanggung_jawab": "'.$listargs[4].'",
                          "alamat": "'.$listargs[5].'",
                          "provinsi": "'.$listargs[6].'",
                          "kabupaten": "'.$listargs[7].'",
                          "kecamatan": "'.$listargs[8].'"
                        } IN rs RETURN NEW';

    return new Arangodb( $query );

  }

  public function cariKoKe(){

    $listargs 		= func_get_args();

    $query        = 'FOR doc IN field
                      FILTER
                        doc.name like "%'.$listargs[0].'%" AND doc.jenis == "kecamatan" OR
                        doc.name like "%'.$listargs[0].'%" AND doc.jenis == "kabupaten"
                    RETURN doc';

    return new Arangodb( $query );

  }

  public function findProvinsi(){

    $listargs 		= func_get_args();

    $query        = 'FOR doc IN field
                      FILTER
                        doc.idx == "'.$listargs[0].'" AND doc.jenis == "provinsi"
                    RETURN doc';

    return new Arangodb($query);

  }

  public function findKecByKab(){

    $listargs 		= func_get_args();

    $query        = 'FOR doc IN field
                      FILTER
                        doc.kabupaten == "'.$listargs[0].'" AND doc.jenis == "kecamatan"
                    RETURN doc';

    return new Arangodb($query);

  }

  public function findKabupaten() {

  	$listargs 		= func_get_args();

    $query        = 'FOR doc IN field
                      FILTER
                        doc.idx == "'.$listargs[0].'" AND doc.jenis == "kabupaten"
                    RETURN doc';

    return new Arangodb($query);

  }

}
