(function($){

    let ajax_url = pvum_ajax_data.ajax_url;
    let count_refresh_interval = pvum_ajax_data.count_refresh_interval;
    count_refresh_interval = parseInt( count_refresh_interval );
    if( ! count_refresh_interval || count_refresh_interval < 5000){
        count_refresh_interval = 5000;
    }

    //console.log( parseInt( count_refresh_interval ) );

    $(document).on('click','#profile-viewers-icon',function(e){
        e.preventDefault();
        var link = $(this);
        var counter = link.find('#counter');
        var profile_id = link.attr('data-profile');

        //if( link.hasClass('flashing')){
            var panel = $('#profile-view-list');
            if(panel.hasClass('open')){
                panel.removeClass('open');
                panel.find('.profile-view-body').html('');
            }
            else{
                var action_url = ajax_url+'?action=get_views&profile='+profile_id;
                var panel_body = panel.find('.profile-view-body');
                panel_body.html('<div class="pvum-loader"><span class="lds-dual-ring"></span></div>');
                panel.addClass('open');
                $.get(action_url,function(data){
                    panel_body.html(data);
                    var items = panel_body.find('li');
                    panel.find('.clear-all-link').attr('data-count',items.length);
                });
            }
            counter.text(0);
            counter.attr('data-count',0);
        //}
        
    });

    $(document).on('click','.pvum-panel-close',function(e){

        var panel = $('#profile-view-list');
        panel.removeClass('open');
        panel.find('.profile-view-body').html('');

    });

    $(document).on( 'click', '[data-id="pvum-clear-all-views"]',function( e ){
        e.preventDefault();
        var link = $(this);

        if( link.hasClass('busy') ){
            return;
        }

        link.addClass('busy');

        var view_count = link.attr('data-count');
        var profile_id = link.attr( 'data-profile' );

        view_count = parseInt( view_count);
        if( ! view_count ){
            return false;
        }

        var action_url = ajax_url+'?action=pvum_clear_views';
        $.post(action_url,{ profile: profile_id },function( data ){
            link.removeClass('busy');
            link.attr('data-count',0);
            var panel = $('#profile-view-list');
            panel.find('.profile-view-body').html( data );
        });

    });

    setInterval(function(){
        var icon = $('#profile-viewers-icon');
        if( icon.length ){
        var profile_id = icon.attr('data-profile');
        var counter_action_url = ajax_url+'?action=get_views_count&profile='+profile_id;
        var action_url = ajax_url+'?action=get_views&profile='+profile_id;
                $.get(counter_action_url,function(data){

                    //console.log('ajax count:'+data);
                    var panel = $('#profile-view-list');
                    var number = parseInt(data);
                    var link = $('#profile-viewers-icon');
                    var counter = link.find('#counter');
                    counter.text(number);
                    counter.attr('data-count',number);
                    if( number ){
                        link.addClass('flashing');
                    }

                    if( panel.hasClass('open') && number >= 1){
                        var panel_body = panel.find('.profile-view-body');
                        panel_body.html('<p style="text-align:center;margin-top:30px;">Loading...</p>');
                        $.get(action_url,function(data){
                            panel_body.html(data);
                        });

                        counter.text(0);
                        counter.attr('data-count',0);
                    }


                });
            }
    },  count_refresh_interval );

})(jQuery);