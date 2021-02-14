media_manager = {
    id: '',
    config: {},
    selected: [],
    page: 1,
    can_load: true,

    init: function( id, config ) {
        console.log( '[media_manager-' + id + '] Init' );

        this.page     = 1;
        this.can_load = true;

        this.id     = id;
        this.config = config;
        this.selected = [];

        obj = this;
        this.render();

        $('.manager_files_attaches' ).on('scroll', function(){
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                obj.page ++;

                var path = $('.media_toolbar select[name="date"]').val();
                obj.list( path,true);
            }
        });

        return this;
    },

    render: function(){
        console.log( '[media_manager-' + this.id + '] Render' );

        this.initDropzone(this.id);
        this.initSidebar();
        this.initNavBar();

        return this;
    },

    initNavBar: function () {
        console.log( '[media_manager' + this.id + '] Init navigation bar' );

        obj = this;

        $('.media_toolbar select[name="date"]').change(function(){
            var path = $(this).val();
            obj.list(path);
        });

        $('.media_toolbar input[name="search"]').change(function(){
            var search = $(this).val();

            if ( search != '' ) {
                $.post( obj.config['api']['searchUrl'], { search: search }, function(data){
                    $('.manager_files_attaches').hide().html( data.result ).fadeIn();
                    obj.hideSidebar();
                    obj.initFiles();
                });
            } else {
                obj.list(obj.config['path']);
            }
        });
    },

    showSidebar: function() {
        obj = this;

        if ( obj.selected.length == 1 ) {
             $('.media_manager_files .attachment-mass-actions').hide();
             $('.media_manager_files .attachment-details').fadeIn();
        }
        else if ( obj.selected.length > 1 ) {
             $('.media_manager_files .attachment-mass-actions .details .file-count').html( 'Выбрано файлов: <b>' + obj.selected.length + '</b>');
             $('.media_manager_files .attachment-mass-actions').fadeIn();
             $('.media_manager_files .attachment-details').hide();
        }
        else if ( obj.selected.length == 0 ) {
             $('.media_manager_files .attachment-mass-actions').fadeOut();
             $('.media_manager_files .attachment-details').fadeOut();
        }
    },

    hideSidebar: function(){
        $('.media_manager_files .attachment-mass-actions').fadeOut();
        $('.media_manager_files .attachment-details').fadeOut();
    },

    initDropzone: function(id) {
        console.log( '[media_manager-' + id + '] Init Dropzone' );

        Dropzone.autoDiscover = false;

        var drop = new Dropzone( '#manager_upload_' + id,{
            url: obj.config['api']['uploadUrl'],
            params: obj.config['dropzoneConfig']['params'],
            paramName: 'file',
            method: 'POST'
        } );

        drop.on('queuecomplete', function(file) {
            obj.list();
        });

        drop.on('complete', function(file){
            drop.removeFile(file);
        });
    },

    initSidebar: function() {
        console.log( '[media_manager-' + this.id + '] Init Sidebar' );
        obj = this;

        //$(document).on('click', '.manager_sidebar .save-attachment', function(){
        $('.manager_sidebar .save-attachment').click(function(){
            var button = $(this);
            $(button).attr('disabled','disabled');

            var id          = $('.manager_sidebar .attachment-details').attr('data-id');
            var title       = $('.manager_sidebar .attachment-details label[data-setting="title"] input').val();
            var description = $('.manager_sidebar .attachment-details label[data-setting="description"] textarea').val();

            var params = {
                id: id,
                title: title,
                description: description
            };

            console.log( params );

            $.post( obj.config['api']['updateUrl'], params, function (data){
                $(this.main_id + ' .manager_files_attaches li.attach[data-description="' + id + '"]').attr( 'data-description', description );
                $(button).attr('disabled',false);
                obj.list();
            });
        });

        //$(document).on('click',  '.manager_sidebar .delete-attachment', function(){
        $('.manager_sidebar .delete-attachment').click(function(){
            var button = $(this);
            $(button).attr('disabled','disabled');

            $.post( obj.config['api']['deleteUrl'], { selected: obj.selected }, function(data){
                data.deleted.forEach(function (id, index, arr){
                    $('.manager_files_attaches li.attach[data-id="' + id + '"]').fadeOut('normal', function(){
                        $(this).remove();
                    });
                });

                obj.hideSidebar();
                obj.selected = [];

                $(button).attr('disabled',false);
            });
        });
    },

    initFiles: function() {
        console.log( '[media_manager-' + this.id + '] Init files zone' );
        obj = this;

        $('.media_manager_files .attach').click(function(){
            var id = $(this).data('id');
            var selected = true;

            if ( $(this).hasClass('selected')) {
                $(this).removeClass('selected');
                selected = false;
                obj.sel(id, false);
            } else {
                //$('.attach').removeClass('selected');
                $(this).addClass('selected');
                obj.sel(id,true);
            }

            // Инициализация для одного файла
            if( obj.selected.length == 1 ) {
                var url = $('.thumb img', this).attr('src');

                $('.media_manager_files .attachment-details').attr( 'data-id', id );
                $('.media_manager_files .attachment-details .thumbnail img').attr('src', url );
                $('.media_manager_files .attachment-details .details .uploaded').html( $(this).data('date'));
                $('.media_manager_files .attachment-details .details .filename').html( $(this).data('name'));
                $('.media_manager_files .attachment-details .details .file-size').html( $(this).data('size'));

                $('.media_manager_files .attachment-details .setting[data-setting="title"] input').val( $(this).data('title'));
                $('.media_manager_files .attachment-details .setting[data-setting="description"] textarea').val( $(this).data('description'));
            }

            obj.showSidebar();
        });
    },

    sel: function( id, select = true ) {
        console.log('[media_manager-' + id + '] Select' );

        obj = this;
        obj.selected = [];

        $('.manager_files_attaches .attach').each(function(){
            if ( $(this).hasClass('selected')) {
                 obj.selected.push( $(this).attr( 'data-id' ));
            }
        });

        console.log( obj.selected );
        $('.media_manager_files .selected-attaches').val(JSON.stringify( obj.selected ));
    },

    list: function( path = '', append = false ){
        console.log('[media_manager-' + this.id + '] Get files list' );

        obj = this;
        obj.hideSidebar();

        $.post( this.config['api']['listUrl'], {
            path: path,
            page: obj.page
        }, function(data){

            if ( append ) {
                if ( data.can_load ) {
                    $('.manager_files_attaches').append(data.result);
                }
            } else {
                $('.manager_files_attaches').html('').hide().html( data.result ).fadeIn();
            }

            obj.initFiles();
        });
    }
}