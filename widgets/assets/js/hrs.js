$(function(){
    hrs.init();
});


function CIBEAjax() {
    this.post = function(formId) {
        var formObj = $('form#form'+formId);
        if (!formObj) return false;

        hrs.progressbar.find('span').text('Обработка запроса ...');
        hrs.overlayShow();
        hrs.progressbar.show();
        hrs.progressStart();

        var url = formObj.attr("action")
            , data = formObj.serialize();

        $.ajax( {
            url: url
            , type: 'POST'
            , data: data
            , context : this
            , success: function(response) {
                if (response.res == 'error') {
                    alert(response.data.msg);
                } else if (response.res == 'ok') {
                    var d = new Date();
                    location.href = url + '/#' + d.getTime();
                }
            }
            , error: function(jqXHR, textStatus, errorThrown) {

            }
            , complete: function(jqXHR, textStatus) {
                hrs.overlayHide();
                hrs.progressbar.hide();
                hrs.progressStop();
            }
        });

    }
}
ibe_ajax = new CIBEAjax();




jQuery.fn.center = function () {
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
    return this;
}

var hrs = {
    init: function() {
        $('body').append('<div id="popup-overlay"></div>');
        $('body').append('<div id="popup-progressbar"><span>Выполняется поиск ... </span><div class="progress progress-warning progress-striped"><div class="bar" style="width: 0%"></div></div></div>');
        hrs.overlay = $('#popup-overlay');
        hrs.progressbar = $('#popup-progressbar');
        $('.hrs__control_spinner-plus').bind('click', {increment: 1}, hrs.incrementValue);
        $('.hrs__control_spinner-minus').bind('click', {increment: -1}, hrs.incrementValue);
          $('input[name="isReturn"]').bind('change', {}, hrs.showDateback);
      $('#popup-progressbar').center();
        $('.ticket-variants tbody').bind('click', {}, hrs.selectTicketSegment);
        $('.ticket-variants tbody td input[type=radio]').bind('click', {}, hrs.selectTicketSegmentByInput);
    },
    incrementValue: function(e) {
        var valueElement = $('#'+$(this).data('id'));
        valueElement.val(Math.max(parseInt(valueElement.val()) + e.data.increment, 0));
        return false;
    },
    showDateback: function(e) {
        var val = $(this).val(),
            $datebackField = $('#js-datebackField').add( $('#js-datebackLabel') );
        if (val == 1) {
            $datebackField.show();
        } else {
            $datebackField.hide();
            $('#dateback').val('');
        }
        return false;
    },
    overlayShow: function() {
     //   if($.browser.msie) {
            hrs.overlay.show();
    //    } else {
     //       hrs.overlay.stop().fadeIn(250);
     //   }
    },
    overlayHide: function() {
   //     if($.browser.msie) {
            hrs.overlay.hide();
    //    } else {
   //         hrs.overlay.stop().fadeOut(250);
   //     }
    },
    progressStart: function() {
        var width = 0;
        hrs.myInterval = setInterval(function () {
            //var width = hrs.progressbar.find('.bar').css("width") || 0;
            var newWidthInt = width + 0.5;
            if (newWidthInt < 10) newWidthInt += 1.5;
            if (newWidthInt > 10 && newWidthInt < 30) newWidthInt += 2.5;
            if (newWidthInt > 100) {
                alert('На серевере произошла ошибка, попробуйде повторить попытку посже');
                hrs.overlayHide();
                hrs.progressbar.hide();
                hrs.progressStop();
            }
            width = newWidthInt;
            hrs.progressbar.find('.bar').css("width", newWidthInt+'%');
        },600);
    },
    selectTicketSegment: function() {
        var $currentRadio = $(this).find('input:radio');
        if($currentRadio.is(':checked') === false) {
            $currentRadio.prop('checked', true);
            $(this).parent().children("tbody").removeClass('selected');
            $(this).addClass('selected');
        }
    },
    selectTicketSegmentByInput: function() {
        $(this).parents(".ticket-variants").children("tbody").removeClass('selected');
        $(this).parents("tbody").addClass('selected');
    },
    progressStop: function() {
        clearInterval(hrs.myInterval);
        hrs.progressbar.find('.bar').css("width", '0%');
    },
    sendRequest: function(url) {

        hrs.overlayShow();
        hrs.progressbar.show();
        hrs.progressStart();

        $.post(
            url,
            $("#hrs_form_order").serialize(),

            function(response){
                if (response.res == 'error') {
                    alert('На серевере произошла ошибка, попробуйде повторить попытку посже');
                } else if (response.res == 'ok') {
                    location.href = url;
                }
                hrs.overlayHide();
                hrs.progressbar.hide();
                hrs.progressStop();
                //console.log(response);
            },
            "json"
        );
    },
    checkForm: function(form, suffix) {
        if ( typeof( suffix ) == "undefined" ) {
            suffix = "";
        }
        var dep = document.getElementById("depart");

        if(dep) {
            if(dep.value == '.') {
                alert('Необходимо указать город');
                hrs.TryFocusObj( dep );
                return false;
            }
        }
        var dateto = hrs.checkDate(form.elements[ "dateto" + suffix ].value);

        if ( dateto == false ){
            alert('Необходимо указать дату везда');
            hrs.TryFocusObj( form.elements[ "dateto" + suffix ] );
            return false;
        }

        var minDay = new Date(minDate.getTime());
        minDay.setHours(0, 0, 0, 0);

        if ( dateto && minDay > dateto ){
            alert('Невозможно забронировать отель на прошедшие время. Актуализируйте даты поездки.');
            hrs.TryFocusObj( form.elements[ "dateto" + suffix ] );
            return false;
        }





        var dateback = hrs.checkDate(form.elements[ "dateback" + suffix ].value);
        if ( dateback == false ) {
            alert('Необходимо указать дату отбытия');
            hrs.TryFocusObj( form.elements[ "dateback" + suffix ] );
            return false;
        } else {
            if ( dateback < dateto ) {
                alert('Дата заезда не может быть позже даты отбытия');
                return false;
            }
        }


        var adult = document.getElementById("adult_count" + suffix);
        var child = document.getElementById("child_count" + suffix);
        var infant = document.getElementById("infant_count" + suffix);
        if (adult && child && infant) {
            // количество малышей не может привышать количество взрослых
            if (parseInt(adult.value) < parseInt(infant.value)) {
                alert('Количество малышей не может привышать количество взрослых');
                return false;
            }
            // общее количество пассажиров не может быть больше 9
            var total_passengers = (parseInt(adult.value) + parseInt(child.value) + parseInt(infant.value));
            if ( total_passengers > 9){
                alert('Общее количество не может быть больше 9');
                return false;
            }
            if ( total_passengers == 0){
                alert('Общее количество не может быть равно нулю');
                return false;
            } else if (parseInt(adult.value) == 0 && (parseInt(child.value) + parseInt(infant.value)) > 0) {
                alert('Дети не могут лететь без взрослых');
                return false;
            }
        }

        hrs.sendRequest(form.action);
    },
    checkDate: function(dateString) {
        dateString.replace(/^\s+|\s+$/g, '');
        var date = dateString.split(/\W+/);
        if ('undefined' == typeof date_format) {
            var df = {'dd': 0, 'mm': 1, 'yy': 2};
        } else {
            var df_tmp = date_format.split(/\W+/);
            var df = {};
            for (var i in df_tmp) {
                df[df_tmp[i]] = i;
            }
        }
        if (date.length != 3) {
            return false;
        }

        var testDate = new Date(date[df.yy], date[df.mm] - 1, date[df.dd]);
        if(
            testDate.getFullYear() != date[df.yy]
                || testDate.getMonth() + 1 != date[df.mm]
                || testDate.getDate() != date[df.dd]
            ) {
            return false;
        }
        return testDate;
    },
    TryFocusObj: function( obj ) {
        try {
            obj.focus();
        } catch ( ex ) {}
    }
}