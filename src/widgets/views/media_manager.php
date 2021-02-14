<div id="manager_upload_<?= $id ?>" class="media_manager_upload">
    <span class="title">Перетащите файлы сюда</span><br/>
    <span>или кликните по полю</span><br/>
    <span>Максимальный размер файла: 500 MB.</span>
    <br/><br/>
</div>
<div id="manager_files_<?= $id ?>" class="media_manager_files">
    <input type="hidden" class="selected-attaches" value="" />

    <div class="media_toolbar">
        <div class="left">
            <?php
                $month_list = [ '1'=>'Январь','2'=>'Февраль','3'=>'Март', '4'=>'Апрель','5'=>'Май', '6'=>'Июнь', '7'=>'Июль','8'=>'Август','9'=>'Сентябрь', '10'=>'Октябрь','11'=>'Ноябрь','12'=>'Декабрь' ];
                $options = [ 0 => 'Все даты' ];
                foreach ( $dates as $date ) {
                     $key = $date[ 'year' ] . '-' . $date[ 'month'];
                     $options[ $key ] = $month_list[ $date[ 'month']] . ' ' . $date['year'];
                }
                echo \yii\helpers\Html::dropDownList('date', 0, $options, [ 'class' => 'form-control'] );
            ?>
        </div>
        <div class="right">
            <?= \yii\helpers\Html::textInput('search', '', [ 'class' => 'form-control', 'placeholder' => 'Поиск медиафайлов ...' ] ); ?>
        </div>
    </div>
    <div style="clear:both"></div>
    <ul class="manager_files_attaches"></ul>
    <div class="manager_sidebar">
        <div class="attachment-mass-actions">
            <h3>Выбранные файлы</h3>
            <div class="attachment-info">
                <div class="details">
                    <div class="file-count">Выбрано файлов: 0</div>
                    <div style="padding-top: 5px">
                        <button type="button" class="btn btn-default button-link delete-attachment">Удалить навсегда</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="attachment-details">
            <h3>Параметры файла</h3>
            <div class="attachment-info">
                <div class="thumbnail thumbnail-application">
                    <img src="" class="icon" draggable="false" alt="">
                </div>
                <div class="details">
                    <div class="filename">---</div>
                    <div class="uploaded">01.01.2020</div>
                    <div class="file-size">0 KB</div>
                    <div style="padding-top: 5px">
                        <button type="button" class="btn btn-default button-link delete-attachment">Удалить навсегда</button>
                    </div>
                </div>
            </div>

            <label class="setting" data-setting="title" style="padding-top: 20px;">
                <span class="name">Заголовок</span><br/>
                <input type="text" value="" class="form-control" style="width:215px!important; font-weight: normal">
            </label><br />

            <label class="setting" data-setting="description">
                <span class="name">Описание</span><br />
                <textarea class="form-control" style="height: 80px; width:215px!important; font-weight: normal"></textarea>
            </label>

            <div style="padding-top: 10px;">
                <button type="button" class="btn btn-success button-link save-attachment">Сохранить</button> &nbsp;
            </div>
        </div>
    </div>
</div>