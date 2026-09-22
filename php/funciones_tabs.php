<?php
function get_tabs($ids_seccion = "", $padre = true ){
    
    global $db;
    $seccion = $db->select_all("SELECT * FROM seccion WHERE id IN ($ids_seccion) ");
	echo '<ul class="nav nav-pills nav-primary" id="pills-tab" role="tablist">';
	        foreach ($seccion as $key => $rw) {
	        	if($key==0) { $a = 'active'; }else{ $a=""; }
	        	echo '<li class="nav-item">
	        	  <a class="nav-link '.$a.'" id="tab_'.$rw["_key"].'-tab" data-bs-toggle="pill" href="#tab_'.$rw["_key"].'" 
	        	     role="tab" aria-controls="tab_'.$rw["_key"].'" aria-selected="true">'.$rw["nombre"].'</a>
	        	</li>';
	        }
    echo '</ul><hr>';

    echo '<div class="tab-content" id="pills-tabContent">';
            foreach ($seccion as $key => $rw) {
            	if($key==0) { $a = ' show active '; }else{ $a=""; }
            	echo '<div class="tab-pane fade '.$a.'" id="tab_'.$rw["_key"].'" role="tabpanel" aria-labelledby="tab_'.$rw["_key"].'-tab">';
                       get_hijos($rw['id']);
                echo '</div>';
            }
    echo '</div>';

   
}


function get_hijos($ids_seccion){
 	global $db;
    $path_root = __DIR__;
    $path_root = str_replace('\php', "", $path_root);
    $seccion = $db->select_all("SELECT * FROM sub_seccion WHERE id_seccion  IN ($ids_seccion) ORDER BY orden");
	echo '<ul class="nav nav-pills nav-primary" id="pills-tab" role="tablist">';
	        foreach ($seccion as $key => $rw) {
	        	if($key==0) { $a = 'active'; }else{ $a=""; }
	        	echo '<li class="nav-item">
	        	  <a class="nav-link '.$a.'" id="tab_'.$rw["_key"].'-tab" data-bs-toggle="pill" href="#tab_'.$rw["_key"].'" 
	        	     role="tab" aria-controls="tab_'.$rw["_key"].'" aria-selected="true">'.$rw["nombre"].'</a>
	        	</li>';
	        }
    echo '</ul>';

    echo '<div class="tab-content" id="pills-tabContent">';
            foreach ($seccion as $key => $rw) {
            	if($key==0) { $a = ' show active '; }else{ $a=""; }
            	echo '<div class="tab-pane fade '.$a.'" id="tab_'.$rw["_key"].'" role="tabpanel" aria-labelledby="tab_'.$rw["_key"].'-tab">';
                        include_once ($path_root.'/modulos/'.$rw['url'] );
                echo '</div>';
            }
    echo '</div>';

    
}
?>