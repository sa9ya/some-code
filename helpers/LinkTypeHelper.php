<?php

namespace common\helpers;

class LinkTypeHelper
{
    /**
     * Constants of tone ids
     */
    const SEARCH_TYPE = 1;
    const HAND_ADDED_TYPE = 2;
    const RSS_TYPE = 3;
    const TELEGRAM_TYPE = 4;
    const TWITTER_TYPE = 5;
    const YOUTUBE_TYPE = 13;

    /**
     * Array of tone names and ids
     */
    const LINK_TYPE_NAME = array(
        self::SEARCH_TYPE => 'Поисковая выдача',
        self::HAND_ADDED_TYPE => 'Добавлена вручную',
        self::RSS_TYPE => 'RSS',
        self::TELEGRAM_TYPE => 'Telegram',
        self::TWITTER_TYPE => 'Twitter',
        self::YOUTUBE_TYPE => 'YouTube'
    );

    /**
     * Array of tone names and ids
     */
    const LINK_TYPE_ICON = array(
        self::SEARCH_TYPE => '<i class="fas fa-search"></i>',
        self::HAND_ADDED_TYPE => '<i class="fas fa-eye"></i>',
        self::RSS_TYPE => '<i class="fas fa-rss"></i>',
        self::TELEGRAM_TYPE => '<i class="fab fa-telegram"></i>',
        self::TWITTER_TYPE => '<i class="fab fa-twitter"></i>',
        self::YOUTUBE_TYPE => '<i class="fab fa-youtube"></i> '
    );

    /**
     * Function to return tone name
     *
     * @param $tone_id
     * @return string | null
     */
    public static function getTypeName($type_id) : ?string
    {
        return self::LINK_TYPE_NAME[$type_id] ?? null;
    }

    /**
     * Function to return tone icon
     *
     * @param $type_id
     * @return string | null
     */
    public static function getTypeIcon($type_id) : ?string
    {
        return self::LINK_TYPE_ICON[$type_id] ?? null;
    }
}