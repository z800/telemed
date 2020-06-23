<?php

namespace MyApp\PARSING;

class Filtering {

    public function getReplace(){

  		$numargs 			= func_num_args();
  		$listargs 		= func_get_args();

  		switch ( preg_replace('/[^a-zA-Z]/', "", $listargs[0]) ) {

  			case "num":
  				$zxc 		= preg_replace('/[^0-9]/', "", $listargs[1]);
  			break;

  			case "nummin":
  				$zxc 		= preg_replace('/[^0-9\-]/', "", $listargs[1]);
  			break;

  			case "numin":
  				$zxc 		= preg_replace('/[^0-9a-zA-Z-]/', "", $listargs[1]);
  			break;

  			case "numx":
  				$zxc 		= preg_replace('/[^0-9a-zA-Z:,]/', "", $listargs[1]);
  			break;

  			case "lett":
  				$zxc 		= preg_replace('/[^a-zA-Z]/', "", $listargs[1]);
  			break;

  			case "lettx":
  				$zxc 		= preg_replace('/[^a-zA-Z. ]/', "", $listargs[1]);
  			break;

  			case "numle":
  				$zxc 		= preg_replace('/[^0-9a-zA-Z]/', "", $listargs[1]);
  			break;

  			case "numledot":
  				$zxc 		= preg_replace('/[^0-9a-zA-Z.]/', "", $listargs[1]);
  			break;

  			case "numlespasi":
  				$zxc 		= preg_replace('/[^0-9a-zA-Z ]/', "", $listargs[1]);
  			break;

  			case "numlespa":
  				$zxc 		= preg_replace('/[^0-9a-zA-Z_]/', "", $listargs[1]);
  			break;

  			case "numcom":
  				$zxc 		= preg_replace('/[^0-9,]/', "", $listargs[1]);
  			break;

  			case "numdot":
  				$zxc 		= preg_replace('/[^0-9\.]/', "", $listargs[1]);
  			break;

  			case "latlong":
  				$zxc 		= preg_replace('/[^0-9a-zA-Z\'.-]/', '', $listargs[1]);
  			break;

  			case "address":
  				$zxc 		= preg_replace('/[^0-9a-zA-Z \.,-:]/', "", $listargs[1]);
  			break;

  			default:
  				$zxc 		= false;

  		}
  		return $zxc;

    }

}
