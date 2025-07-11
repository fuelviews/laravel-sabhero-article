<?php

namespace Fuelviews\SabHeroArticle\Exceptions;

use Exception;

class CannotSendEmail extends Exception
{
    public static function postNotPublished(): CannotSendEmail
    {
        return new self('The post is not published.');
    }
}
