<?php

namespace Tustin\PlayStation\Enums;

enum DescriptionType: string
{
    case Short = 'SHORT';
    case Long = 'LONG';
    case CompatibilityNotice = 'COMPATIBILITY_NOTICE';
    case Legal = 'LEGAL';
}
