jQuery(document).ready(function($){
    // clockpicker init
    $('.clockpicker').clockpicker({
        donetext: "OK"
    });

    // datepicker init
    $('.datepicker').datepicker();

    // notification close
    $(".ccn-notification__close").click(function(){
        $(".ccn-notification").hide();
    })

    // colorpicker
    $(".colorpicker").spectrum();

    // date toggler
    $(".message-dates-header").click(function(e){
        e.preventDefault();
        let index = $(this).index();
        $(".message-dates-header").removeClass("active");
        $(this).addClass("active");
        $(".message-date").removeClass("active");
        $(".message-date").eq(index).addClass("active");
    });

    $('#add-new-ccn-message').on('click', function(e){
        e.preventDefault();
        $('a.page-title-action')[0].click();
    });

    $('select[name="ccn_message_layout"]').on('change', function(){

        var fd = new FormData();
        fd.append( "action", 'get_layout_color');
        fd.append( "layout", $(this).val());
        $.ajax({
            type: 'POST',
            url: ccnAjax.url,
            data: fd, 
            processData: false,
            contentType: false,
            beforeSend : function ( xhr ) {
                
            },
            success : function( data ){
                $("input[name='ccn_layout_box_background_color']").spectrum("set", data.ccn_layout_box_background_color);
                $("input[name='ccn_layout_box_border_color']").spectrum("set", data.ccn_layout_box_border_color);
                $("input[name='ccn_layout_box_text_color']").spectrum("set", data.ccn_layout_box_text_color);
                $("input[name='ccn_layout_button_background_color']").spectrum("set", data.ccn_layout_button_background_color);
                $("input[name='ccn_layout_button_background_color_on_hover']").spectrum("set", data.ccn_layout_button_background_color_on_hover);
                $("input[name='ccn_layout_button_text_color']").spectrum("set", data.ccn_layout_button_text_color);
                $('.ccn-message-preview__content').css({'border-color': data.ccn_layout_box_border_color, 'background-color': data.ccn_layout_box_background_color});
                $('.ccn-message-preview__text').css({ 'color': data.ccn_layout_box_text_color });
                $('.ccn-message-preview__title').css({ 'color': data.ccn_layout_box_text_color });
                $('.ccn-message-preview__button').css({ 'color': data.ccn_layout_button_text_color, 'background-color': data.ccn_layout_button_background_color });
                $('.ccn-message-preview__button').attr('onMouseOver', "this.style.backgroundColor='" + data.ccn_layout_button_background_color_on_hover +"'");
                $('.ccn-message-preview__button').attr('onMouseOut', "this.style.backgroundColor='" + data.ccn_layout_button_background_color +"'");
            }
        });
    });

    $('select[name="ccn_message_type"]').on('change', function(){
        if($(this).val() == 'minimum_amount') {
            $('div[data-layout="minimum_amount"]').show();
        } else {
            $('div[data-layout="minimum_amount"]').hide();
        }
        var fd = new FormData();
        fd.append( "action", 'get_message_data');
        fd.append( "message_type", $(this).val());
        $.ajax({
            type: 'POST',
            url: ccnAjax.url,
            data: fd, 
            processData: false,
            contentType: false,
            beforeSend : function ( xhr ) {
                
            },
            success : function( data ){
                $("p.message-type-notice").text(data.notice);
            }
        });
    });

    $('.ccn-button.save-page').on('click', function(e){
        e.preventDefault();
        $('#publishing-action input#publish').trigger('click');
    });

    $('input[name="ccn_message_header"]').on('change', function(){
        $('.ccn-message-preview__title').text($(this).val());
    });

    $('input[name="ccn_message_header"]').on('keyup', function(){
        $('.ccn-message-preview__title').text($(this).val());
    });

    $('textarea[name="ccn_message_text"]').on('change', function(){
        $('.ccn-message-preview__text').html($(this).val());
    });

    $('input[name="ccn_button_text"]').on('change', function(){
        $('.ccn-message-preview__button').text($(this).val());
    });

    $('input[name="ccn_button_text"]').on('keyup', function(){
        $('.ccn-message-preview__button').text($(this).val());
    });

    /*function myCustomOnChangeHandler() {
      alert("Some one modified something");
    }

    setTimeout(function () {
        if (tinyMCE.activeEditor.isDirty()) {
            myCustomOnChangeHandler();
        }
    }, 1000);*/
    // hidden block
    // $(".js-toggle-hidden").change(function(){
    //     let option = $(this).find("option:selected").val();
    //     if (option == "minimum_amount") {
    //         $(".ccn-message-hidden-block").slideDown();            
    //     } else {
    //         $(".ccn-message-hidden-block").slideUp();
    //     }
    // })

    // form change shows preview
    // $(".ccn-page-wrap").on('keyup change paste', 'input, select, textarea', function(){
    //     $(".ccn-message-preview").fadeIn();
    // })
})