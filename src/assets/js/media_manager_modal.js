media_manager_modal = {
    modal_id: '',
    init: function(){
        this.initButtons();
    },

    initButtons: function() {

        $('.media-manager-import-modal-btn').unbind();
        $('.media-manager-import-modal-btn').click(function(){

            console.log( '[media_manager_modal] Import button click' );

            var import_url = $(this).attr('data-import-url');
            var params = $(this).attr('data-params');
            params = JSON.parse(params);
            console.log( params );

            var btn = $(this);
            btn.attr('disabled','disabled');

            var box = $('.media_manager_files');
            var attach = $(box).find( "li.selected" );
            var id     = $(attach).attr('data-id');

            var selected_json = $('.selected-attaches', box).val();
            var parsed_json = JSON.parse(selected_json);

            params.selected = parsed_json;

            if ( parsed_json.length < 1 ) {
                alert( 'Необходимо выбрать хотя бы один файл' );
            } else {

                 $.ajax({
                     type: 'POST',
                     url: import_url,
                     dataType: 'json',
                     data: params,
                     success: function( data ){
                         document.location.reload();
                     }
                 });
            }

        });

        $('.media-manager-show-modal-btn').click(function(){
            console.log('[media_manager_modal] Show modal' );
            var id = $(this).attr('data-id');
            var form = $('#' + id + ' .modal-content .modal-body');
            this.modal_id = id;

            $.ajax({
                dataType: 'json',
                cache: false,
                url: '/backend/media_manager/ajax/widget',
                data:{
                   id: id
                },
                success: function (data) {
                    form.empty().append(data.content);
                }
            });
        });
    }
}

$(document).ready(function(){
    media_manager_modal.init();
});