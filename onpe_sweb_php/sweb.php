<?php

  include('db.php');
  BaseDatos('localhost','root','','onpe');

  $parametros = $_SERVER['REQUEST_URI'];
  $parametros = str_replace("%20", " ", $parametros);
  $parametros = explode("/",$parametros);
  $parametros = array_slice($parametros,2);
  
  $longitud = count( $parametros );

  if ( $longitud > 1 )
    if ( $parametros[0] == "participacion" ) getParticipacion();
    else if ( $parametros[0] == "actas" && $parametros[1] == "ubigeo" ) getActasUbigeo();
    else if ( $parametros[0] == "actas" && $parametros[1] == "numero" && $longitud == 3 ) getActasNumero();

  function getParticipacion() {
    global $_SQL;
    global $parametros;
    global $longitud;

    $bDPD = $parametros[1] == "Nacional" || $parametros[1] == "Extranjero";

    if ( $longitud == 2 )
      $_SQL = $parametros[1] == "Nacional" ? "call sp_getVotos(1,25)" : ( $parametros[1] == "Extranjero" ? "call sp_getVotos(26,30)" : "" );
    elseif ( $longitud == 3 ) $_SQL = $bDPD ? "call sp_getVotosDepartamento('$parametros[2]')" : "";
    elseif ( $longitud == 4 ) $_SQL = $bDPD && isDPD( $parametros[2], "Departamento" ) ? "call sp_getVotosProvincia('$parametros[3]')" : "";

    getRegistros();
  }

  function getActasUbigeo() {
    global $_SQL;
    global $parametros;
    global $longitud;

  switch ( $longitud ) {
      case 3 : if ( $parametros[2] == "Peru" ) $_SQL =  "call sp_getDepartamentos(1,25)";
                else $_SQL = "call sp_getDepartamentos(26,30)";
                break;
      case 4 : $_SQL = "call sp_getProvinciasByDepartamento('$parametros[3]')"; break;
      case 5 : $_SQL = "call sp_getDistritosByProvincia('$parametros[4]')"; break;
      case 6 : $_SQL = "call sp_getLocalesVotacionByDistrito('$parametros[4]','$parametros[5]')"; break;
      case 7 : $_SQL = "call sp_getGruposVotacionByProvinciaDistritoLocal('$parametros[4]','$parametros[5]','$parametros[6]')"; break;
      case 8 : $_SQL = "call sp_getGrupoVotacionByProvinciaDistritoLocalGrupo('$parametros[3]','$parametros[4]','$parametros[5]','$parametros[6]','$parametros[7]')"; break;
    }

    getRegistros();
  }

  function getActasNumero() {
    global $_SQL;
    global $parametros;

    $_SQL = "call sp_getGrupoVotacion('$parametros[2]')";
    getRegistros();
  }

  function isDPD( $detalle, $DPD ) {
    global $_SQL;

    if ( $DPD == "Departamento" ) $_SQL = "call sp_isDepartamento('$detalle')";
    else if ( $DPD == "Provincia" ) $_SQL = "call sp_isProvincia('$detalle')";
    return getCampo();
  }

?>