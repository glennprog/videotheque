$(document).ready(function () {
    $(".delete-alert").click(function(e){
        var msg = $(".delete-action").attr('data-delete-alert-msg');
        if(msg == undefined){
            msg = 'Voulez vraiment supprimez les données ?'
        }
        if( !confirm( msg ) ){
            e.preventDefault();
            console.log("no deleting");
        }
    });
});