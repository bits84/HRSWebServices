<div class="biletix">
<form id="biletix_form_order" class="form-order ticket-small-form clearfix style_ptt-form form-inline" autocomplete="off" name="booking_form" onsubmit="hrs.checkForm( this ); return false; " method="post" action="/hotels">

    <div class="biletix__control-group_city biletix__control_small-search">
        <label class="control-label" for="depart">Город</label>
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
                                                    data: {title: request.term, last: "depart", data_json: 1},
                                                    success: function (data) {
                                                            response(data.slice(0, 10));
                                                        }
                                                });
                                            }',
            'htmlOptions' => array(
                'class' => 'text route_place _depart',
                'style' => "position: relative;"
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
        <input type="hidden" class="text route_place depart" id="depart" name="depart" value="<? if (isset($formParams['depart'])) echo CHtml::encode($formParams['depart']); ?>."/>
        <div class="city-list_depart"
             style="height: 250px; overflow:auto;position:absolute;overflow-x: hidden; ">

        </div>
    </div>

    <div class="biletix__control_small-search">
        <label class="control-label" for="unit_count">Номер 1М</label>
        <input class="biletix__control_number" id="unit_count" type="text" name="unit" value="<? if (isset($formParams['unit'])) echo intval($formParams['unit']); else echo '1'; ?>"/>

        <label class="control-label" for="double_count">2М</label>
        <input class="biletix__control_number" id="double_count" type="text" name="double" value="<? if (isset($formParams['double'])) echo intval($formParams['double']); else echo '0'; ?>"/>
    </div>
    
    <div class="biletix__control_small-search">
        <label class="control-label" for="adult_count">Взрослые</label>
        <input class="biletix__control_number" id="adult_count" type="text" name="adult" value="<? if (isset($formParams['adult'])) echo intval($formParams['adult']); else echo '1'; ?>"/>

        <label class="control-label" for="child_count">Дети</label>
        <input class="biletix__control_number" id="child_count" type="text" name="child" value="<? if (isset($formParams['child'])) echo intval($formParams['child']); else echo '0'; ?>"/>
    </div>

    <div class="biletix__control-group_dates biletix__control_small-search">
        <label class="control-label" for="">Даты</label>

        <?
            $date = new DateTime();
        ?>
        <div class="input-append date datepicker" data-date-format="dd-mm-yyyy" id="dateto_hrs" style="width: 150px;">
            <input id="dateto" class="route_date biletix__control biletix__control_date" name="dateto" type="text" style="width:206px;" value="<? (isset($formParams['dateto'])) ? $formParams['dateto'] : $date->format("d-m-Y") ?>">
            <span class="add-on" style="background: 0 0;border: none;height: auto;"><i class="b-icon b-icon_size_16x16 b-icon_type_calendar"></i></span>
        </div>
    </div>
    <div class="biletix__control-group_dates biletix__control_small-search">
        <span class="biletix__control_dott"></span>
        <span class="biletix__control_dott"></span>
        <span class="biletix__control_dott"></span>
        <span class="biletix__control_dott"></span>
        <span id="js-datebackField">
            <span class="date_separator"></span>
            <div class="input-append date datepicker" data-date-format="dd-mm-yyyy" id="dateback_hrs" style="width: 150px;">
                <input id="dateback" class="route_date biletix__control biletix__control_date" name="dateback" type="text" style="width:206px;" value="<? (isset($formParams['dateback'])) ? $formParams['dateback'] : $date->format("d-m-Y") ?>">
                <span class="add-on" style="background: 0 0;border: none;height: auto;"><i class="b-icon b-icon_size_16x16 b-icon_type_calendar"></i></span>
            </div>
        </span>
    </div>
    <input type="hidden" class="count" name="infant" id="infant_count" value="<? if (isset($formParams['infant'])) echo intval($formParams['infant']); else echo '0'; ?>" />
    <input type="hidden" name="class" value="<? if (isset($formParams['class'])) echo intval($formParams['class']); else echo '1'; ?>" />
    <input type="hidden" name="directOnly" id="directOnly" value="<? if (isset($formParams['directFlights']) && $formParams['directFlights'] == 1) echo '1'; else echo '0'; ?>" />
    <div class="biletix__control_small-search">
        <input class="biletix__control_submit b-ptt-btn" type="submit" value="Найти отели"/>
    </div>
</form>
</div>
<?
    $date = new DateTime();
    $date->modify('-1 month');
?>
<script>
    $('#dateto_hrs').datepicker({language: 'ru'});
    $('#dateback_hrs').datepicker({language: 'ru'});


    var minDate = new Date(<?= $date->format("Y"); ?>, <?= $date->format("m"); ?>, <?= $date->format("d"); ?>);
    $(function () {}
</script>
