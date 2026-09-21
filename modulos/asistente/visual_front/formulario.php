<div class="container-fluid" id="app">

    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <form id="formulario" method="POST" class="form-horizontal row" style="margin:auto;width:95%">

                         <div class="col-12 col-md-4" v-for="rw in data_disenos">
                             <div class ="card">
                                 <div class ="card-header border-t-danger">
                                 <p class   ="mt-1 f-m-light">
                                     <div class="form-check radio radio-success" v-if="rw.active ===1">
                                         <input class="form-check-input"  type="radio" name="diseno" v-bind:value="rw.id" v-bind:id="'radio_' + rw.id" checked="true">
                                         <label class="form-check-label" v-bind:for="'radio_' + rw.id">{{rw.nombre}}</label>
                                     </div>

                                     <div class="form-check radio radio-success" v-if="rw.active === 2">
                                         <input class="form-check-input"  type="radio" name="diseno" v-bind:value="rw.id" v-bind:id="'radio_' + rw.id">
                                         <label class="form-check-label" v-bind:for="'radio_' + rw.id">{{rw.nombre}}</label>
                                     </div>
                                 </p>
                                 </div>
                                 <div class ="card-body">

                                                <div class="form-group row">
                                                     <label for="accion" class="col-sm-6 col-form-label">Color primario</label>
                                                     <div class="col-sm-6">
                                                        <input type="color" v-bind:id="'color_primario_' + rw.id"  v-bind:name="'color_primario_' + rw.id" title="Color primario" placeholder="Color primario" maxlength="50" v-bind:value="rw.color_primario" />
                                                     </div>
                                                 </div>

                                    <img v-bind:src="rw.img" style="width: 100%; ">
                                 </div>
                             </div>
                         </div>


                          <button class="btn btn-square btn-outline-info tooltip_btn" 
                                   id="btn_aceptar"
                                   type="button" 
                                   title="Click para actualizar" 
                                   onclick="actualizar()" 
                                   data-bs-original-title="Click para aceptar"><i class="fa fa-check"></i> Actualizar</button> 

                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
const vm = new Vue({
  el: '#app',
  data: {
    data_disenos:[]
  },
  computed: {
      data_disenos: {
        // getter
        get: function () {
          return this.data_disenos
        },
        // setter
        set: function (val) {
          this.data_disenos = val
        }
      }
  }
})


function listar_disenos() {
  $.ajax({
    url: page_root+'listar_disenos',
    type: 'POST',
    dataType: 'json',
    data: {},
  })
  .done(function(r) {
    vm.data_disenos = r.data;
  })
  .fail(function() {
    console.log("error");
  });
  
 
}


function actualizar() {
   $.ajax({
    url: page_root+'aceptar',
    type: 'POST',
    dataType: 'json',
    data: $("#formulario").serialize(),
  })
  .done(function(r) {
      msg(r.msg);
      listar_disenos();
      setTimeout(function() { location.reload(); }, 1000);
  })
  .fail(function() {
    console.log("error");
  });
}



jQuery(document).ready(function($) {
   listar_disenos();
   
});
 
</script>