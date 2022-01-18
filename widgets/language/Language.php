<?php
namespace backend\widgets\language;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class Language extends Widget
{
    public $languages = [
        'uk' => 'text_ukrainian',
        'en' => 'text_english',
        'ru' => 'text_russian'
    ];

    private $current_language;

    public function init()
    {
        $this->current_language = Yii::$app->language;
    }

    public function run() {
        if(count($this->languages) > 1) {
            return $this->render('view', [
                'languages' => $this->languages,
                'current_language' => $this->current_language
            ]);
        }
    }
}