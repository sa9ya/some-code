<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $languages array */
/* @var $current_language string */
?>


<ceo-modal
        btnClassList="btn-icon"
        modalContentStyle="padding: 14px;border-radius:14px; width: max-content;"
>
    <div slot="button" style="position: relative;">
        <svg width="33" height="33" viewBox="0 0 33 33" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <path d="M22.9583 32H27.4792C28.6782 32 29.8281 31.5237 30.6759 30.6759C31.5237 29.8281 32 28.6782 32 27.4792V22.9584"
                  stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                  stroke-linejoin="round"></path>
            <path d="M32 10.0417V5.52083C32 4.32183 31.5237 3.17194 30.6759 2.32412C29.8281 1.4763 28.6782 1 27.4792 1H22.9583"
                  stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                  stroke-linejoin="round"></path>
            <path d="M10.0417 32H5.52083C4.32183 32 3.17194 31.5237 2.32412 30.6759C1.4763 29.8281 1 28.6782 1 27.4792V22.9584"
                  stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                  stroke-linejoin="round"></path>
            <path d="M1 10.0417V5.52083C1 4.32183 1.4763 3.17194 2.32412 2.32412C3.17194 1.4763 4.32183 1 5.52083 1H10.0417"
                  stroke="currentColor" stroke-width="1.2" stroke-linecap="round"
                  stroke-linejoin="round"></path>
        </svg>
        <span id="selected-lang"><?= strtoupper(Yii::t('app', $languages[$current_language])) ?></span>
    </div>


    <?php $form = ActiveForm::begin([
        'id' => 'language',
        'method' => 'POST',
        'action' => ['site/change-language']
    ]) ?>
    <ul class="select-lang">
        <?php foreach ($languages as $index => $language) { ?>
            <?php if ($index != $current_language) { ?>
                <li class="form-group">
                    <input type="radio" class="" name="language" value="<?=$index?>" onChange="this.form.submit()" id="language-<?=$index?>">
                    <label for="language-<?=$index?>"><?= Yii::t('app', $language) ?></label>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
    <input type="hidden" name="route" value="<?=\yii\helpers\Url::to([Yii::$app->controller->id.'/'.Yii::$app->controller->action->id])?>">
    <?php ActiveForm::end(); ?>
</ceo-modal>

<style>
    #language .form-group label:before,
    #language .form-group label:after {
        display: none;
    }
</style>
