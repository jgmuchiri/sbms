$(document).ready(function () {
    $('.btn-data').click(function (e) {
        // e.preventDefault();
        // if (confirm('Please save your progress before adding new data')) {
        //
        // }
        var modal = $(this).attr('id');
        $(modal).modal('show');

    });

    // Toggle Function
    $('.toggle').click(function () {
        // Switches the Icon
        $(this).children('i').toggleClass('fa-pencil');
        // Switches the forms
        $('.form').animate({
            height: "toggle",
            'padding-top': 'toggle',
            'padding-bottom': 'toggle',
            opacity: "toggle"
        }, "slow");
    });
    $('[data-toggle="tooltip"]').tooltip();


    //
    //$('.editor').summernote({
    //  toolbar: [
    //    // [groupName, [list of button]]
    //    ['style', ['bold', 'italic', 'underline', 'clear']],
    //    ['font', ['strikethrough', 'superscript', 'subscript']],
    //    ['fontsize', ['fontsize']],
    //    ['color', ['color']],
    //    ['para', ['ul', 'ol', 'paragraph']],
    //    ['height', ['height']]
    //  ]
    //});

    // show active tab on reload
    if (location.hash !== '') $('a[href="' + location.hash + '"]').tab('show');

    // remember the hash in the URL without jumping
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (history.pushState) {
            history.pushState(null, null, '#' + $(e.target).attr('href').substr(1));
            $('.cv-edit-form').find('input[name=curPage]').val(location.hash);
        } else {
            location.hash = '#' + $(e.target).attr('href').substr(1);
            $('.cv-edit-form').find('input[name=curPage]').val(location.hash);
        }
    });

    $('.nav-item').on('click', function (e) {
        //store hash
        var target = this.hash;
        // e.preventDefault();
        $('body').scrollTo(target, 800, {offset: -70, 'axis': 'y'});
    });

    $('input[type=text],input[type=time],input[type=date],input[type=number],input[type=password],input[type=email],textarea,select').addClass('form-control');


    $('.delete').click(function(e){
        var loc =$(this).attr('href');
        swal({
            title:'Are you sure?',
            text:'This action is permanent. You will loose the data',
            type:'warning',
            showCancelButton:true,
            confirmButtonColor:'#DD6B55',
            confirmButtonText:'Yes, Do it!',
            closeOnConfirm:false,
            allowOutsideClick:false
        },function(){
            swal('Delete action successful');
            if(loc !=undefined)
                window.location.href=loc;
        });
        e.preventDefault();
    })

});

//fix youtube embeding
function yoube(url) {
    var regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    var match = url.match(regExp);
    if (match && match[2].length == 11) {
        return 'https://www.youtube.com/embed/'+match[2];
    } else {
        return url;
    }
}

var shortContent = function() {
    if($(window).height() > $('body').height()) {
        $('footer').addClass('shortContent');
    }

};

(function(){

    shortContent();

    $(window).resize(function() {
        shortContent();
    });

}());
/**
 * validates currency
 * @param amount
 * @returns {boolean}
 */
function validCurrency() {
    var amount = $('input[name=amount]').val();

    var regex = /^\d+(?:\.\d{0,2})$/;
    if (regex.test(amount)) {//curreny is ok
        if (amount == '0.00') {
            alert('Amount entered must be greater than zero');
            return false;
        }
        return true;
    } else {
        alert('Amount entered is invalid');
        return false;
    }
}