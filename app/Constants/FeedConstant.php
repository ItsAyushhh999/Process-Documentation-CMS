<?php

namespace App\Constants;

class FeedConstant
{
    //sources constant
    const FEED_SOURCE_GITHUB = 101;
    const FEED_SOURCE_AWS = 102;
    const FEED_SOURCE_TDMS = 103;

    public static array $FEED_SOURCES = [
        self::FEED_SOURCE_GITHUB => 'Git Hub',
        self::FEED_SOURCE_AWS => 'AWS',
        self::FEED_SOURCE_TDMS => 'TDMS',
    ];

    // status constant
    const FEED_STATUS_SUCCESS = 200;
    const FEED_STATUS_FAILURE = 201;
    const FEED_STATUS_REVERT = 202;
    const FEED_STATUS_CREATED = 203;
    const FEED_STATUS_CLOSE = 204;
    const FEED_STATUS_MERGE = 205;
    const FEED_STATUS_COMPLETED = 206;
    const FEED_STATUS_PROCESS = 207;
    const FEED_STATUS_REVIEWED = 208;
    const FEED_STATUS_SYNCHRONIZED = 209;

    const FEED_STATUS_OTHERS = 210;

    public static array $success = ['success', 'Succeeded'];
    public static array $InProgress = ['Superseded', 'InProgress'];
    public static array $Failed = ['Cancelled', 'Failed', 'Stopped', 'Stopping', 'Failed'];

    public static array $FEED_STATUS = [
        self::FEED_STATUS_SUCCESS => 'Success',
        self::FEED_STATUS_FAILURE => 'Failure',
        self::FEED_STATUS_REVERT => 'Revert',
        self::FEED_STATUS_CREATED => 'Created',
        self::FEED_STATUS_CLOSE => 'Close',
        self::FEED_STATUS_MERGE => 'Merge',
        self::FEED_STATUS_COMPLETED => 'Completed',
        self::FEED_STATUS_PROCESS => 'InProgress',
        self::FEED_STATUS_REVIEWED => 'Reviewed',
        self::FEED_STATUS_SYNCHRONIZED => 'Synchronized',
        self::FEED_STATUS_OTHERS => 'Others',
    ];

    // type constant
    const FEED_TYPE_PULL_REQUEST = 301;
    const FEED_TYPE_DEPLOYMENT = 302;

    public static array $FEED_TYPES = [
        self::FEED_TYPE_PULL_REQUEST => 'Pull Request',
        self::FEED_TYPE_DEPLOYMENT => 'Deployment',
    ];

    /**
     * Generates a comment string based on the provided array.
     *
     * @param array $array The array containing key-value pairs for the comment.
     * @return string|null The generated comment string, or null if the array is empty.
     */
    public static function makeComment(array $array = []): ?string
    {
        $comment = null;
        foreach ($array as $key => $value) {
            $comment .= " $key : $value ";
        }

        return $comment ?: null;
    }
}
