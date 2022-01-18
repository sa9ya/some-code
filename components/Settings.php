<?php

namespace backend\components;

use yii;
use backend\models\Settings as SettingsModel;
use yii\helpers\ArrayHelper;

 /**
  * With this component you can get all core settings from data base and use it in any place in project
  * for use this component just add code below in your config file
  *
  * ```php
  *
  *  'components' => [
  *      ...
  *      'settings' => [
  *          'class' => 'backend\components\Settings',
  *          'cache' => 'yii\caching\FileCache',
  *      ],
  *      ...
  *  ]
  *
  * ```
  */
class Settings
{
    /**
     * @var array $settings
     */
    private static $settings;

    /**
     * Construct of Settings component
     *
     * get all setting from database and store them in cache
     */
    public function __construct()
    {
        self::$settings = ArrayHelper::map(SettingsModel::find()->asArray()->all(), 'name', 'value');
    }

    /**
     * Set setting dynamically
     * For set setting you need to set setting name and setting value
     * The settings will apply if there is no data in the array with that name
     *
     * @param string $setting_name // key of array data
     * @param $setting_value       // value in array item
     *
     */
    public function setSetting(string $setting_name, $setting_value) : void
    {
        if($this->getSetting($setting_name) === null) {
            self::$settings[$setting_name] = $setting_value;
        }
    }

    /**
     * Get setting value from array
     *
     * @param string $setting_name // key of array data
     * @return mixed
     *
     */
    public function getSetting(string $setting_name)
    {
        return self::$settings[$setting_name] ?? null;
    }

    public function clearCacheSettings()
    {
        Yii::$app->cache->delete('settings');
    }
}