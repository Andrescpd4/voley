    <script src="<?php echo WEB_ROOT ?>js/heaven/rollups/aes.js"></script>
    <script type="text/javascript" src="<?php echo WEB_ROOT ?>js/jquery_ui/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo WEB_ROOT ?>js/jquery/validation.js"></script> 
    <script type="text/javascript" src="<?php echo WEB_ROOT ?>js/heaven/general.js"></script>
    <script type="text/javascript" src="<?php echo WEB_ROOT ?>js/heaven/grid.js"></script>
    <script type="text/javascript" src="<?php echo WEB_ROOT ?>js/heaven/jquery.extra.js?t=1"></script>
    <script type="text/javascript" src="<?php echo WEB_ROOT ?>js/heaven/pagination.js"></script>
    <script type="text/javascript" src="<?php echo WEB_ROOT ?>js/vue.min.js"></script>
   
    <script type='text/javascript' src='<?php echo WEB_ROOT ?>plantilla/assets/libs/choices.js/public/assets/scripts/choices.min.js'></script>
    
<script src="<?php echo WEB_ROOT ?>js/heaven/toastDemo.js"></script>
     <script src="<?php echo WEB_ROOT ?>js/heaven/desktop-notification.js"></script>

     <!-- Script heaven.js no existe en esta version, comentado para evitar error 404 -->
     <!-- <script src="<?php echo WEB_ROOT ?>js/heaven.js"></script> -->

     <script src="<?php echo WEB_ROOT ?>js/multi_select/jquery.sumoselect.js"></script>
    <link href="<?php echo WEB_ROOT ?>js/multi_select/sumoselect.css" rel="stylesheet" />

    <script type="text/javascript" src="<?php echo WEB_ROOT ?>js/heaven/formulario_basico_v2.js"></script>
    <link href="<?php echo WEB_ROOT ?>js/crud/bootstrap-table.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
    <script src="<?php echo WEB_ROOT ?>js/crud/tableExport.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>js/crud/bootstrap-table.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>js/crud/bootstrap-table-locale-all.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>js/crud/bootstrap-table-export.min.js"></script>
    <script src="<?php echo WEB_ROOT ?>js/crud/bootstrap-table-mobile.min.js"></script>

<!-- Resources -->
     <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
     <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
     <script src="https://cdn.amcharts.com/lib/4/themes/material.js"></script>
     <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>


   


    <script type="text/javascript">

        $(document).ready(function(e) {
           $("input, select, textarea").change(function(e){
              $(e.target).removeClass("error");  
           });

           set_token();

           $('.js-example-basic-multiple').select2({
              placeholder: 'Seleccione varias opciones...',
           });
           $('.js-example-basic-single').select2({
              placeholder: 'Seleccione una opción...',
           });
           
           $('.select_auto').select2({
              placeholder: 'Seleccione una opción...',
           });
           
           $('.select_auto_multiple').select2({
             placeholder: 'Seleccione varias opciones...',
           });
            
            $('.select_auto2').SumoSelect({search: true, 
              searchText: 'Seleccione...',
              placeholder: 'Seleccione...',
              captionFormat: '{0} Seleccionados',
              captionFormatAllSelected: '{0} Todos Seleccionados!',
              noMatch : 'No hay coincidencias para "{0}"',
              locale :  [ 'Aceptar' ,  'Cancelar' ,  'Seleccionar todo' ],
              nativeOnDevice: ['Android', 'BlackBerry', 'iPhone', 'iPad', 'iPod', 'Opera Mini', 'IEMobile', 'Silk'],
              showTitle : 'true',
              selectAll:false,

            });

            $('.select_auto2_full').SumoSelect({search: true, 
              searchText: 'Seleccione...',
              placeholder: 'Seleccione...',
              captionFormat: '{0} Seleccionados',
              captionFormatAllSelected: '{0} Todos Seleccionados!',
              noMatch : 'No hay coincidencias para "{0}"',
              locale :  [ 'Aceptar' ,  'Cancelar' ,  'Seleccionar todo' ],
              nativeOnDevice: ['Android', 'BlackBerry', 'iPhone', 'iPad', 'iPod', 'Opera Mini', 'IEMobile', 'Silk'],
              showTitle : 'true',
              selectAll:true,

            });
        });


function tablesorte(id,pageLength=25,order=0,forma_orden="asc") {
  var nFilas = $('#'+id).length;

  if (nFilas<=1) {
      
       if($("#"+id).hasClass('dataTable')) {

       }else{
         
        $.fn.dataTable.ext.errMode = 'none';                 
        $('#'+id).DataTable( {
            "scrollX": true,
            "scrollY": true,
            "fixedHeader": true,
            "responsive": false,
            "language": {
            "url": "js/datatable/spanish.json"
          },
            "pageLength": pageLength,
            "order": [[ order, forma_orden ]],     
            dom: 'Bfrtip',
            buttons: [
                {
                   extend: 'excel',
                   title: 'Exportar Excel'
                }
              ]
          });
         
       }     
    }

    setTimeout(function() {
      $(".dt-button").addClass('btn');
      $(".dt-button").addClass('btn-outline-success');
    }, 100);
   
  }


function paginadorInit(startPage,perPage,containerID,paginadorClass){
    minHeight = false;
    $(paginadorClass).jPages({
        containerID  : containerID,
        perPage      : perPage,
        startPage    : startPage,
        startRange   : 1,
        midRange     : 5,
        endRange     : 1,
        first        : '',
        previous     : 'Anterior',
        next         : 'Siguiente',
        last         : '',
        minHeight    : minHeight,
        callback     : function(pages,items){
                       }
    });    
}
</script> 

