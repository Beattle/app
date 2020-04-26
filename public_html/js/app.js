$(function() {
  $('input[name="status"]').click(function(){
    let id = this.dataset.id
    let data = {id:id,status:this.checked,ajax:true};
    $.ajax({
      uri:window.location.href,
      method:'POST',
      data:data,
      dataType:'json',
      success:(data)=>{

      }
    })
  })
});