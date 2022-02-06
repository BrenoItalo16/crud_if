

function confirmDelete(url){
    $('.ui.basic.modal').modal('show');
    $('#confirmDeleteBtn').click(function(){
        window.location = url;
    })
}