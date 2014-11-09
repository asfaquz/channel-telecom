<?php
if ($data['rows'] < 0):
    echo Yii::t('strings', 'No results to display.');
else:
    foreach ($data['items'] as $unit):
        ?>
        <div class="row wd-200 fl-l pd-100">
            <?php
            echo $sImageHtml = CHtml::image($unit['images'][0]);
            ?>

        </div>

        <?php
    endforeach;

endif;
?>