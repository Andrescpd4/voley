<?php
function cifrar_campos($data,$campo){
	$format = array();
	foreach ($data as $key => $rw) {
		$rw['id_key'] = urlsafe_b64encode($rw[$campo]);
		$format[] = $rw;
	}
	return $format;
}
function llenar_combo_pai_entidad($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['ano']) {
		$s.=" AND ano='".$filtros["ano"]."' ";
	}

	if ($filtros['visible']) {
		$s.=" AND visible='".$filtros["visible"]."' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM pai_entidad $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;

}


function get_proyectos($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['tipo_proyecto_id']) {
		$s.=" AND p.tipo_proyecto_id='".$filtros["tipo_proyecto_id"]."' ";
	}

	if ($filtros['pai_entidad_id']) {
		$s.=" AND p.pai_entidad_id='".$filtros["pai_entidad_id"]."' ";
	}

	if ($filtros['visible']) {
		$s.=" AND p.visible='".$filtros["visible"]."' ";
	}

	if ($filtros['id_tipo_programacion']) {
		$s.=" AND p.id_tipo_programacion='".$filtros["id_tipo_programacion"]."' ";
	}

	$sql = "SELECT p.*, t.nombre AS tipo_proyecto, tp.nombre AS tipo_programacion, tp.key 
	FROM proyectos p, tipo_proyecto t, tipo_programacion tp
	WHERE p.tipo_proyecto_id=t.id
	AND p.id_tipo_programacion=tp.id $s";

	if ($local==false) {
	   $result = cifrar_campos ( $db->select_all($sql), 'id' );
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}

function get_tipo_proyecto($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['nombre']) {
		$s.=" AND nombre LIKE '%".$filtros["nombre"]."%' ";
	}


	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM tipo_proyecto $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}





function get_tipo_programacion($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['nombre']) {
		$s.=" AND nombre LIKE '%".$filtros["nombre"]."%' ";
	}

	if ($filtros['key']) {
		$s.=" AND key='".$filtros["key"]."' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM tipo_programacion $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}


function get_si_no($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$sql = "SELECT * FROM visible ";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}

function get_seccion($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['nombre']) {
		$s.=" AND nombre LIKE '%".$filtros["nombre"]."%' ";
	}

	if ($filtros['key']) {
		$s.=" AND key='".$filtros["key"]."' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM seccion $s ";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}



function get_tipo_rubro($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['nombre']) {
		$s.=" AND nombre LIKE '%".$filtros["nombre"]."%' ";
	}

	if ($filtros['key']) {
		$s.=" AND key='".$filtros["key"]."' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM tipo_rubro $s ";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}

function get_productos($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['ano']) {
		$s.=" AND ano='".$filtros["ano"]."' ";
	}

	if ($filtros['visible']) {
		$s.=" AND visible='".$filtros["visible"]."' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM pai_entidad $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}


function get_obtetivos_generales($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['proyectos_id']) {
		$s.=" AND proyectos_id='".$filtros["proyectos_id"]."' ";
	}

	if ($filtros['visible']) {
		$s.=" AND visible='".$filtros["visible"]."' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM objetivos_generales $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}



function get_obtetivos_especificos($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['objetivos_generales_id']) {
		$s.=" AND objetivos_generales_id='".$filtros["objetivos_generales_id"]."' ";
	}

	if ($filtros['visible']) {
		$s.=" AND visible='".$filtros["visible"]."' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM objetivos_especificos $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}



function get_fuentes($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['codigo']) {
		$s.=" AND codigo='".$filtros["codigo"]."' ";
	}

	if ($filtros['nombre']) {
		$s.=" AND nombre LIKE '%".$filtros["nombre"]."%' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM fuentes $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}




function get_catalogo_indicadores($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['id_pai']) {
		$s.=" AND id_pai='".$filtros["id_pai"]."' ";
	}

	if ($filtros['nombre']) {
		$s.=" AND nombre LIKE '%".$filtros["nombre"]."%' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM catalogo_indicadores $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}


function get_recursos($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";
	if ($filtros['codigo']) {
		$s.=" AND codigo='".$filtros["codigo"]."' ";
	}

	if ($filtros['fuentes_id']) {
		$s.=" AND fuentes_id='".$filtros["fuentes_id"]."' ";
	}

	if ($filtros['nombre']) {
		$s.=" AND nombre LIKE '%".$filtros["nombre"]."%' ";
	}

	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM recursos $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}



function get_sexo($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";


	if ($filtros['nombre']) {
		$s.=" AND nombre LIKE '%".$filtros["nombre"]."%' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM sexo $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}


function get_tipo_documento($local=false, $blanco=true, $predeterminado='0', $filtros = array() ){
	global $db;
	$s="";

	if ($filtros['nombre']) {
		$s.=" AND nombre LIKE '%".$filtros["nombre"]."%' ";
	}
	if ($s != "") {
		$s = trim($s);
		$s = trim($s,'AND');
		$s = " WHERE ".$s;

	}

	$sql = "SELECT * FROM tipo_documento $s";

	if ($local==false) {
	   $result = $db->select_all($sql);
	}else{
       $result = get_llenar_combo($sql, $blanco , $predeterminado );
	}
	
	return $result;
}
?>