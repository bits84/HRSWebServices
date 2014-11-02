<?php
/**
 * Created on 02.05.2013
 * @author Andrey Morozov andrey@3davinci.ru
 */
?>

<div class="b-hotel-filter hrs">
    <form id="hrs_form_order" class="b-hotel-filter__form" autocomplete="off"
          onsubmit="hrs.checkForm( this ); return false;" name="booking_form" method="post" action="/hotel">
        <div class="b-hotel-filter__form-unit">
            <p class="b-hotel-filter__form-unit-name">Пункт назначения:</p>
            <?php
            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'id' => '_depart',
                'name' => '_depart',
                'value' => isset($formParams['depart']) ? CHtml::encode($formParams['depart']) : '',

                'source' => 'js: function(request, response) {
                                            $.ajax({
                                                url: "/hotel/search",
                                                method: "GET",
                                                dataType: "json",
                                                data: {title: request.term, last: "arival", data_json: 1},
                                                success: function (data) {
                                                        response(data.slice(0, 10));
                                                    }
                                            });
                                        }',
                'htmlOptions' => array(
                    'class' => 'b-hotel-filter__form-unit-field _depart',
                ),
                'options' => array(
                    'minLength' => '2',
                    'showAnim' => 'fold',
                    'select' => 'js:function(event, ui) {
                                                $("#_depart").val(ui.item.value);
                                                $("#depart").val(ui.item.value);
                                            }',
                    'focus' => 'js:function( event, ui ) {event.preventDefault()}'
                )
            ));
            ?>

            <input class="hrs__control hrs__control_hidden text depart" id="depart"
                   name="depart" type="hidden"
                   value="<? if (isset($formParams['depart'])) echo CHtml::encode($formParams['depart']); ?>."/>

            <div id="hrs__city-depart-list" class="city-list_depart"
                 style="height: 450px; overflow:auto; position:absolute; z-index:9999;"></div>
        </div>
        <div class="b-hotel-filter__form-unit">


            <?
            $date = new DateTime();
            ?>
            <div class="hrs__control-group hrs__control-group_horizontal hrs__control-group_indent">
                <?
                $date_one = new DateTime();
                $date_one->modify('+1 day');
                ?>

                <div class="hrs__control-group-unit">
                    <div class="input-append date" data-date-format="dd-mm-yyyy" id="dateto_hrs">
                        <label for="dateto" data-width-day="9" class="b-hotel-filter__form-unit-name">Дата
                            въезда</label>
                        <input id="dateto" class="b-hotel-filter__form-unit-field b-hotel-filter__form-item-field"
                               name="dateto"
                               value="<?= (isset($formParams['dateto'])) ? $formParams['dateto'] : $date->format("d-m-Y") ?>"
                               type="text">
                        <span class="add-on" style="background: 0 0;border: none;height: auto;"><i
                                class="b-icon b-icon_size_16x16 b-icon_type_calendar"></i></span>
                    </div>


                    <div class="input-append date" data-date-format="dd-mm-yyyy" id="dateback_hrs"
                        <? if (isset($formParams['isReturn']) && $formParams['isReturn'] == 2) echo ' style="display: none;"'; ?>>
                        <label for="dateback" data-width-day="9" class="b-hotel-filter__form-unit-name">Дата
                            выезда</label>
                        <input id="dateback" class="b-hotel-filter__form-unit-field b-hotel-filter__form-item-field"
                               name="dateback"
                               value="<?= (isset($formParams['dateback'])) ? $formParams['dateback'] : $date_one->format("d-m-Y") ?>"
                               type="text">
                        <span class="add-on" style="background: 0 0;border: none;height: auto;"><i
                                class="b-icon b-icon_size_16x16 b-icon_type_calendar"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="b-hotel-filter__form-unit" style="width: 350px;">
            <div class="b-hotel-filter__form-item">
                <p class="b-hotel-filter__form-unit-name">Одноместный</p>
                <input class="b-hotel-filter__form-unit-field b-hotel-filter__form-item-field" name="single" id="single"
                       type="text" onkeyup="adultsCountPiple();"
                       placeholder="1" value="<?= (isset($formParams['single'])) ? $formParams['single'] : '1' ?>">
            </div>
            <div class="b-hotel-filter__form-item">
                <p class="b-hotel-filter__form-unit-name">Двухместный</p>
                <input class="b-hotel-filter__form-unit-field b-hotel-filter__form-item-field" name="double" id="double"
                       type="text" onkeyup="adultsCountPiple();"
                       placeholder="0" value="<?= (isset($formParams['double'])) ? $formParams['double'] : '0' ?>">
            </div>

        </div>
        <div class="b-hotel-filter__form-unit" style="width: 350px;">
            <div class="b-hotel-filter__form-item">
                <p class="b-hotel-filter__form-unit-name">Взрослых:</p>
                <input class="b-hotel-filter__form-unit-field b-hotel-filter__form-item-field" type="text" id="adults"
                       readonly="readonly"
                       placeholder="1"
                       value="<?= (isset($formParams['adult_count'])) ? $formParams['adult_count'] : '1' ?>">
            </div>
            <div class="b-hotel-filter__form-item">
                <p class="b-hotel-filter__form-unit-name">Дети:</p>
                <input class="b-hotel-filter__form-unit-field b-hotel-filter__form-item-field" type="text" name="child"
                       placeholder="0"
                       value="<?= (isset($formParams['child_count'])) ? $formParams['child_count'] : '0' ?>">
            </div>

        </div>


        <div class="b-hotel-filter__border"></div>
        <div class="b-hotel-list__unit-btn">
            <input class="hrs__control_submit b-ptt-btn" type="submit" value="Найти отели"/>
        </div>

        <div class="b-hotel-filter__map" style="text-align:center;">
            <img src="/images/filter-map.jpg">
        </div>

        <div class="b-hotel-list__unit-btn">
            <a href="javascript:void(0)" class="b-hotel-list__unit-btn-link"
               onclick="$('#hrs_form_order').attr('action','/map/hotel'); $('#hrs_form_order').submit(); ">Показать
                на карте</a>
        </div>
    </form>
</div>





<?
$date = new DateTime();
$date->modify('-1 month');
?>
<script>
    $('#dateto_hrs').datepicker({language: 'ru',autoclose: true});
    $('#dateback_hrs').datepicker({language: 'ru',autoclose: true});

    function adultsCountPiple() {
        var single = $('#single').val();
        var double = $('#double').val();
        var result = parseInt(single) + (parseInt(double) * 2);
        $('#adults').val(result);
    }
    function send() {
        alert($("#depart").val());

        $.post(
            "/hotel",
            {'depart': $("#depart").val()},

            function (response) {
                if (response.res == 'error') {

                } else if (response.res == 'ok') {
                    location.href = '/hotels/hotelAvail';
                }
                //     hrs.overlayHide();
                //      hrs.progressbar.hide();
                //       hrs.progressStop();
                //console.log(response);
            }

        );
    }


    function showHotelsOnMap() {

        $.post(
            "/map/hotel",
            $('#hrs_form_order').serialize(),
            function (data) {
                location.href = '/map/hotel';
            }

        );

    }

    var minDate = new Date(<?= $date->format("Y"); ?>, <?= $date->format("m"); ?>, <?= $date->format("d"); ?>);
</script>

